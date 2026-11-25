<?php
if(!defined('ABSPATH'))exit;
?><!doctype html>
<html <?php language_attributes(); ?>>
<head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width, initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('lk-auth-page'); ?>><?php wp_body_open();
do_action('elementor/page_templates/canvas/before_content');
echo '<section class="lk-auth-shell"><div class="lk-auth-card" aria-labelledby="lk-account-title">';
if(is_user_logged_in()&&lk_auth_mode()!=='reset')lk_account();else lk_auth_form(lk_auth_mode());
echo '</div></section>';
do_action('elementor/page_templates/canvas/after_content');
wp_footer(); ?></body></html>
