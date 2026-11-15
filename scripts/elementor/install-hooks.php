<?php
if (PHP_SAPI !== 'cli') exit;
chdir(dirname(__DIR__, 2));
$current = trim((string) shell_exec('git config --local --get core.hooksPath'));
if ($current !== '' && $current !== '.githooks') { fwrite(STDERR, "An existing hooksPath is configured; chain the layout hooks without replacing existing hooks.\n"); exit(1); }
passthru('git config --local core.hooksPath .githooks', $code); if ($code) exit($code);
$process = proc_open(array('git', 'config', '--local', 'lk.phpBinary', PHP_BINARY), array(), $pipes);
if (!is_resource($process) || proc_close($process) !== 0) exit(1);
echo "Installed local Elementor export/check hooks. No remote configuration was changed.\n";
