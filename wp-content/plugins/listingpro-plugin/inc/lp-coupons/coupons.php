<?php
/*---------------------------------------------------
				adding coupons page
----------------------------------------------------*/
if(!function_exists('listingpro_register_coupons_page')){
	function listingpro_register_coupons_page() {
		add_submenu_page(
			'edit.php?post_type=price_plan',
			__( 'Coupons', 'listingpro-plugin' ),
			'Coupons',
			'manage_options',
			'lp-coupons',
			'listingpro_coupons_page',
            33
		);
		
	}
}
add_action( 'admin_menu', 'listingpro_register_coupons_page' );

/* ----------------------include functions.php--------------- */
include_once(WP_PLUGIN_DIR.'/listingpro-plugin/inc/lp-coupons/functions.php');

?>