<?php
require_once __DIR__ . '/package.php';
final class LK_Layout_Busy extends RuntimeException {}

/** The store adapter limits mutations to allowlisted layout metadata. */
final class LK_Layout_Engine {
 private $store; private $policy; private $directory;
 function __construct($store, $policy, $directory) { $this->store = $store; $this->policy = $policy; $this->directory = $directory; }
 function refresh($package) { $plan = $this->plan($package); $this->store->invalidate(array_values($plan['ids'])); }
 function plan($package) {
  LK_Layout_Package::validate($package, $this->policy, $this->directory);
  $state = $this->store->state(); $ids = array(); $changes = array();
  foreach ($package['documents'] as $key => $doc) {
   $id = $this->store->resolve($key, $this->policy['documents'][$key]);
   if (isset($ids[$id])) throw new RuntimeException('Two layout keys resolve to the same document.');
   $ids[$id] = $key;
   foreach (LK_Layout_Package::META as $name) {
    $exists = array_key_exists($name, $doc['meta']); $value = $exists ? $doc['meta'][$name] : null;
    $source = LK_Layout_Package::hash(array('exists' => $exists, 'value' => $value));
    $previous = $state['documents'][$key][$name] ?? null;
    if ($previous && $previous['id'] !== $id) throw new RuntimeException('Layout document mapping changed: ' . $key);
    // An unchanged local field never overwrites edits made on the destination.
    if ($previous && $previous['source'] === $source) continue;
    $current = $this->store->read($id, $name);
    if ($previous && $previous['target'] !== LK_Layout_Package::snapshot_hash($name, $current)) throw new RuntimeException('Layout conflict: ' . $key . ' / ' . $name . '. Resolve it locally before deploying.');
    $changes[] = array('key' => $key, 'id' => $id, 'meta' => $name, 'source' => $source, 'exists' => $exists, 'value' => $value, 'before' => $current);
   }
  }
  return array('ids' => array_flip($ids), 'changes' => $changes, 'state' => $state);
 }
 function apply($package) {
  if (!$this->store->lock()) throw new LK_Layout_Busy('Another layout import is running.');
  try {
   $plan = $this->plan($package); $state = $plan['state'];
   if (in_array($package['release'], $state['blocked_releases'] ?? array(), true)) throw new RuntimeException('This layout release was rolled back. Export a new revision locally.');
   if (($state['release'] ?? '') === $package['release']) return $state;
   $strings = array('{{home}}' => $this->store->home(), '{{site}}' => $this->store->site());
   foreach ($plan['ids'] as $key => $id) $strings['{{document:' . $key . '}}'] = $id;
   if ($plan['changes']) foreach ($package['assets'] as $key => $asset) {
    $media = $this->store->asset($key, $asset, $this->directory . '/assets/' . $asset['file']);
    $strings['{{attachment:' . $key . '}}'] = $media['id'];
    $strings['{{asset:' . $key . '}}'] = $media['url'];
   }
   $backup = $this->store->backup(array('release' => $package['release'], 'items' => $plan['changes'], 'state' => $state));
   $this->store->begin($plan['changes']);
   try {
    // Recheck after row locks: an editor saving during media staging must win.
    foreach ($plan['changes'] as $change) if (LK_Layout_Package::snapshot_hash($change['meta'], $this->store->read($change['id'], $change['meta'])) !== LK_Layout_Package::snapshot_hash($change['meta'], $change['before'])) throw new RuntimeException('Layout changed while the deployment was preparing; retry after resolving it locally.');
    foreach ($plan['changes'] as $change) {
     $value = LK_Layout_Package::replace($change['value'], $strings);
     if (strpos(LK_Layout_Package::json($value), '{{') !== false && preg_match('/\{\{(?:asset|attachment|document|home|site)\b/', LK_Layout_Package::json($value))) throw new RuntimeException('Unresolved layout reference.');
     if ($change['meta'] === '_elementor_data') $value = LK_Layout_Package::json($value);
     if ($change['exists']) $this->store->write($change['id'], $change['meta'], $value);
     else $this->store->remove($change['id'], $change['meta']);
     $state['documents'][$change['key']][$change['meta']] = array('id' => $change['id'], 'source' => $change['source'], 'target' => LK_Layout_Package::snapshot_hash($change['meta'], array('exists' => $change['exists'], 'value' => $value)));
    }
    $state['release'] = $package['release']; $state['backup'] = $backup;
    $this->store->save_state($state); $this->store->commit();
   } catch (Throwable $error) { $this->store->abort(); throw $error; }
   $this->store->invalidate(array_values($plan['ids']));
   return $state;
  } finally { $this->store->unlock(); }
 }
 function rollback($backup_id) {
  if (!$this->store->lock()) throw new LK_Layout_Busy('Another layout import is running.');
  try {
   $backup = $this->store->load_backup($backup_id); $current = $this->store->state();
   if (($current['backup'] ?? '') !== $backup_id) throw new RuntimeException('Only the most recent layout backup can be rolled back.');
   // Resolve IDs before taking row locks; never restore by a source numeric ID.
   foreach ($backup['items'] as $item) {
    $id = $this->store->resolve($item['key'], $this->policy['documents'][$item['key']]);
    if ($id !== $item['id']) throw new RuntimeException('Layout mapping changed since deployment; automatic rollback refused.');
   }
   $this->store->begin($backup['items']);
   try {
    foreach ($backup['items'] as $item) if (LK_Layout_Package::snapshot_hash($item['meta'], $this->store->read($item['id'], $item['meta'])) !== $current['documents'][$item['key']][$item['meta']]['target']) throw new RuntimeException('Layout changed since deployment; automatic rollback refused.');
    foreach ($backup['items'] as $item) {
     if ($item['before']['exists']) $this->store->write($item['id'], $item['meta'], $item['before']['value']);
     else $this->store->remove($item['id'], $item['meta']);
    }
    $state = $backup['state']; $state['blocked_releases'] = array_values(array_unique(array_merge($state['blocked_releases'] ?? array(), $current['blocked_releases'] ?? array(), array($current['release']))));
    $this->store->save_state($state); $this->store->commit();
   } catch (Throwable $error) { $this->store->abort(); throw $error; }
   $this->store->invalidate(array_unique(array_column($backup['items'], 'id')));
   return $state;
  } finally { $this->store->unlock(); }
 }
}
