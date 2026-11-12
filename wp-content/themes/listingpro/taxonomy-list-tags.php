<?php
/**
 * The template for displaying Listing category.
 */

global $listingpro_options;
$listing_mobile_view            =   $listingpro_options['single_listing_mobile_view'];


if( ($listing_mobile_view == 'app_view' && $listing_mobile_view == 'app_view2') && wp_is_mobile() )
{
    get_header('app-view');
}
else
{
    get_header();
}
$listing_style = $listingpro_options['listing_style'];
if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
    $listing_style = esc_html($_GET['list-style']);
}

if( $listing_mobile_view == 'app_view' && wp_is_mobile() )
{
    get_template_part( 'mobile/templates/listing-with-map-app' );
}
elseif ( $listing_mobile_view == 'app_view2' && wp_is_mobile() )
{
    get_template_part( 'mobile/templates/listing-with-map-app-new' );
}
else
{
    switch($listing_style) {

        case '1': get_template_part( 'templates/listing-simple' );
            break;

        case '2': get_template_part( 'templates/listing-with-sidebar' );
            break;

        case '3': get_template_part( 'templates/listing-with-map' );
            break;
		case '4': get_template_part( 'templates/listing-with-header-filters' );
        break;
        case '5': get_template_part( 'templates/listing-with-sidebar-filters' );
            break;
		case '6': get_template_part( 'templates/listing-with-map-classic' );
            break;

    }
}

if( ($listing_mobile_view == 'app_view' && $listing_mobile_view == 'app_view2') && wp_is_mobile() )
{
    get_footer('app-view');
}
else
{
    get_footer();
}