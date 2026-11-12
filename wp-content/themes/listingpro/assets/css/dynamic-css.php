<?php
/**
 * Dynamic css generation file
 *
 */
$path = preg_replace('/wp-content(?!.*wp-content).*/','',__DIR__);
require_once($path.'wp-load.php');

header('Content-type: text/css');
header('Cache-control: must-revalidate');
header( "Content-type: text/css; charset: UTF-8" );

echo LP_dynamic_options_v2();
echo listingpro_dynamic_options();
echo listingpro_dynamic_css_options();

?>