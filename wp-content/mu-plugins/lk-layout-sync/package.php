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
 static function json($value) { return json_encode(self::canonical($value), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR); }
 static function hash($value) { return hash('sha256', self::json($value)); }
 static function value_hash($key, $value) {
  if ($key === '_elementor_data' && is_string($value)) $value = json_decode($value ?: '[]', true, 512, JSON_THROW_ON_ERROR);
  return self::hash($value);
 }
 static function snapshot_hash($key, $snapshot) {
  return self::hash(array('exists' => (bool) $snapshot['exists'], 'value' => $snapshot['exists'] ? self::value_hash($key, $snapshot['value']) : null));
 }
 static function validate($package, $policy, $directory) {
  if (($package['version'] ?? 0) !== 1 || !is_array($package['documents'] ?? null) || !is_array($package['assets'] ?? null) || !is_array($policy['documents'] ?? null)) throw new RuntimeException('Unsupported layout package format.');
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
   $file = $directory . '/assets/' . $asset['file'];
   if (!is_file($file) || !hash_equals($key, hash_file('sha256', $file))) throw new RuntimeException('Missing or modified layout asset: ' . $key);
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
