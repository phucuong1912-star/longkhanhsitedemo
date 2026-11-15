<?php
/** Offline regression tests using WordPress's real hook dispatcher, no DB or remote calls. */
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
define('ABSPATH', dirname(__DIR__) . '/');
require ABSPATH . 'wp-includes/plugin.php';
$with_helper = in_array('--with-helper', $argv, true);
if ($with_helper) {
 function get_user_fields_by_type($types) { return array('user_hours' => 'Fixture opening hours'); }
}
function cubewp_render_blocks($content, $block, $instance) { return $content . '|' . ($instance === null ? 'legacy' : $instance); }
require ABSPATH . 'wp-content/mu-plugins/long-khanh-compat.php';
function compat_expect($ok, $message) { if (!$ok) throw new RuntimeException($message); echo 'PASS ', $message, PHP_EOL; }

final class Compat_Test_Tags {
 public $tags = array();
 function register($name, $controls) { $this->tags[$name] = $controls; }
 function unregister($name) { unset($this->tags[$name]); }
 function config() {
  do_action('elementor/dynamic_tags/register', $this);
  return array_map(function ($controls) { return $controls(); }, $this->tags);
 }
}
add_action('elementor/dynamic_tags/register', function ($manager) {
 // Reproduce the legacy CubeWP module's unconditional optional-helper call.
 $manager->register('cubewp-business-hours-tag', function () { return get_user_fields_by_type(array('business_hours')); });
 $manager->register('cubewp-text-tag', function () { return array('post_field' => 'Existing post field'); });
 $manager->register('site-custom-tag', function () { return array('custom' => 'Existing custom tag'); });
}, 10);

if (!$with_helper) {
 remove_action('elementor/dynamic_tags/register', 'lk_compat_cubewp_optional_tags', 100);
 try { (new Compat_Test_Tags())->config(); throw new RuntimeException('The missing-helper fatal was not reproduced.'); }
 catch (Error $error) { compat_expect(strpos($error->getMessage(), 'get_user_fields_by_type') !== false, 'Reproduced the production CubeWP missing-helper fatal'); }
 add_action('elementor/dynamic_tags/register', 'lk_compat_cubewp_optional_tags', 100);
}
$config = (new Compat_Test_Tags())->config();
compat_expect(isset($config['cubewp-business-hours-tag']) === $with_helper, $with_helper ? 'Business-hours tag stays available when its real helper exists' : 'Only the unsupported business-hours tag is omitted before controls load');
compat_expect($config['cubewp-text-tag'] === array('post_field' => 'Existing post field'), 'Existing CubeWP post fields remain available');
compat_expect($config['site-custom-tag'] === array('custom' => 'Existing custom tag'), 'Other dynamic tags remain available');
add_filter('render_block', 'cubewp_render_blocks', 10, 3);
do_action('plugins_loaded');
compat_expect(apply_filters('render_block', 'body', array()) === 'body|legacy', 'Two-argument block hooks retain their compatibility bridge');
compat_expect(apply_filters('render_block', 'body', array(), 'current-instance') === 'body|current-instance', 'Three-argument block hooks keep the supplied instance');

if (!$with_helper) {
 $process = proc_open(array(PHP_BINARY, __FILE__, '--with-helper'), array(1 => array('pipe', 'w'), 2 => array('pipe', 'w')), $pipes);
 if (!is_resource($process)) throw new RuntimeException('Cannot run the helper-present regression case.');
 $output = stream_get_contents($pipes[1]); $error = stream_get_contents($pipes[2]); fclose($pipes[1]); fclose($pipes[2]);
 if (proc_close($process) !== 0) throw new RuntimeException($error . $output);
 echo $output;
}
