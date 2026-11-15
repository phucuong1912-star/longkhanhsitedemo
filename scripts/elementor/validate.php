<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$root = dirname(__DIR__, 2);
require_once $root . '/wp-content/mu-plugins/lk-layout-sync/package.php';
try {
 $directory = $root . '/wp-content/lk-layouts';
 $package = json_decode(file_get_contents($directory . '/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
 $policy = json_decode(file_get_contents($root . '/wp-content/mu-plugins/lk-layout-sync/scopes.json'), true, 512, JSON_THROW_ON_ERROR);
 LK_Layout_Package::validate($package, $policy, $directory);
 if (preg_match('~https?://(?:localhost|127\.0\.0\.1|\[::1\])\b~i', LK_Layout_Package::json($package))) throw new RuntimeException('A localhost URL escaped the exporter.');
 if (in_array('--git', $argv, true)) {
  $paths = array('wp-content/lk-layouts/manifest.json', 'wp-content/mu-plugins/lk-layout-sync/scopes.json');
  foreach ($package['assets'] as $asset) $paths[] = 'wp-content/lk-layouts/assets/' . LK_Layout_Package::asset_filename($package, $asset);
  foreach ($paths as $path) {
   $process = proc_open(array('git', 'show', 'HEAD:' . $path), array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes, $root);
   if (!is_resource($process)) throw new RuntimeException('Cannot verify the Git package.');
   $committed = stream_get_contents($pipes[1]); $error = stream_get_contents($pipes[2]); fclose($pipes[1]); fclose($pipes[2]);
   if (proc_close($process) !== 0 || $committed !== file_get_contents($root . '/' . $path)) throw new RuntimeException('Commit the complete Elementor package before pushing: ' . $path);
  }
 }
 echo 'Layout package valid: ' . count($package['documents']) . ' documents, ' . count($package['assets']) . " images, 0 articles.\n";
} catch (Throwable $error) { fwrite(STDERR, $error->getMessage() . "\n"); exit(1); }
