<?php
if(PHP_SAPI!=='cli')exit;
require dirname(__DIR__).'/wp-load.php';
// Blog is an editable Elementor page, not the theme's posts-index template.
update_option('page_for_posts',0);
Elementor\Plugin::$instance->files_manager->clear_cache();
echo 'Child theme routing configured.'.PHP_EOL;
