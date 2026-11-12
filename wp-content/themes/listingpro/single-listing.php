<?php
/**
 * The template for displaying Listing single page.
 *
 */




    global $listingpro_options;
    $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
	$lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];

	if( $listing_mobile_view == 'app_view' && wp_is_mobile() )
	{
        get_header('app-view');
        get_template_part( 'mobile/templates/listing_app_view' );
		do_action( 'listing_single_page_content');
        get_footer('app-view');

    }
    elseif ( $listing_mobile_view == 'app_view2' && wp_is_mobile() )
    {
        get_header('app-view');
        get_template_part( 'mobile/templates/listing_app_view2' );
        do_action( 'listing_single_page_content');
        get_footer('app-view');
    }
    else
    {
		get_header();
		
		global $post;
		$listingid = $post->ID;
		$gAddress = listing_get_metabox_by_ID('gAddress', $listingid);
		lp_get_lat_long_from_address($gAddress, $listingid);
		
		$lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];
		if($lp_detail_page_styles == 'lp_detail_page_styles1') {
			
		get_template_part( 'templates/listing_detail2' );
		}
		else if( $lp_detail_page_styles == 'lp_detail_page_styles2' )
		{
			get_template_part( 'templates/listing_detail3' );
		} 
		else if ( $lp_detail_page_styles == 'lp_detail_page_styles3' )
		{
			get_template_part( 'templates/listing-style3' );
		}
		else if( $lp_detail_page_styles == 'lp_detail_page_styles4' )
		{
			get_template_part( 'templates/listing-style4' );
		}
		else if( $lp_detail_page_styles == 'lp_detail_page_styles5' )
		{
			get_template_part( 'templates/listing_detail5' );
		}else if( $lp_detail_page_styles == 'lp_detail_page_styles6' ) {
		   get_template_part( 'templates/listing-detail6' ); //classic new style
		}

		do_action( 'listing_single_page_content');
        get_footer();
    }
	 