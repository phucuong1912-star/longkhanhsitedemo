<?php
/*
Plugin Name: ListingPro Ads
Plugin URI: 
Description: This plugin Only compatible With listingpro Theme By CridioStudio.
Version: 1.4
Author: CridioStudio (Dev Team)
Author URI: http://www.cridio.com
Author Email: support@cridio.com

  Copyright 2018 CridioStudio
*/

if (!defined('ABSPATH')) return;
if (!is_admin()) return;

if (!defined('PLUGIN_DIR_PATH')) define('PLUGIN_DIR_PATH', plugins_url('', __FILE__));


class ListingAds
{

    private $dir;

    function __construct()
    {
        $dir = dirname(__FILE__);
    }
    /* /constructor */

    public function includeFiles()
    {
        include_once('functions.php');
        include_once('include/functions.php');
    }
}
/* end class */
$active_theme = wp_get_theme();
if ($active_theme->get('Name') == 'Listingpro' || $active_theme->get('Template') == 'listingpro') {
    $obj = new ListingAds();
    $obj->includeFiles();
}
add_action('admin_enqueue_scripts', 'includeScripts');
if (!function_exists('includeScripts')) {
    function includeScripts()
    {
        global $pagenow, $post_type;
        if (($pagenow == "post-new.php" && $post_type == "lp-ads") || ($pagenow == "post.php" && $post_type == "lp-ads")) {
            //wp_register_style('jquery-uui', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css');
            //wp_enqueue_style('jquery-uui');
            //wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('plugin-mainjs', PLUGIN_DIR_PATH . '/js/main.js', array('jquery'));
        }
    }
}
