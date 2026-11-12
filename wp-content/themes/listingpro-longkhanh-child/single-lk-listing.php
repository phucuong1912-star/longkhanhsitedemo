<?php
if(!defined('ABSPATH'))exit;
the_post();
$GLOBALS['lk_listing_context']=get_the_ID();
$view=get_query_var('lk_view');
$template=(int)get_option($view?'lk_template_listing_gallery':'lk_template_listing');
$content=post_password_required()?get_the_password_form():\Elementor\Plugin::$instance->frontend->get_builder_content_for_display($template,true);
?>
<!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><?php wp_head(); ?></head>
<body <?php body_class('elementor-template-canvas lk-detail-page'); ?>>
<?php wp_body_open();do_action('elementor/page_templates/canvas/before_content');echo $content;do_action('elementor/page_templates/canvas/after_content');wp_footer(); ?>
</body></html>
