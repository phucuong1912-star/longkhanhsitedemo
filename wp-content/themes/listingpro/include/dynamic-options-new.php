<?php

if( !function_exists( 'LP_dynamic_options_v2' ) )

{

    function LP_dynamic_options_v2()

    {

        global $listingpro_options;

        global $listingpro_options;

        $lp_page_header = $listingpro_options['page_header']['url'];

        $lp_home_banner = $listingpro_options['home_banner']['url'];

        $lp_page_banner = '';

        if(class_exists('ListingproPlugin')){

            $lp_page_banner = listing_get_metabox('lp_page_banner');

        }
        /* Header Color Options*/

        $lp_top_bar = $listingpro_options['top_bar_enable'];

        $headerBgcolor = $listingpro_options['header_bgcolor'];

        $top_bar_opacity = $listingpro_options['top_bar_opacity'];

        $banner_height = $listingpro_options['banner_height'];

        $headerBgcolor_inner    =   $listingpro_options['header_bgcolor_inner_pages'];

        $top_bar__inner =   $listingpro_options['top_bar_bg_inner'];

        $themeClr = $listingpro_options['theme_color'];

        $header_textcolor   =   $listingpro_options['header_textcolor'];

        $top_bar_color_inner    =   $listingpro_options['top_bar_color_inner'];
        $top_bar_color_inner2    =   $listingpro_options['top_bar_color_inner2'];


        $h4ff = $listingpro_options['h4_typo']['font-family'];

        $bodyff = $listingpro_options['typography-body']['font-family'];

        $topBannerView = lp_get_banner_style();

        $header_views = lp_get_header_views();



        ?>

        .lp-top-bar{

            background-color: #fff;

        }

        .lp-top-bar:after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            background-color: <?php echo esc_attr( $top_bar__inner ); ?>;
            opacity: <?php echo $top_bar_opacity; ?>;
        }

        .header-with-topbar .lp-header-overlay:first-child,

        .header-without-topbar .lp-header-overlay:first-child,

        .header-menu-dropdown .lp-header-overlay:first-child

        {

        <?php if( ($topBannerView == 'banner_view2' || $topBannerView == 'banner_view3') && ($header_views == 'header_with_topbar' || $header_views == 'header_without_topbar' || $header_views == 'header_menu_bar' ) ) :?>

        display: none;

    <?php endif; ?>

        }

        .lp-topbar-menu li a,

        .lp-top-bar-social ul li a{

        color: <?php echo esc_attr( $top_bar_color_inner ); ?>;

        }

        .home .lp-topbar-menu li a,

        .home .lp-top-bar-social ul li a{

        color: <?php echo esc_attr( $header_textcolor ); ?>;

        }
        .lp-topbar .lp-topbar-menu li a,
        .lp-topbar .lp-top-bar-social ul li a{
            color: <?php echo esc_attr( $top_bar_color_inner2 ); ?>;
        }


        .lp-header-middle,
        .lp-menu-bar.lp-header-full-width-st,
        .header-bg-color-class{
            background-color: <?php echo esc_attr( $headerBgcolor_inner ); ?>
        }

        .home .lp-header-middle,
        .home .header-bg-color-class{

        background-color: <?php echo esc_attr( $headerBgcolor ); ?>

        }


        .lp-header-search-wrap{

        <?php if( !empty( $lp_home_banner ) ): ?>

        background-image: url(<?php echo esc_url( $lp_home_banner ); ?>);

    <?php endif; ?>
        }
        .lp-header-search-wrap-banner-height{
        <?php if( !empty( $banner_height ) ): ?>

        height: <?php echo esc_attr( $banner_height ); ?>px;

    <?php endif; ?>
        }
        .lp-header-search-wrap-set-top{
        top: 0px;
        }

        .header-container-height{

        height: <?php echo esc_attr( $banner_height ); ?>px;

        }
        .lp-category-abs2 .lp-category-abs2-inner span,

        .lp-header-search .lp-search-form-submit,

        .lp-listing .lp-listing-top .lp-listing-cat,

        .red-tooltip + .tooltip > .tooltip-inner,

        .header-style2 .lp-header-middle,

        .lp-listings.list-style .lp-listing .lp-listing-bottom .lp-listing-cat i,

        .lp-header-search .price-filter ul li:hover,

        .header-style3 .lp-header-middle .lp-header-nav-btn button span,

        .lp-listing-announcement .announcement-wrap a,

        .lp-listing-review-form .review-submit-btn,

        .lp-discount-btn,

        .lp-header-search .price-filter ul li.active,

        .lp-header-user-nav .lp-user-menu .lp-user-welcome,

        .imo-widget-title-container:before,

        .dis-code-copy-pop .copy-now,
        .event-grid-date span,
        .event-grid-ticket{

        background-color: <?php echo esc_attr( $themeClr ); ?>

        }

        .lp-section-heading i, .lp-section-title-container i,

        .lp-section-heading i,

        .lp-listing .lp-listing-bottom h3 a:hover,

        .lp-listings .more-listings a,

        .lp-activity a:hover,

        .lp-activity .lp-activity-bottom .lp-activity-review-writer a,

        .footer-menu ul li a:hover,

        .footer-menu ul li a:focus,

        .lp-listings-widget .lp-listing .lp-listing-detail h3 a:hover,

        .lp-header-title .lp-header-title-left .lp-header-breadcrumbs a:hover,

        .header-style3 .lp-header-middle .lp-header-add-btn a,

        .lp-listing-announcement .announcement-wrap i,

        .lp-listing-faq .faq-title a span,

        .lp-widget-social-links a:hover,

        .flip-clock-wrapper ul li a div div.inn,

        .lp-widget .lp-listing-price-range p a,

        .lp-listing-additional-details li span,

        .listing-page-sidebar .lp-widget ul li.lp-widget-social-links a:hover,

        .online-owner-widget .lp-online-social a:hover,

        .lp-discount-widget .lp-dis-code-copy span,

        .lp-section-title-container .lp-sub-title a,

        .lp-listing-faq .faq-title a i,

        .lp-listing-faq .ui-accordion-header-icon,

        .lp-header-search-form #input-dropdown li:hover span,

        .lp-header-search-form #input-dropdown li:hover a,

        .lp-header-search-form #input-dropdown li:hover,

        .lp-header-search-form .chosen-container .chosen-results li:hover,

        .lp-header-search-form .chosen-container .chosen-results li.highlighted,

        .home .lp-top-bar-social ul li a:hover,

        .lp-activity-review-writer a,

        .lp-activity-description a,

        .arrow-left:hover,

        .arrow-right:hover,

        .lp-menu-item-title:hover,

        .lp-listing-menu-items .lp-listing-menu-item .lp-menu-item-price span,

        .lp-listing-menu-items h6,

        .lp-listing-menu-top span,

        .widget-social-icons li a,

        .lp-dis-code-copy span,

        #sidebar aside.widget.widget_recent_entries li a:hover,

        .element-inner-button,

        .lp-header-title .lp-header-toggles a.active,

        .lp-author-nav ul li a i,
        .hours-select > li > button.add-hours span,
        .element-inner-button,
        .lp-listings .listing-slider .lp-listing-bottom-inner .lp-listing-cat,
        .event-hosted-grid label strong,
        .lp-listing-menu-items .lp-listing-menu-item .lp-menu-item-price a,
        .lp-event-detail-title-hosted h2 a,.lp-event-detail-side-section ul li > a,
        .lp-event-attende-view-all span
        {
        color: <?php echo esc_attr( $themeClr ); ?> !important;
        }
        .widget-social-icons li a.phone-link{
        color: #797979 !important;
        }

        .lp-header-search .price-filter ul li:hover,

        .lp-header-search .price-filter ul li.active,

        .header-style3 .lp-header-middle .lp-header-nav-btn button,

        .grid-style3 .lp-blog-grid-link:hover,
        .lp-event-attende-view-all span{

        border-color: <?php echo esc_attr( $themeClr ); ?> !important;

        }



        .lp-header-search-filters .header-filter-wrap:hover,

        .lp-header-search-filters .header-filter-wrap.active,

        .lp-listings .more-listings a:hover,

        .lp-section-title-container .lp-sub-title a:hover,

        .lp-listing-timings .toggle-all-days:hover,

        .lp-pagination ul li > span.current:hover, .lp-pagination ul li > span.current,

        .lp-listing-action-btns .smenu div:hover,

        .lp-listing-action-btns .smenu div:hover a,

        .lp-listing-action-btns .smenu div a:hover,

        .lp-blog-grid-author li.category-link a:hover,

        .grid-style3 .lp-blog-grid-link,

        .new-list-style .lp-blog-grid-link,

        .grid-style3 .lp-blog-grid-shares span i:hover,

        .new-list-style .lp-blog-grid-shares span i:hover,

        .tagcloud a:hover,

        .element-inner-button:hover,
        .lp-event-attende-view-all span:hover,
        .lp-detail-event-going-btn button{

        color: #fff !important;

        border-color: <?php echo esc_attr( $themeClr ); ?> !important;

        background-color: <?php echo esc_attr( $themeClr ); ?> !important;

        }

        .lp-header-search-filters .header-filter-wrap.active i,
        .lp-header-search-filters .header-filter-wrap:hover i,
        .lp-header-toggles a{
        color: #fff !important;
        }

        .lp-header-search .lp-header-search-tagline,
        .left-heading .lp-sub-title,
        .sidebar-banner-des,.lp-header-search-tagline-sidebar-banner span{
        font-family: <?php echo esc_attr($h4ff); ?>;
        }

        .lp-join-now li a{

        <?php

        if( $bodyff && $bodyff != '' ){

            ?>

            font-family: <?php echo esc_attr( $bodyff ); ?>

            <?php

        }

        ?>

        }


        lp-header-middle .lp-header-user-nav .header-login-btn:hover{

        color: <?php echo esc_attr( $themeClr ); ?> !important;

        border-color: #fff;

        background-color: #fff;

        }

        .widget-social-icons li a:hover,
        .lp-header-middle .lp-header-user-nav:hover a,

        .close-right-icon{

        color: <?php echo esc_attr( $themeClr ); ?> !important;

        }

        .lp-ann-btn, .lp-ann-btn:hover{

        color: <?php echo esc_attr( $themeClr ); ?>;

        }

        .lp-listing-action-btns ul li#lp-book-now > a:hover,

        .lp-listing-leadform-inner .form-horizontal input[type="submit"],

        .page-container-second-style2 .blog-pagination a,

        .page-container-second-style2 .lp-review-btn,

        .contact-style2.contact-right .lp-review-btn,

        .lp-banner-bottom-right a:hover{

        background-color: <?php echo esc_attr( $themeClr ); ?> !important;

        border-color: <?php echo esc_attr( $themeClr ); ?> !important;

        color: #fff !important;

        }

        .lp-listing-action-btns ul li#lp-book-now > a:hover i{

        color: #fff !important;

        }

        .lp_auto_loc_container .lp-dyn-city{



        }

        .popup-header strong, .lp-listing-announcements .lp-listing-announcement .close-ann{

        background-color: <?php echo esc_attr( $themeClr ); ?>

        }

        .lp-listing-additional-details .toggle-additional-details,

        .lp-author-nav ul li a:hover, .lp-author-nav ul li a:hover i,

        .lp-author-nav ul li a.active:before,
        .lp-events-btns-outer button:first-child{

        background-color: <?php echo esc_attr( $themeClr ); ?>;

        color: #fff !important;

        border-color: <?php echo esc_attr( $themeClr ); ?>

        }

        .app-view-reviews-all{

        color: <?php echo esc_attr( $themeClr ); ?>;

        border-color: <?php echo esc_attr( $themeClr ); ?>;

        }

        .icon-bar{

        background-color: <?php echo esc_attr( $listingpro_options['header_textcolor'] ); ?>;

        }
		.app-view-header .icon-bar{
			background-color: <?php echo esc_attr( $listingpro_options['app_view_header_textcolor'] ); ?>;
		}
		.app-view-header .lp-search-toggle .user-menu i,.open-filter, .close-filter, .home-filter-close, .home-filter-open{
			color: <?php echo esc_attr( $listingpro_options['app_view_header_textcolor'] ); ?>;
		}
        .contact-right a span{
        color: <?php echo esc_attr( $listingpro_options['typography-body']['color'] ); ?>;
        }
        .contact-right a span:hover{
        color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .lp-grid-box-bottom-grid6 a:hover,.lp-filter-top-section li a,.sidebar-filters .currency-signs>ul>li>a{
        color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .lp-grid6-cate a:hover,.lp-listing-cats a:hover,.lp-grid-box-bottom-grid6 .pull-right:hover .fa,.lp-grid-box-bottom-grid6 .pull-right a:hover{
        color: <?php echo esc_attr( $themeClr ); ?> !important;

        }
        .sidebar-filters .currency-signs>ul>li>a:hover{
        border-color: <?php echo esc_attr( $themeClr ); ?>;

        }
        .search-filters > ul > li > a.active, div#lp-find-near-me ul li a.active{
        border-color: <?php echo esc_attr( $themeClr ); ?> !important;

        }
        .search-filters > ul > li > a.active{
        background-color: <?php echo esc_attr( $themeClr ); ?> !important;

        }
        <?php

    }

}

//add_action('wp_head', 'LP_dynamic_options_v2', 101);



?>