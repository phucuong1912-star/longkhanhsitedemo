<?php
/** Called only by GitHub Actions after validating the committed package. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
$options = getopt('', array('environment:', 'commit:', 'output:', 'origin::'));
$root = dirname(__DIR__, 2);
require_once $root . '/wp-content/mu-plugins/lk-layout-sync/package.php';
try {
 $policy = json_decode(file_get_contents($root . '/wp-content/mu-plugins/lk-layout-sync/scopes.json'), true, 512, JSON_THROW_ON_ERROR);
 $package = json_decode(file_get_contents($root . '/wp-content/lk-layouts/manifest.json'), true, 512, JSON_THROW_ON_ERROR);
 LK_Layout_Package::validate($package, $policy, $root . '/wp-content/lk-layouts');
 $environment = $options['environment'] ?? '';
 if (!in_array($environment, array('master', 'prod'), true) || !preg_match('/^[a-f0-9]{40}$/D', $options['commit'] ?? '')) throw new RuntimeException('Expected environment and full Git SHA.');
 $origin = $policy[$environment === 'prod' ? 'production_origin' : 'master_origin'];
 if (isset($options['origin']) && rtrim($options['origin'], '/') !== rtrim($origin, '/')) throw new RuntimeException('Workflow origin differs from the layout environment policy.');
 $ready = array('version' => 1, 'release' => $package['release'], 'commit' => $options['commit'], 'environment' => $environment, 'origin' => rtrim($origin, '/'));
 $output = $options['output'] ?? '';
 if (!$output) throw new RuntimeException('Missing output path.');
 if (!is_dir(dirname($output)) && !mkdir(dirname($output), 0755, true)) throw new RuntimeException('Cannot create the release marker directory.');
 $temp = $output . '.tmp';
 if (file_put_contents($temp, LK_Layout_Package::json($ready) . "\n", LOCK_EX) === false || !rename($temp, $output)) throw new RuntimeException('Cannot publish the release marker.');
 echo "Prepared $environment layout release {$package['release']}.\n";
} catch (Throwable $error) { fwrite(STDERR, $error->getMessage() . "\n"); exit(1); }
