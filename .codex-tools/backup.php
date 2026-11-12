<?php
if (PHP_SAPI !== 'cli') { http_response_code(403); exit; }
require dirname(__DIR__) . '/wp-load.php';
global $wpdb;
$folder = 'C:/Users/ITCK/.codex/backups/longkhanhsite-' . gmdate('Ymd-His');
wp_mkdir_p($folder);
$file = fopen($folder . '/database.sql', 'wb');
fwrite($file, "SET FOREIGN_KEY_CHECKS=0;\nSET NAMES utf8mb4;\n");
foreach ($wpdb->get_col('SHOW TABLES') as $table) {
 $name = '`' . str_replace('`','``',$table) . '`';
 $create = $wpdb->get_row('SHOW CREATE TABLE ' . $name, ARRAY_N);
 fwrite($file, "DROP TABLE IF EXISTS {$name};\n" . $create[1] . ";\n");
 for ($offset=0; ; $offset+=250) {
  $rows = $wpdb->get_results("SELECT * FROM {$name} LIMIT {$offset},250", ARRAY_A);
  if (!$rows) break;
  foreach ($rows as $row) {
   $values = array_map(function($value) use ($wpdb) { return $value===null ? 'NULL' : "'" . $wpdb->_real_escape($value) . "'"; }, array_values($row));
   fwrite($file, 'INSERT INTO ' . $name . ' VALUES (' . implode(',',$values) . ");\n");
  }
 }
}
fwrite($file, "SET FOREIGN_KEY_CHECKS=1;\n"); fclose($file);
copy(ABSPATH . 'wp-config.php', $folder . '/wp-config.php');
copy(ABSPATH . 'wp-content/plugins/cubewp-framework/cube/functions/blocks-render.php', $folder . '/blocks-render.php');
echo wp_json_encode(array('backup'=>$folder,'bytes'=>filesize($folder.'/database.sql'))), PHP_EOL;
