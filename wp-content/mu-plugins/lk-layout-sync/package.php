<?php
/** Portable package format. No WordPress bootstrap or database writes here. */
final class LK_Layout_Package {
 const META = array('_elementor_data', '_elementor_page_settings', '_elementor_edit_mode', '_wp_page_template', '_elementor_template_type');
 static function canonical($value) {
  if (is_array($value)) {
   if (array_keys($value) !== range(0, count($value) - 1)) ksort($value);
   foreach ($value as &$item) $item = self::canonical($item);
  }
  return $value;
 }
 static function json($value) {
  // PHP hosts can serialize the same float differently (e.g. 0.7 versus
  // 0.69999999999999996). A portable checksum must not depend on php.ini.
  // Keep the host's setting intact outside this one encoding operation.
  $precision = ini_get('serialize_precision');
  $changed = $precision !== '-1';
  if ($changed && (!function_exists('ini_set') || ini_set('serialize_precision', '-1') === false)) throw new RuntimeException('Deterministic JSON encoding requires serialize_precision=-1.');
  try { return json_encode(self::canonical($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR); }
  finally { if ($changed) ini_set('serialize_precision', $precision); }
 }
 static function hash($value) { return hash('sha256', self::json($value)); }
 static function value_hash($key, $value) {
  if ($key === '_elementor_data' && is_string($value)) $value = json_decode($value ?: '[]', true, 512, JSON_THROW_ON_ERROR);
  return self::hash($value);
 }
 static function snapshot_hash($key, $snapshot) {
  return self::hash(array('exists' => (bool) $snapshot['exists'], 'value' => $snapshot['exists'] ? self::value_hash($key, $snapshot['value']) : null));
 }
 static function asset_filename($package, $asset) { return $asset['file'] . ($package['version'] === 2 ? '.gz' : ''); }
 static function asset_source($package, $directory, $asset) {
  $path = $directory . '/assets/' . self::asset_filename($package, $asset);
  return $package['version'] === 2 ? 'compress.zlib://' . str_replace('\\', '/', $path) : $path;
 }
 static function validate_asset($package, $directory, $key, $asset) {
  $file = $directory . '/assets/' . self::asset_filename($package, $asset);
  if (!is_file($file)) throw new RuntimeException('Missing or modified layout asset: ' . $key);
  if ($package['version'] === 1) {
   if (!hash_equals($key, hash_file('sha256', $file))) throw new RuntimeException('Missing or modified layout asset: ' . $key);
   return;
  }
  if (!is_int($asset['bytes'] ?? null) || $asset['bytes'] < 1 || !preg_match('/^[a-f0-9]{64}$/D', $asset['payload_sha256'] ?? '')) throw new RuntimeException('Invalid compressed layout asset metadata.');
  if (!hash_equals($asset['payload_sha256'], hash_file('sha256', $file)) || file_get_contents($file, false, null, 0, 2) !== "\x1f\x8b") throw new RuntimeException('Missing or modified layout asset: ' . $key);
  if (!function_exists('gzopen')) throw new RuntimeException('Gzip support is required for layout assets.');
  $stream = @gzopen($file, 'rb');
  if (!$stream) throw new RuntimeException('Cannot decompress layout asset: ' . $key);
  try {
   $hash = hash_init('sha256');
   $bytes = hash_update_stream($hash, $stream, $asset['bytes'] + 1);
   if ($bytes !== $asset['bytes'] || !hash_equals($key, hash_final($hash))) throw new RuntimeException('Missing or modified layout asset: ' . $key);
  } finally { gzclose($stream); }
 }
 static function public_error($message) {
  // Only fixed codes and validated logical identifiers are public. Never echo
  // a database/PHP exception, server path, credential, document body or backup.
  $codes = array(
   'Layout package checksum mismatch.' => 'package_checksum',
   'Layout allowlist does not match the package.' => 'layout_allowlist',
   'Layout deployment environment does not match this website.' => 'environment_mismatch',
   'Transactional InnoDB tables are required for layout sync.' => 'transaction_support',
   'Gzip support is required for layout assets.' => 'gzip_unavailable',
   'Cannot save the layout baseline.' => 'baseline_write',
   'Cannot publish the layout deployment status.' => 'status_write',
  );
  if (isset($codes[$message])) return array('error_code' => $codes[$message]);
  if (preg_match('/^(?:Missing or modified layout asset|Cannot decompress layout asset): ([a-f0-9]{64})$/D', $message, $match)) return array('error_code' => 'asset_integrity', 'asset' => $match[1]);
  if (preg_match('/^(?:Missing or unexpected layout document|Layout document mapping changed): ([a-z0-9-]{1,64})$/D', $message, $match)) return array('error_code' => 'document_mapping', 'document' => $match[1]);
  if (preg_match('/^Layout conflict: ([a-z0-9-]{1,64}) \/ (_[a-z0-9_]+)\./', $message, $match) && in_array($match[2], self::META, true)) return array('error_code' => 'layout_conflict', 'document' => $match[1], 'meta' => $match[2]);
  return array('error_code' => 'layout_import_failed');
 }
 static function validate($package, $policy, $directory) {
  if (!in_array($package['version'] ?? 0, array(1, 2), true) || !is_array($package['documents'] ?? null) || !is_array($package['assets'] ?? null) || !is_array($policy['documents'] ?? null)) throw new RuntimeException('Unsupported layout package format.');
  $payload = $package; unset($payload['release']);
  if (!hash_equals(self::hash($payload), $package['release'] ?? '')) throw new RuntimeException('Layout package checksum mismatch.');
  if (array_keys($package['documents']) !== array_keys($policy['documents'])) {
   $a = array_keys($package['documents']); $b = array_keys($policy['documents']); sort($a); sort($b);
   if ($a !== $b) throw new RuntimeException('Layout allowlist does not match the package.');
  }
  foreach ($package['documents'] as $key => $doc) {
   $scope = $policy['documents'][$key] ?? array();
   if (!in_array($scope['type'] ?? '', array('page', 'elementor_library'), true) || $doc['type'] !== $scope['type']) throw new RuntimeException('Protected content type refused: ' . $key);
   if (!is_array($doc['meta'] ?? null)) throw new RuntimeException('Invalid document metadata.');
   foreach ($doc['meta'] as $name => $value) if (!in_array($name, self::META, true)) throw new RuntimeException('Forbidden metadata: ' . $name);
  }
  foreach ($package['assets'] as $key => $asset) {
   if (!preg_match('/^[a-f0-9]{64}$/D', $key) || !preg_match('/^[a-f0-9]{64}\.(?:png|jpe?g|webp|gif|avif|svg)$/D', $asset['file'] ?? '') || strpos($asset['file'], $key . '.') !== 0) throw new RuntimeException('Invalid asset filename.');
   self::validate_asset($package, $directory, $key, $asset);
  }
 }
 static function validate_ready($ready, $package, $policy, $home) {
  $environment = $ready['environment'] ?? '';
  if (($ready['version'] ?? 0) !== 1 || !in_array($environment, array('master', 'prod'), true) || !preg_match('/^[a-f0-9]{40}$/D', $ready['commit'] ?? '')) throw new RuntimeException('Invalid layout release marker.');
  $expected = $policy[$environment === 'prod' ? 'production_origin' : 'master_origin'];
  if (($ready['release'] ?? '') !== $package['release'] || rtrim($home, '/') !== rtrim($expected, '/') || rtrim($home, '/') !== rtrim($ready['origin'] ?? '', '/')) throw new RuntimeException('Layout deployment environment does not match this website.');
 }
 static function replace($value, $strings) {
  if (is_array($value)) { foreach ($value as &$item) $item = self::replace($item, $strings); return $value; }
  if (!is_string($value)) return $value;
  if (isset($strings[$value]) && is_int($strings[$value])) return $strings[$value];
  return strtr($value, $strings);
 }
}
