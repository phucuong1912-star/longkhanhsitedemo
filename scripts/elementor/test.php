<?php
/** Offline integration tests of the importer using an isolated destination DB adapter. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require_once dirname(__DIR__, 2) . '/wp-content/mu-plugins/lk-layout-sync/engine.php';

function expect($condition, $message) { if (!$condition) throw new RuntimeException($message); }
function rejects($callback, $fragment) {
 try { $callback(); } catch (Throwable $error) {
  expect(strpos($error->getMessage(), $fragment) !== false, 'Unexpected rejection: ' . $error->getMessage()); return;
 }
 throw new RuntimeException('Expected rejection: ' . $fragment);
}
function seal($package) { unset($package['release']); $package['release'] = LK_Layout_Package::hash($package); return $package; }

final class Layout_Test_Store {
 public $ids = array('header' => 4101, 'blog' => 4907);
 public $types = array(4101 => 'elementor_library', 4907 => 'page', 12 => 'post');
 public $meta = array(4101 => array('_elementor_data' => '[{"old":"header"}]', '_wp_page_template' => 'default'), 4907 => array('_elementor_data' => '[{"old":"blog"}]'));
 public $data = array(); public $backups = array(); public $media = array(); public $protected;
 public $writes = 0; public $fail_at = 0; public $locked = false; public $on_asset;
 public $fail_cache = false; public $invalidations = 0;
 private $transaction;
 function __construct() {
  $this->protected = array('posts' => array(12 => array('title' => 'Existing production article', 'content' => 'Production-only body')),
   'listings' => array(44 => array('address' => 'Production address')), 'comments' => array(9 => 'A real review'),
   'users' => array(8 => 'Existing owner'), 'uploads' => array('old.jpg' => 'original bytes'));
 }
 function home() { return 'https://destination.example'; }
 function site() { return $this->home(); }
 function resolve($key, $scope) {
  $id = $this->ids[$key] ?? 0;
  if (!$id || ($this->types[$id] ?? '') !== $scope['type']) throw new RuntimeException('Missing or unexpected layout document: ' . $key);
  return $id;
 }
 function state() { return $this->data; }
 function save_state($value) { $this->data = $value; }
 function read($id, $key) { return array('exists' => array_key_exists($key, $this->meta[$id] ?? array()), 'value' => $this->meta[$id][$key] ?? ''); }
 function guard($id, $key) {
  expect(in_array($id, $this->ids, true) && in_array($this->types[$id], array('page', 'elementor_library'), true) && in_array($key, LK_Layout_Package::META, true), 'Protected write attempted');
  if (++$this->writes === $this->fail_at) throw new RuntimeException('Injected database failure');
 }
 function write($id, $key, $value) { $this->guard($id, $key); $this->meta[$id][$key] = $value; }
 function remove($id, $key) { $this->guard($id, $key); unset($this->meta[$id][$key]); }
 function lock() { if ($this->locked) return false; $this->locked = true; return true; }
 function unlock() { $this->locked = false; }
 function begin($changes) { $this->transaction = array($this->meta, $this->data); }
 function commit() { $this->transaction = null; }
 function abort() { list($this->meta, $this->data) = $this->transaction; $this->transaction = null; }
 function backup($data) { $id = 'backup-' . (count($this->backups) + 1); $this->backups[$id] = $data; return $id; }
 function load_backup($id) { return $this->backups[$id]; }
 function invalidate($ids) { if ($this->fail_cache) throw new RuntimeException('Injected cache failure'); ++$this->invalidations; }
 function asset($key, $asset, $source) {
  expect(hash_file('sha256', $source) === $key, 'Importer received incorrect image bytes');
  if ($this->on_asset) { $callback = $this->on_asset; $this->on_asset = null; $callback($this); }
  if (!isset($this->media[$key])) $this->media[$key] = array('id' => 80001 + count($this->media), 'url' => $this->home() . '/wp-content/uploads/lk-layouts/' . $asset['file']);
  return $this->media[$key];
 }
}

$directory = sys_get_temp_dir() . '/lk-layout-test-' . bin2hex(random_bytes(6));
mkdir($directory . '/assets', 0755, true);
$bytes = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
$sha = hash('sha256', $bytes); $image = $directory . '/assets/' . $sha . '.png'; file_put_contents($image, $bytes);
$policy = array('master_origin' => 'https://master.example', 'production_origin' => 'https://destination.example', 'documents' => array(
 'header' => array('type' => 'elementor_library'), 'blog' => array('type' => 'page')));
$package = seal(array('version' => 1, 'documents' => array(
 'header' => array('type' => 'elementor_library', 'meta' => array('_elementor_data' => array(array('image' => array('id' => '{{attachment:' . $sha . '}}', 'url' => '{{asset:' . $sha . '}}'), 'url' => '{{home}}/blog/', 'template_id' => '{{document:header}}')), '_wp_page_template' => 'elementor_canvas')),
 'blog' => array('type' => 'page', 'meta' => array('_elementor_data' => array(array('widgetType' => 'posts', 'text' => 'Dynamic blog')), '_elementor_edit_mode' => 'builder'))),
 'assets' => array($sha => array('file' => $sha . '.png', 'title' => 'Logo', 'alt' => 'Owner logo', 'width' => 1, 'height' => 1))));
$gzip_bytes = gzencode($bytes, 9); $gzip_file = $image . '.gz'; file_put_contents($gzip_file, $gzip_bytes);
$gzip_package = $package; $gzip_package['version'] = 2;
$gzip_package['assets'][$sha]['bytes'] = strlen($bytes);
$gzip_package['assets'][$sha]['payload_sha256'] = hash('sha256', $gzip_bytes);
$gzip_package = seal($gzip_package);
$new = function () use ($policy, $directory) { $store = new Layout_Test_Store(); return array($store, new LK_Layout_Engine($store, $policy, $directory)); };
$tests = array();
$tests['gzip transport verifies original image bytes and imports through the same safe engine'] = function () use ($new, $gzip_package, $directory, $sha, $bytes) {
 list($store, $engine) = $new(); $before = $store->protected;
 $engine->apply($gzip_package);
 $source = LK_Layout_Package::asset_source($gzip_package, $directory, $gzip_package['assets'][$sha]);
 $output = $directory . '/decompressed.png';
 try { expect(copy($source, $output) && file_get_contents($output) === $bytes, 'Copy did not restore the exact original image'); }
 finally { if (is_file($output)) unlink($output); }
 expect($store->protected === $before && count($store->media) === 1, 'Gzip import changed protected content');
};
$tests['rewritten PNG files cannot corrupt the compressed transport'] = function () use ($new, $gzip_package, $image, $bytes) {
 list($store, $engine) = $new(); file_put_contents($image, 'An upload layer rewrote the legacy PNG');
 try { $engine->apply($gzip_package); expect(count($store->media) === 1, 'Importer used the rewritten PNG instead of gzip'); }
 finally { file_put_contents($image, $bytes); }
};
$tests['modified gzip payload is refused before any layout writes'] = function () use ($new, $gzip_package, $gzip_file, $gzip_bytes) {
 list($store, $engine) = $new(); file_put_contents($gzip_file, gzencode('A different image'));
 try { rejects(function () use ($engine, $gzip_package) { $engine->apply($gzip_package); }, 'modified layout asset'); }
 finally { file_put_contents($gzip_file, $gzip_bytes); }
 expect(!$store->backups && !$store->writes && !$store->media, 'Corrupt transport changed the destination');
};
$tests['a matching compressed checksum cannot disguise different original image bytes'] = function () use ($new, $gzip_package, $gzip_file, $gzip_bytes, $sha) {
 list($store, $engine) = $new(); $different = gzencode('A different image'); file_put_contents($gzip_file, $different);
 $bad = $gzip_package; $bad['assets'][$sha]['payload_sha256'] = hash('sha256', $different); $bad['assets'][$sha]['bytes'] = strlen('A different image');
 try { rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'modified layout asset'); }
 finally { file_put_contents($gzip_file, $gzip_bytes); }
 expect(!$store->writes && !$store->backups, 'Incorrect original image passed integrity checks');
};
$tests['gzip decompression is bounded by the declared original length'] = function () use ($new, $gzip_package, $sha) {
 list($store, $engine) = $new(); $bad = $gzip_package; $bad['assets'][$sha]['bytes'] = 1;
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'modified layout asset');
 expect(!$store->writes && !$store->backups, 'An oversized decompressed image reached metadata writes');
};
$tests['missing compressed files and invalid gzip headers are refused'] = function () use ($new, $gzip_package, $gzip_file, $gzip_bytes, $sha) {
 list($store, $engine) = $new(); $bad = $gzip_package; $bad['assets'][$sha]['file'] = $sha . '.webp';
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'modified layout asset');
 $bad = $gzip_package; file_put_contents($gzip_file, 'not a gzip archive'); $bad['assets'][$sha]['payload_sha256'] = hash('sha256', 'not a gzip archive');
 try { rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'modified layout asset'); }
 finally { file_put_contents($gzip_file, $gzip_bytes); }
};
$tests['public error details expose identifiers but never private exception text'] = function () use ($sha) {
 expect(LK_Layout_Package::public_error('Missing or modified layout asset: ' . $sha) === array('error_code' => 'asset_integrity', 'asset' => $sha), 'Asset diagnostic missing');
 expect(LK_Layout_Package::public_error('Missing or unexpected layout document: header') === array('error_code' => 'document_mapping', 'document' => 'header'), 'Mapping diagnostic missing');
 $private = LK_Layout_Package::json(LK_Layout_Package::public_error('SQL error: password=SECRET; /private/database.sql; post body'));
 expect(strpos($private, 'SECRET') === false && strpos($private, 'database.sql') === false && strpos($private, 'post body') === false, 'Public status leaked exception details');
};
$tests['portable JSON hashes ignore host float precision and preserve its setting'] = function () {
 $original = ini_get('serialize_precision'); $value = array('opacity' => 0.7, 'small' => 0.0000001, 'nested' => array(0.9, 1.23456789012345));
 try {
  ini_set('serialize_precision', '-1'); $expected = LK_Layout_Package::hash($value);
  foreach (array('17', '100', '14', '-1') as $precision) {
   ini_set('serialize_precision', $precision);
   expect(LK_Layout_Package::hash($value) === $expected, 'Checksum depends on PHP serialize_precision=' . $precision);
   expect(ini_get('serialize_precision') === $precision, 'Encoding leaked an INI change into WordPress');
  }
 } finally { ini_set('serialize_precision', $original); }
};
$tests['JSON encoding failures also restore the host precision'] = function () {
 $original = ini_get('serialize_precision');
 try {
  ini_set('serialize_precision', '17');
  rejects(function () { LK_Layout_Package::json(array(INF)); }, 'Inf and NaN');
  expect(ini_get('serialize_precision') === '17', 'A failed JSON encoding leaked its INI setting');
 } finally { ini_set('serialize_precision', $original); }
};
$tests['portable IDs, URLs, first backup, article preservation and idempotence'] = function () use ($new, $package, $sha) {
 list($store, $engine) = $new(); $before = $store->protected; $initial = $store->meta;
 $plan = $engine->plan($package); expect($store->meta === $initial && !$store->backups, 'A dry run wrote data');
 expect($plan['ids']['header'] === 4101, 'Destination mapping was not used');
 $state = $engine->apply($package); $nodes = json_decode($store->meta[4101]['_elementor_data'], true);
 expect($nodes[0]['image']['id'] === 80001 && $nodes[0]['template_id'] === 4101, 'Numeric destination IDs not substituted');
 expect($nodes[0]['url'] === 'https://destination.example/blog/', 'Home URL not substituted');
 expect($nodes[0]['image']['url'] === $store->media[$sha]['url'], 'Image URL not substituted');
 expect($store->protected === $before, 'Production content changed');
 expect($store->backups[$state['backup']]['items'][0]['before']['exists'], 'Original metadata backup missing');
 $writes = $store->writes; $engine->apply($package);
 expect($store->writes === $writes && count($store->backups) === 1, 'Same release was applied twice');
};
$tests['new articles and reviews written during deployment survive'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $store->on_asset = function ($target) {
  $target->protected['posts'][999] = array('title' => 'Just published', 'content' => 'A new production post');
  $target->protected['comments'][1000] = 'A new review';
 }; $engine->apply($package);
 expect(isset($store->protected['posts'][999], $store->protected['comments'][1000]), 'Concurrent production content was lost');
};
$tests['local changes apply but unchanged fields preserve destination edits'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $engine->apply($package);
 $store->meta[4907]['_elementor_edit_mode'] = 'target-editor-choice';
 $next = $package; $next['documents']['header']['meta']['_wp_page_template'] = 'new-template'; $next = seal($next);
 $engine->apply($next);
 expect($store->meta[4101]['_wp_page_template'] === 'new-template', 'Changed local field missing');
 expect($store->meta[4907]['_elementor_edit_mode'] === 'target-editor-choice', 'Unchanged local field overwrote destination');
 $conflict = $next; $conflict['documents']['blog']['meta']['_elementor_edit_mode'] = 'new-local-choice';
 $before = $store->meta; $backups = count($store->backups);
 rejects(function () use ($engine, $conflict) { $engine->apply(seal($conflict)); }, 'Layout conflict');
 expect($store->meta === $before && count($store->backups) === $backups, 'Conflict partially applied or created an import backup');
};
$tests['metadata deletion is scoped and rollback restores it'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $engine->apply($package); $before = $store->meta;
 $next = $package; unset($next['documents']['header']['meta']['_wp_page_template']); $next = seal($next);
 $state = $engine->apply($next); expect(!isset($store->meta[4101]['_wp_page_template']), 'Local metadata deletion did not propagate');
 $engine->rollback($state['backup']); expect($store->meta === $before, 'Rollback did not restore deleted metadata');
 rejects(function () use ($engine, $next) { $engine->apply($next); }, 'rolled back');
};
$tests['transaction failure rolls back every metadata change and state'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $before = $store->meta; $content = $store->protected; $store->fail_at = 3;
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'Injected database failure');
 expect($store->meta === $before && $store->data === array() && $store->protected === $content, 'Failed transaction left partial data');
 expect(!$store->locked && count($store->backups) === 1, 'Failure did not unlock or retain the metadata backup');
 $store->fail_at = 0; $engine->apply($package); expect($store->data['release'] === $package['release'], 'Retry failed');
};
$tests['cache failure after commit can be retried without importing content again'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $store->fail_cache = true;
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'Injected cache failure');
 expect($store->data['release'] === $package['release'], 'Committed metadata state was lost after a cache failure');
 $writes = $store->writes; $store->fail_cache = false; $engine->refresh($package);
 expect($store->writes === $writes && $store->invalidations === 1 && count($store->backups) === 1, 'Cache retry repeated the metadata import');
};
$tests['an editor saving during media staging prevents all layout writes'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $original_blog = $store->meta[4907];
 $store->on_asset = function ($target) { $target->meta[4101]['_elementor_data'] = '[{"text":"Saved during deploy"}]'; };
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'changed while');
 expect($store->meta[4101]['_elementor_data'] === '[{"text":"Saved during deploy"}]' && $store->meta[4907] === $original_blog, 'Concurrent layout edit was overwritten');
};
$tests['rollback refuses a stale backup and preserves later edits'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $first = $engine->apply($package);
 $next = $package; $next['documents']['header']['meta']['_wp_page_template'] = 'second'; $next = seal($next); $last = $engine->apply($next);
 rejects(function () use ($engine, $first) { $engine->rollback($first['backup']); }, 'most recent');
 $store->meta[4101]['_wp_page_template'] = 'later-manual-edit'; $before = $store->meta;
 rejects(function () use ($engine, $last) { $engine->rollback($last['backup']); }, 'changed since');
 expect($store->meta === $before, 'Rollback overwrote later destination edits');
};
$tests['multiple rollbacks keep every reverted release blocked'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $first = $engine->apply($package);
 $next = $package; $next['documents']['header']['meta']['_wp_page_template'] = 'second'; $next = seal($next);
 $last = $engine->apply($next); $engine->rollback($last['backup']); $engine->rollback($first['backup']);
 rejects(function () use ($engine, $next) { $engine->apply($next); }, 'rolled back');
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'rolled back');
};
$tests['allowlist excludes articles, non-layout metadata and duplicate mappings'] = function () use ($new, $package, $policy, $directory) {
 list($store, $engine) = $new(); $bad = $package; $bad['documents']['blog']['type'] = 'post';
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'Protected content type');
 $bad = $package; $bad['documents']['blog']['meta']['_lk_rating'] = '5';
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'Forbidden metadata');
 $store->ids['blog'] = 12;
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'unexpected layout document');
 expect(!$store->backups && !$store->writes, 'Rejected package wrote data');
 $duplicate = $package; $duplicate['documents']['blog']['type'] = 'elementor_library';
 $duplicate_policy = $policy; $duplicate_policy['documents']['blog']['type'] = 'elementor_library';
 $store->ids['blog'] = $store->ids['header'];
 $duplicate_engine = new LK_Layout_Engine($store, $duplicate_policy, $directory);
 rejects(function () use ($duplicate_engine, $duplicate) { $duplicate_engine->apply(seal($duplicate)); }, 'same document');
};
$tests['destination document replacement cannot reuse a previous baseline'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $engine->apply($package); $store->ids['blog'] = 4999; $store->types[4999] = 'page';
 $next = $package; $next['documents']['blog']['meta']['_elementor_edit_mode'] = 'new';
 rejects(function () use ($engine, $next) { $engine->apply(seal($next)); }, 'mapping changed');
};
$tests['checksums and path traversal are rejected before mutation'] = function () use ($new, $package, $image, $bytes, $sha) {
 list($store, $engine) = $new(); $bad = $package; $bad['release'] = str_repeat('0', 64);
 rejects(function () use ($engine, $bad) { $engine->apply($bad); }, 'checksum');
 $bad = $package; $bad['assets'][$sha]['file'] = '../outside.png';
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'Invalid asset filename');
 file_put_contents($image, 'corrupt');
 try { rejects(function () use ($engine, $package) { $engine->apply($package); }, 'modified layout asset'); }
 finally { file_put_contents($image, $bytes); }
 expect(!$store->writes && !$store->backups, 'Corrupt package mutated the destination');
};
$tests['unresolved references roll back and a concurrent import is refused'] = function () use ($new, $package) {
 list($store, $engine) = $new(); $bad = $package; $bad['documents']['header']['meta']['_elementor_data'][0]['template_id'] = '{{document:unknown}}'; $before = $store->meta;
 rejects(function () use ($engine, $bad) { $engine->apply(seal($bad)); }, 'Unresolved');
 expect($store->meta === $before, 'Unresolved reference left partial metadata');
 $store->locked = true;
 rejects(function () use ($engine, $package) { $engine->apply($package); }, 'Another layout import');
};
$tests['release marker accepts only the matching branch, origin, SHA and package'] = function () use ($policy, $package) {
 $ready = array('version' => 1, 'environment' => 'prod', 'origin' => 'https://destination.example', 'commit' => str_repeat('a', 40), 'release' => $package['release']);
 LK_Layout_Package::validate_ready($ready, $package, $policy, 'https://destination.example');
 foreach (array('environment' => 'master', 'origin' => 'https://other.example', 'commit' => 'short', 'release' => str_repeat('b', 64)) as $key => $value) {
  $bad = $ready; $bad[$key] = $value;
  rejects(function () use ($bad, $package, $policy) { LK_Layout_Package::validate_ready($bad, $package, $policy, 'https://destination.example'); }, $key === 'commit' ? 'Invalid' : 'environment');
 }
};

$failed = 0;
try {
 foreach ($tests as $name => $test) {
  try { $test(); echo "PASS $name\n"; }
  catch (Throwable $error) { ++$failed; fwrite(STDERR, "FAIL $name: {$error->getMessage()}\n"); }
 }
} finally { unlink($image); unlink($gzip_file); rmdir($directory . '/assets'); rmdir($directory); }
echo (count($tests) - $failed) . '/' . count($tests) . " layout tests passed.\n";
exit($failed ? 1 : 0);
