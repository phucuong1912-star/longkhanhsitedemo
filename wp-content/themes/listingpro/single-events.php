<?php
/**
 * The template for displaying Events single page.
 *
 */

global $listingpro_options;
$listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
if( $listing_mobile_view == 'app_view' && wp_is_mobile() )
{
	get_header('app-view');
	get_template_part('mobile/templates/events-app-view');
	get_footer('app-view');

}else{

	get_header();

	get_template_part( 'templates/single-event/event-detail' );

	get_footer();
}
?>