<?php
if( !function_exists('listingpro_dynamic_options')){
    function listingpro_dynamic_options() {
        $lp_page_header = lp_theme_option_url('page_header');
        $lp_home_banner = lp_theme_option_url('home_banner');
        $lp_page_banner = '';
        if(class_exists('ListingproPlugin')){
            $lp_page_banner = listing_get_metabox('lp_page_banner');
        }
        /* Header Color Options*/
        $lp_top_bar = lp_theme_option('top_bar_enable');
        $headerBgcolor = lp_theme_option('header_bgcolor');
        $headerBgcolorInner = lp_theme_option('header_bgcolor_inner_pages');
        $top_bar_bgcolor = lp_theme_option('top_bar_bgcolor');
        $topBannerView = lp_get_banner_style();
        $app_view_menu_header_img = lp_theme_option_url('app_view_menu_header_img');
        if($app_view_menu_header_img    ==  ''){
            $app_view_menu_header_img   =   get_template_directory_uri().'/assets/images/admin/adminbg.jpg';
        }
// Body 
        $bodyff = lp_theme_option_by_index('typography-body','font-family');
        $bodyfz = lp_theme_option_by_index('typography-body','font-size');
        $bodyfw = lp_theme_option_by_index('typography-body','font-weight');
        $bodycol = lp_theme_option_by_index('typography-body','color');
        $bodylh = lp_theme_option_by_index('typography-body','line-height');
// H1 Styles
        $h1ff = lp_theme_option_by_index('h1_typo','font-family');
        $h1fz = lp_theme_option_by_index('h1_typo','font-size');
        $h1fw = lp_theme_option_by_index('h1_typo','font-weight');
        $h1col = lp_theme_option_by_index('h1_typo','color');
        $h1lh = lp_theme_option_by_index('h1_typo','line-height');
// H2 Styles
        $h2ff = lp_theme_option_by_index('h2_typo','font-family');
        $h2fz = lp_theme_option_by_index('h2_typo','font-size');
        $h2fw = lp_theme_option_by_index('h2_typo','font-weight');
        $h2col = lp_theme_option_by_index('h2_typo','color');
        $h2lh = lp_theme_option_by_index('h2_typo','line-height');
// H3 Styles
        $h3ff = lp_theme_option_by_index('h3_typo','font-family');
        $h3fz = lp_theme_option_by_index('h3_typo','font-size');
        $h3fw = lp_theme_option_by_index('h3_typo','font-weight');
        $h3col = lp_theme_option_by_index('h3_typo','color');
        $h3lh = lp_theme_option_by_index('h3_typo','line-height');
// H4 Styles
        $h4ff = lp_theme_option_by_index('h4_typo','font-family');
        $h4fz = lp_theme_option_by_index('h4_typo','font-size');
        $h4fw = lp_theme_option_by_index('h4_typo','font-weight');
        $h4col = lp_theme_option_by_index('h4_typo','color');
        $h4lh = lp_theme_option_by_index('h4_typo','line-height');
// H5 Styles
        $h5ff = lp_theme_option_by_index('h5_typo','font-family');
        $h5fz = lp_theme_option_by_index('h5_typo','font-size');
        $h5fw = lp_theme_option_by_index('h5_typo','font-weight');
        $h5col = lp_theme_option_by_index('h5_typo','color');
        $h5lh = lp_theme_option_by_index('h5_typo','line-height');
// H6 Styles
        $h6ff = lp_theme_option_by_index('h6_typo','font-family');
        $h6fz = lp_theme_option_by_index('h6_typo','font-size');
        $h6fw = lp_theme_option_by_index('h6_typo','font-weight');
        $h6col = lp_theme_option_by_index('h6_typo','color');
        $h6lh = lp_theme_option_by_index('h6_typo','line-height');
// p Styles
        $pff = lp_theme_option_by_index('paragraph_typo','font-family');
        $pfz = lp_theme_option_by_index('paragraph_typo','font-size');
        $pfw = lp_theme_option_by_index('paragraph_typo','font-weight');
        $pcol = lp_theme_option_by_index('paragraph_typo','color');
        $plh = lp_theme_option_by_index('paragraph_typo','line-height');
// Navigation Styles
        $nav_ff = lp_theme_option_by_index('nav_typo','font-family');
        $nav_fz = lp_theme_option_by_index('nav_typo','font-size');
        $nav_fw = lp_theme_option_by_index('nav_typo','font-weight');
        $nav_col = lp_theme_option_by_index('nav_typo','color');
        $themeClr = lp_theme_option('theme_color');
        $secThemeClr = lp_theme_option('sec_theme_color');
        $footer_top_bgcolor = lp_theme_option('footer_top_bgcolor');
        $footer_bgcolor = lp_theme_option('footer_bgcolor');
        $banner_opacity = lp_theme_option('banner_opacity');
        $banner_height = lp_theme_option('banner_height');
        $header_textcolor = lp_theme_option('header_textcolor');
//Banner heights for search bottom view
        $banner_height_bottom = lp_theme_option('banner_height_search_bottom');
// Categories colors
        $categories_background_color = lp_theme_option('categories_color');
        $categories_background_hover_color = lp_theme_option('categories_hover_color');
        $categories_text_color = lp_theme_option('categories_text_color');
        $footer_bottom_color_switch =   lp_theme_option('footer_bottom_color_switch');
        $footer_top_color_switch    =   lp_theme_option('footer_top_color_switch');
        $footer_top_text_color  =   '';
        $footer_bottom_text_color    =   '';
        if( $footer_bottom_color_switch == 1 )
        {
            $footer_bottom_text_color   =   lp_theme_option('footer_bottom_text_color');
        }
        if( $footer_top_color_switch )
        {
            $footer_top_text_color  =   lp_theme_option('footer_top_text_color');
        }
        function hex2rgba($color, $opacity = false) {
            $default = 'rgb(0,0,0)';
            //Return default if no color provided
            if(empty($color))
                return $default;
            //Sanitize $color if "#" is provided
            if ($color[0] == '#' ) {
                $color = substr( $color, 1 );
            }
            //Check if color has 6 or 3 characters and get values
            if (strlen($color) == 6) {
                $hex = array( $color[0] . $color[1], $color[2] . $color[3], $color[4] . $color[5] );
            } elseif ( strlen( $color ) == 3 ) {
                $hex = array( $color[0] . $color[0], $color[1] . $color[1], $color[2] . $color[2] );
            } else {
                return $default;
            }
            //Convert hexadec to rgb
            $rgb =  array_map('hexdec', $hex);
            //Check if opacity is set(rgba or rgb)
            if($opacity){
                if(abs($opacity) == 1)
                    $opacity = 1.0;
                $output = 'rgba('.implode(",",$rgb).','.$opacity.')';
            } else {
                $output = 'rgb('.implode(",",$rgb).')';
            }
            //Return rgb(a) color string
            return $output;
        }
        $color = $themeClr;
        $rgb = hex2rgba($color);
        $rgba = hex2rgba($color, 0.9);
        $header_bg_color_archive    =   lp_theme_option('lp_archive_bg_color');
        $header_bg_color_detailpage =   lp_theme_option('lp_detail_page_styles4_bg_color');
        $header_bg_img_archive = lp_theme_option_url('lp_archive_bg');
        $header_bg_img_detailpage = lp_theme_option_url('lp_detail_page_styles4_bg');
		$footer_icon_Clr = lp_theme_option('footer_bottom_icon_color_switch');//classic new style
        ?>
        .single .lp-listing-top-title-header .lp-header-overlay {
            background-color: <?php echo $header_bg_color_detailpage; ?>;
            <?php 
                if (!empty($header_bg_img_detailpage)) {
                    ?>opacity: 0.6;<?php
                }else {
                    ?>opacity: 1;<?php
                }
            ?>
        }
        .archive .lp-header-overlay,
        .search .lp-header-overlay
        {
            background-color: <?php echo $header_bg_color_archive; ?>;
            <?php 
                if (!empty($header_bg_img_archive)) {
                    ?>opacity: 0.8;<?php
                }else {
                    ?>opacity: 1;<?php
                }
            ?>
        }


        <?php
        if(is_admin_bar_showing()){
            ?>
            div.lp-top-notification-bar{
            top: 32px !important;
            }
            <?php
        }
        ?>
        .banner-view-classic .lp-home-categoires li a,
        .home-categories-area .new-banner-category-view2 li a span p#cat-img-bg,
        .home-categories-area .new-banner-category-view1 li a,
        .banner-default-view-category-2 .lp-home-categoires li a span p#cat-img-bg,
        .banner-view-cat-tranparent-category .lp-upper-cat-view3 li a span p#cat-img-bg,
        .home-categories-area .lp-inside-search-view1 li a,
        .home-categories-area .lp-inside-search-view2 li a span p#cat-img-bg,
        .banner-view-cat-tranparent-category .lp-upper-cat-view4 li a,
        .home-categories-area .banner-default-view-category1 li a,
        .home-categories-area .banner-default-view-category2 li a span p#cat-img-bg,
        .banner-default-view-category4.lp-home-categoires li a,
        .banner-default-view-category4.lp-home-categoires li a span p#cat-img-bg,
        .banner-default-view-category3.lp-home-categoires li a,
        .banner-default-view-category3.lp-home-categoires li a span p#cat-img-bg,
        .new-banner-category-view.lp-home-categoires li a,
        .new-banner-category-view3.lp-home-categoires li a,
        .lp-upper-cat-view1.lp-home-categoires li a,
        .lp-upper-cat-view2.lp-home-categoires li a,
        .lp-inside-search-view.lp-home-categoires li a,
        .lp-inside-search-view3.lp-home-categoires li a,
        .home-categories-area .lp-home-categoires.new-banner-category-view4 li a,
        .lp-home-categoires li a
        {
        background: <?php echo esc_html($categories_background_color); ?> !important;
        }
        #cat-img-bg i {
            line-height: 80px;
        }
        .lp-home-categoires li a:hover {
        background: <?php echo esc_html($categories_background_hover_color); ?> !important;
        }
        .home-categories-area .new-banner-category-view2 li a:hover span p#cat-img-bg, .home-categories-area .new-banner-category-view1 li a:hover, .banner-default-view-category-2 .lp-home-categoires li a:hover span p#cat-img-bg, .banner-view-cat-tranparent-category .lp-upper-cat-view3 li a:hover span p#cat-img-bg, .home-categories-area .lp-inside-search-view1 li a:hover, .home-categories-area .lp-inside-search-view2 li a:hover span p#cat-img-bg, .banner-view-cat-tranparent-category .lp-upper-cat-view4 li a:hover,.home-categories-area .banner-default-view-category1 li a:hover, .home-categories-area .banner-default-view-category2 li a:hover span p#cat-img-bg,
        .banner-default-view-category4.lp-home-categoires li a:hover,
        .banner-default-view-category3.lp-home-categoires li a:hover,.new-banner-category-view.lp-home-categoires li a:hover,.new-banner-category-view3.lp-home-categoires li a:hover,
        .lp-upper-cat-view1.lp-home-categoires li a:hover,.lp-upper-cat-view2.lp-home-categoires li a:hover,.lp-inside-search-view.lp-home-categoires li a:hover,.lp-inside-search-view3.lp-home-categoires li a:hover, .home-categories-area .lp-home-categoires.new-banner-category-view4 li a:hover{
        background: <?php echo esc_html($categories_background_hover_color); ?> !important;
        }
        .home-categories-area .lp-home-categoires.category-view-bg-transparent li a{
        background: <?php echo hex2rgba($categories_background_color, 0.4); ?> !important;
        }
        .home-categories-area .lp-home-categoires.category-view-bg-transparent li a:hover{
        background: <?php echo hex2rgba($categories_background_hover_color, 0.4); ?> !important;
        }
        .lp-home-banner-contianer-inner-new-search .lp-search-description p, .home-categories-area .new-banner-category-view2 li a span, .banner-default-view-category-2 .lp-home-categoires li a span, .home-categories-area .lp-inside-search-view2 li a span,.home-categories-area .banner-default-view-category2 li a span, .home-categories-area ul.lp-home-categoires li a span{
        color: <?php echo esc_html($categories_text_color); ?>;
        }
        .header-container.lp-header-bg .lp-color-header-style .lp-menu-container .lp-menu > div > ul > li > a,
        .header-container.lp-header-bg .lp-color-header-style .lp-menu-container .lp-menu ul li.page_item_has_children::after,
        .header-container.lp-header-bg .lp-color-header-style .lp-menu-container .lp-menu ul li.menu-item-has-children::after, .lp-menu-outer::after, #click-search-view,
        #click-search-view i, .lp-menu-container .lp-menu > div > ul > li > a,
        .lp-join-now a,
        .lp-header-full-width .lp-add-listing-btn li a,
        .lp-header-middle .header-main-menu ul li a,
        .lp-add-listing-btn ul li a,
        .lp-header-bg-black .navbar-toggle,
        .lp-header-full-width .lp-add-listing-btn li a, .lp-header-middle .lp-header-add-btn a{
        color: <?php echo esc_html($header_textcolor); ?>;
        }
        .lp-header-full-width .lp-add-listing-btn li a,
        .lp-header-bg-black .navbar-toggle, .lp-header-middle .lp-header-add-btn a,
        .header-container.lp-header-bg .lp-menu-container .lp-menu>div>ul>li>a{
        border-color: <?php echo esc_html($header_textcolor); ?>;
        }
        .lp-header-full-width .lp-add-listing-btn ul li a.header-list-icon, .lp-header-full-width .lp-add-listing-btn ul li a.header-list-icon-st6{
        border: 1px solid <?php echo esc_html($header_textcolor); ?>;
        }
        .lp-header-with-bigmenu .lp-join-now a,.lp-header-with-bigmenu .lp-add-listing-btn ul li a{
        color: <?php echo esc_html($header_textcolor); ?> ;
        }
        .lp-header-with-bigmenu .lp-join-now a:hover,.lp-header-with-bigmenu .lp-add-listing-btn ul li a,.lp-header-with-bigmenu .nav .open > a,.lp-header-with-bigmenu .nav .open > a:hover,.lp-header-with-bigmenu .nav .open > a:focus,.lp-header-with-bigmenu .nav > li > a:hover,.lp-header-with-bigmenu .nav > li > a:focus,.lp-mega-menu-outer #menu-main li a:hover,.about-box-icon-style2 i,.lp-total-meta ul li a:hover,
        .lp-total-meta ul li a:hover span,.lp-total-meta ul .active a,.lp-total-meta ul .active a span,.theme-color,
        .lp-qoute-butn a,.lp-mega-menu-outer .lp-menu ul li:hover a,.theme-color span,
        .stickynavbar #nav_bar ul li a:hover{
        color: <?php echo esc_html($themeClr); ?> ;
        }
        .theme-color,.listing-category-slider4 .slick-prev:hover:before,.listing-category-slider4 .slick-next:hover:before{
        color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-category-icon-outer,.lp-right-content-box .lp-social-box ul li a i:hover,.lp-detail-services-box ul li a:hover,.lp-detail-offers-content a,.lp-offer-count,.lp-quote-submit-btn,.lp-left-filter .search-filters .sortbyrated-outer ul li a:hover,.lp-qoute-butn a:first-child,.lp-new-social-widget li i,.lp_payment_step_next.active{
        background-color:<?php echo esc_html($themeClr); ?> ;
        }
        .listing-second-view .widget-box.business-contact.lp-lead-form-st input.lp-review-btn,
        .listing-style-3 a.open-map-view:hover,.listing-app-view .listing-app-view2 .lp-listing-announcement .announcement-wrap.last .announcement-btn{
        color: <?php echo esc_html($themeClr); ?>;
        border: 1px solid <?php echo esc_html($themeClr); ?>;
        }
        .widget-box.business-contact.lp-lead-form-st .contact-form.quickform form.form-horizontal .form-group.pos-relative i.lp-search-icon,
        .listing-app-view .listing-app-view2 .single_listing .review-form .form-submit .lp-review-btn, .lp-dashboard-event-tick-btn .fa{
        color: <?php echo esc_html($themeClr); ?>;
        }
        .widget-box.business-contact.lp-lead-form-st .contact-form.quickform form.form-horizontal .form-group.pos-relative:hover input.lp-review-btn,#see_filter:hover {
        background: <?php echo esc_html($themeClr); ?>;
        }
        .lp-search-bar-header .lp-header-search-button .lp-search-bar-right .fa-magnifying-glass.lp-search-icon:hover,
        .listing-app-view2 .toggle-all-days:hover,.lp-menu-app-view-outer .lp-menu-type-heading,
        .listing-app-view .listing-app-view2 .lp-listing-announcement .announcement-wrap.last .announcement-btn:hover,
        .listing-app-view .listing-app-view2 .single_listing .review-form .form-submit .lp-review-btn:hover,#claimform.lp-form-planclaim-st .claim-details .form-group .lp-review-btn,#lp_report_listing button#lp-report-this-listing{
        background: <?php echo esc_html($themeClr); ?>;
        color: #fff;
        }
        .lp-qoute-butn a,.listing-app-view2 .toggle-all-days:hover,
        .listing-app-view .listing-app-view2 .single_listing .review-form .form-submit .lp-review-btn:hover,.listing-app-view .listing-app-view2 .single_listing .review-form .form-submit .lp-review-btn{
        border-color:<?php echo esc_html($themeClr); ?> ;
        }
        .lp-detail-services-box ul li a:hover,.lp-left-filter .search-filters > ul > li > a:hover,.list_view .lp-grid3-category-outer span.cat-icon{
        background-color:<?php echo esc_html($themeClr); ?> !important;
        }
        .lp-header-with-bigmenu .lp-join-now a,.lp-header-with-bigmenu .lp-add-listing-btn ul li a{
        color: <?php echo esc_html($header_textcolor); ?> ;
        }
        .lp-header-with-bigmenu .lp-join-now a:hover,.lp-header-with-bigmenu .lp-add-listing-btn ul li a,.lp-header-with-bigmenu .nav .open > a,.lp-header-with-bigmenu .nav .open > a:hover,.lp-header-with-bigmenu .nav .open > a:focus,.lp-header-with-bigmenu .nav > li > a:hover,.lp-header-with-bigmenu .nav > li > a:focus,.lp-mega-menu-outer #menu-main li a:hover,.about-box-icon-style2 i,.lp-total-meta ul li a:hover,
        .lp-total-meta ul li a:hover span,.lp-total-meta ul .active a,.lp-total-meta ul .active a span,.theme-color,
        .lp-qoute-butn a,.lp-mega-menu-outer .lp-menu ul li:hover a,.theme-color span,
        .stickynavbar #nav_bar ul li a:hover{
        color: <?php echo esc_html($themeClr); ?> ;
        }
        .theme-color,.listing-category-slider4 .slick-prev:hover:before,.listing-category-slider4 .slick-next:hover:before,
        .listing-app-view .listing-app-view2 .lp-listing-announcement .announcement-wrap.last .announcement-btn
        ,.lp-popular-menu-outer h6,.lp-popular-menu-outer h6 .fa
        {
        color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-category-icon-outer,.lp-right-content-box .lp-social-box ul li a i:hover,.lp-detail-services-box ul li a:hover,.lp-detail-offers-content a,.lp-offer-count,.lp-quote-submit-btn,.lp-left-filter .search-filters .sortbyrated-outer ul li a:hover,.lp-qoute-butn a:first-child,.lp-new-social-widget li i{
        background-color:<?php echo esc_html($themeClr); ?> ;
        }
        .lp-qoute-butn a{
        border-color:<?php echo esc_html($themeClr); ?> ;
        }
        .lp-detail-services-box ul li a:hover,.lp-left-filter .search-filters > ul > li > a:hover,.list_view .lp-grid3-category-outer span.cat-icon
        ,.lp-listing-menuu-slider .slick-arrow:hover
        {
        background-color:<?php echo esc_html($themeClr); ?> !important;
        }
        .footer-style1 .footer-upper-bar,
        .footer-style2,
        .footer-style4,
        .footer-style5,
        .footer-style7,
        .footer-style8,
        .footer-style10,
        .footer-style11{
        background-color: <?php echo esc_attr( $footer_top_bgcolor ); ?>;
        }
        .footer-style1 .footer-bottom-bar,
        .footer-style3,
        .footer4-bottom-area,
        .footer5-bottom-area,
        .footer-style6,
        .footer7-bottom-area,
        .footer8-bottom-area,
        .footer-style8-bg-logo,
        .footer-style9,
        .footer10-bottom-area{
        background-color: <?php echo esc_attr( $footer_bgcolor ); ?>;
        }
        .footer-style1 .footer-upper-bar,
        .footer-style1 .footer-upper-bar a,
        .padding-top-60 .widget h2,
        .padding-top-60 .widget a,
        .padding-top-60 .widget span,
        .padding-top-60 .widget p{
        <?php
        if( !empty( $footer_top_color_switch ) )
        {
            ?>
            color: <?php echo esc_attr( $footer_top_text_color ); ?> !important;
            border-color: <?php echo esc_attr( $footer_top_text_color ); ?>
            <?php
        }
        ?>
        }
        .footer-about-company li:before{
        <?php
        if( !empty( $footer_bottom_text_color ) )
        {
            ?>
            background-color: <?php echo esc_attr( $footer_bottom_text_color ); ?> !important;
            <?php
        }
        ?>
        }
        footer.style3 .container,
        footer.style3 .copyrights,
        footer.style3 .footer-menu ul li a,
        .footer-bottom-bar .footer-about-company li,
        .footer-bottom-bar .credit-links,
        .footer-bottom-bar .credit-links a,
        footer .widget-title,
        footer .widget-title h2,
        footer .widget a,
        footer .widget span,
        footer .widget p,
        footer .tagcloud a,
        footer.footer-style9 .footer-menu li a,
        .footer4-bottom-area .footer-menu li a,
        .footer4-bottom-area .copyrights,
        .footer5-bottom-area .copyrights{
        <?php
        if( !empty( $footer_bottom_text_color ) )
        {
            ?>
            color: <?php echo esc_attr( $footer_bottom_text_color ); ?> !important;
            border-color: <?php echo esc_attr( $footer_bottom_text_color ); ?>;
            <?php
        }
        ?>
        }
        <?php
        if(!empty($headerBgcolor))
        {
            ?>
            .header-front-page-wrap .lp-header-full-width .lp-menu-bar{
            background-color: <?php echo esc_html($headerBgcolor); ?>;
            }
            .header-right-panel .lp-menu > ul li a,
            .lp-menu > ul li a,
            .lp-menu ul li.page_item_has_children::after,
            .lp-menu ul li.menu-item-has-children::after{
            color: <?php echo esc_html($header_textcolor); ?> !important;
            }
            .header-inner-page-wrap .lp-menu-bar-color{
            background-color: <?php echo esc_html($headerBgcolorInner); ?>
            }
            .header-inner-page-wrap .header-filter .input-group.width-49-percent.margin-right-15.hide-where,
            .header-inner-page-wrap .header-filter .input-group.width-49-percent,
            .header-inner-page-wrap .header-filter .input-group.width-49-percent.margin-right-15 {
            border:1px solid <?php echo esc_html($header_textcolor); ?>;
            }
            .header-inner-page-wrap .lp-header-bg-black .navbar-toggle,
            .header-inner-page-wrap .lp-header-bg-black.header-fixed .navbar-toggle{
            color: <?php echo esc_html($header_textcolor); ?>;
            border-color: <?php echo esc_html($header_textcolor); ?>;
            }
            <?php
            if( $topBannerView == 'map_view' )
            {
                ?>
                .header-front-page-wrap .lp-menu-bar, .header-normal .lp-menu-bar.lp-header-full-width {
                background-color: <?php echo esc_html($headerBgcolor); ?>
                }
                .header-front-page-wrap .header-right-panel .lp-menu ul li a,
                .header-front-page-wrap .lp-menu ul li.page_item_has_children::after,
                .header-front-page-wrap .lp-menu ul li.menu-item-has-children::after,
                .header-front-page-wrap .lp-join-now a, .lp-add-listing-btn li a {
                color: <?php echo esc_html($header_textcolor); ?>;
                }
                <?php
            }
        }
        if(!empty($top_bar_bgcolor)) { ?>
            .lp-topbar {
            background-color: <?php echo esc_html($top_bar_bgcolor); ?> !important;
            }
        <?php } ?>
        <?php if(!empty($lp_page_banner)){ ?>
            .listing-page{
            background-image:url(<?php echo esc_url($lp_page_banner); ?>);
            }
        <?php } elseif(!empty($lp_page_header)){ ?>
            .listing-page{
            background-image:url(<?php echo esc_url($lp_page_header); ?>);
            }
        <?php } ?>
        <?php
        $videoBanner = '';
        $videoBanner = lp_theme_option('lp_video_banner_on');
        if($videoBanner!="video_banner"){
            if(!empty($lp_home_banner)){ ?>
                .header-container.lp-header-bg{
                background-image:url(<?php echo esc_url($lp_home_banner); ?>);
                background-repeat:no-repeat;
                background-position:center top;
                }
            <?php }
        }
        ?>
		.pin{
			background: <?php echo esc_attr( $themeClr ); ?>;
		}
		.features li .tick-icon{
			color: <?php echo esc_attr( $themeClr ); ?>;
		}
        .call-to-action2 h1{
        background-image:url(<?php echo get_template_directory_uri(); ?>/assets/images/new/hb.png);
        background-repeat:no-repeat;
        background-position:center center;
        }
        .listing-app-view .admin-top-section .user-details .user-portfolio,.listing-app-view .user-detail-wrap{
        background-image:url(<?php echo esc_attr( $app_view_menu_header_img ); ?>);
        background-repeat:no-repeat;
        background-position:center center;
        }
        .lp-blog-style2-inner .lp-blog-grid-author li .fa,.lp-blog-grid-author2 li .fa,
        .listing-app-view .footer-app-menu ul li a:hover,.listing-app-view .small-scrren-app-view .sign-login-wrap a,
        .listing-app-view .small-scrren-app-view .add-listing-btn,
        .googledroppin,.googledroppin:hover{
        color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .city-girds2 .city-thumb2  .category-style3-title-outer,.single-tabber2 ul .active a:after,.waycheckoutModal .btn-default:hover,
        .list_view .lp-grid-box-bottom .lp-nearest-distance,.grid_view2 .lp-grid-box-bottom .lp-nearest-distance{
        background: <?php echo esc_attr( $themeClr ); ?>;
        }
        .single-tabber2 ul li a:hover,.single-tabber2 ul .active a,#sidebar aside ul li a:hover,#sidebar .jw-recent-posts-widget ul li .jw-recent-content a:hover,
        .blog-read-more-style2 a,.blog-read-more a{
        color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .waycheckoutModal .btn-default:hover{
        border-color:<?php echo esc_attr( $themeClr ); ?> !important;
        }
        .blog-detail-link,.listing-app-view .footer-app-menu ul li:hover{
        color: <?php echo esc_attr( $themeClr ); ?> !important;
        border-color:<?php echo esc_attr( $themeClr ); ?> !important;
        }
        .blog-detail-link:hover,.video-bottom-search-content,.listing-app-view.login-form-pop-tabs{
        background-color: <?php echo esc_attr( $themeClr ); ?> !important;
        }
        #sidebar aside h2.widget-title:after{
        background: <?php echo esc_attr( $themeClr ); ?>;
        }
        .app-view-header .lp-menu-bar,.slider-handle,.tooltip-inner{
        background: <?php echo esc_attr( $themeClr ); ?>;
        }
        .review-secondary-btn,.blog-read-more a:hover,
        .lp-tooltip-outer .lp-tool-tip-content .sortbyrated-outer ul li .active{
        background: <?php echo esc_attr( $themeClr ); ?> !important;
        border-color: <?php echo esc_attr( $themeClr ); ?> !important;
        }
        .location-filters-wrapper #distance_range_div input[type=range]::-webkit-slider-thumb,#distance_range_div_btn a:hover,.location-filters-wrapper #distance_range_div input[type=range]::-moz-range-thumb,.location-filters-wrapper #distance_range_div input[type=range]::-ms-thumb{
        background:<?php echo esc_attr( $themeClr ); ?> !important;
        }
        .tooltip.top .tooltip-arrow{
        border-top-color: <?php echo esc_attr( $themeClr ); ?>;
        }
        input:checked + .slider{
        background-color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .listing-app-view .app-view-filters .close{
        border-color: <?php echo esc_attr( $themeClr ); ?> !important;
        color: <?php echo esc_attr( $themeClr ); ?>;
        }
        .listing-app-view .app-view-filters .close:hover{
        background: <?php echo esc_attr( $themeClr ); ?>;
        }
        .listing-app-view .small-scrren-app-view .mm-listview a:hover,.listing-app-view .small-scrren-app-view .mm-listview a:focus,body .list_view a.lp-add-to-fav.remove-fav:hover span{
        color: <?php echo esc_attr( $themeClr ); ?> !important;
        }
        <?php
        if(!empty($banner_opacity)){ ?>
            .lp-header-overlay,.page-header-overlay, .lp-home-banner-contianer-inner-video-outer{
            background-color: rgba(0, 0, 0, <?php echo esc_html($banner_opacity);?>);
            }
        <?php }
        ?>
        <?php
        if(!empty($banner_height)){ ?>
            .lp-home-banner-contianer {
            height: <?php echo esc_html($banner_height);?>px;
            }
        <?php }
        //banner height bottom view
        if(!empty($banner_height_bottom)) { ?>
            .lp-home-banner-contianer-1 {
            height: <?php echo esc_html($banner_height_bottom);?>px;
            }
        <?php } ?>
        .lp-list-view-edit li a:hover, .review-post p i, .lp-header-full-width.lp-header-bg-grey .lp-add-listing-btn li a:hover,
        .lp-header-full-width.lp-header-bg-grey .lp-add-listing-btn li a, .lp-header-bg-grey .navbar-toggle, .lp-search-bar-all-demo .add-more,
        .lp-bg-grey .lp-search-bar-left .border-dropdown .chosen-container-single span::after, .lp-right-grid .add-more,
        .lp-search-bar-all-demo .add-more, .lp-right-grid .add-more, .video-option > h2 > span:first-of-type i, .count-text.all-listing,
        .lp-bg-grey .lp-search-bar-left .border-dropdown .chosen-container-single span::after, a.watch-video.popup-youtube,
        .dashboard-content .tab-content.dashboard-contnt h4 a, .campaign-options ul li i.fa-bar-chart, .email-address,
        body .grid_view2 a.add-to-fav.lp-add-to-fav.simptip-position-top.simptip-movable:hover > i, .wpb_wrapper > ul > li::before,
        body .grid_view2 a.add-to-fav.lp-add-to-fav.simptip-position-top.simptip-movable:hover > span, .lp-h4 a:hover,
        .promote-listing-box .texual-area > ul li i, .row.invoices-company-details a:hover, .checkout-bottom-area ul.clearfix > li > a:hover,
        .lp-all-listing span.count > p, .lp-all-listing span.count, h1 a:hover, h2 a:hover, h3 a:hover, h4 a:hover, h5 a:hover, h6 a:hover,
        .lp-h1 a:hover, .lp-h2 a:hover, .lp-h3 a:hover, .lp-h5 a:hover, .lp-h6 a:hover, .lp-blog-grid-category a:hover,
        .lp-blog-grid-title h4 a:hover, .footer-menu li a:hover, .post-rice, .tags-container li a label, .tags-container li a:hover span,
        .ui-accordion .ui-accordion-header span, .post-stat .fa-star, .listing-page-result-row p a:hover, p a.achor-color,
        .blog-tags ul li a:hover, .post-meta-left-box .breadcrumbs li a:hover, .post-meta-right-box .post-stat li a:hover,
        .parimary-link:hover, .secodary-link, blockquote::after, .lp-blockquote::after, .colored, .lp-add-listing-btn ul li a:hover,
        .listing-second-view .post-meta-right-box .post-stat a.add-to-fav:hover, .lp-list-view-paypal-inner h4:hover,
        .listing-second-view .post-meta-right-box .post-stat a.add-to-fav:hover span, .overlay-video-thumb:hover i,
        body .lp-grid-box-contianer a.add-to-fav.lp-add-to-fav.simptip-position-top.simptip-movable:hover i,
        .bottom-links a,.lp-list-view-content-upper h4:hover, .lp-blog-grid-author li a:hover, .lp-blog-grid-author li:hover,
        .dashboard-content .lp-pay-options .lp-promotebtn:hover, .dashboard-content .lp-pay-options .lp-promotebtn,
        .tags-container li a span.tag-icon, .lp-grid-box-price li > a:hover, .lp-grid-box-bottom .pull-left a:hover,
        .tags-container li a:hover span, .menu ul.sub-menu li:hover > a, .menu ul.children li:hover > a,
        .post-stat li a:hover, .lp-tabs .lp-list-view .lp-list-view-content-upper h4:hover, .lp-tabs .lp-list-view .lp-list-view-paypal-inner h4:hover,
        .post-reviews .fa-star, .listing-second-view .map-area .listing-detail-infos ul li a:hover > span,
        .widget-contact-info .list-st-img li a:hover, .get-directions > a:hover, body .grid_view2 a.add-to-fav.lp-add-to-fav:hover span,
        ul.post-stat li > a:hover > span i, .lp-grid-box-left.pull-left > ul > li > a:hover,
        .grid_view2 .lp-post-quick-links > li a:hover, .list_view .lp-post-quick-links > li a:hover,
        .lp-grid-box-description h4.lp-h4 > a:hover, body .list_view a.add-to-fav.lp-add-to-fav:hover span,
        body .list_view a.add-to-fav.lp-add-to-fav:hover, .grid_view2 .lp-post-quick-links > li a:hover > i,
        .list_view .lp-post-quick-links > li a:hover > i, .list_view .lp-post-quick-links > li a > i:hover,
        .listing-second-view .features.list-style-none > li a:hover > i, .listing-second-view .features li > a:hover span i,.lp-classic-reset-filters ,
		.listingpro-classic-promotional-section .classic-box .classic-text .classic-promotional-box-url,
		.lp-price-free, 
.lp-join-now ul.lp-user-menu > li:hover > a, .listing-second-view .claim-area a.phone-number.md-trigger.claimformtrigger2:hover,.post-meta-right-box .post-stat li a:hover i,
		.lp-activities-classicx-style .lp-section-content-container li.slick-active button:before,
		.lp-section-content-container-boxed .slick-dots li.slick-active button:before,
		.classic-reivew-grid-outer-boxed .classic-reivew-grid-content .lp-activity-review-writer-description .lp-classic-boxed-reviews-content a:hover,
		.lp-blog-grid-box-container-classic-style .lp-blog-grid-box-description-bottom .lp-blog-grid-author a:hover,
		.lp-blog-grid-box-container-classic-style .lp-blog-grid-title a:hover,
		.classic-reivew-grid-outer-boxed .classic-reivew-grid-content .lp-activity-review-writer-description .lp-activity-review-writer a:hover,
		.lp-blog-grid-box-container-classic-style:hover .lp-blog-grid-category a:hover,
		.classic-reivew-grid-outer-boxed .classic-reivew-grid-content .classic-reivew-grid-listing-title-boxed h3 a:hover,
				.listing-view-layout > ul li a.active, .listing-view-layout > ul li a:hover, .listing-style4 #lp-find-near-me .near-me-btn.active,.widget-	box.business-contact.classic-business-lead-form.lp-lead-form-st .user_texts .status a:hover{
        color: <?php echo esc_attr( $themeClr ); ?> !important;
        }
		.post-meta-info-classic  .post-meta-right-box .post-stat li a:hover,
        .dashboard-tabs ul li ul li.active a, .post-meta-info-classic .post-meta-right-box .post-stat li a:hover i{
        	 color: <?php echo esc_html($themeClr); ?> !important;
        }
        .ui-tooltip, .md-closer, .post-submit .ui-tabs .ui-tabs-nav li a, #success span p, .lp-list-view-paypal,
        .lp-listing-form input[type=radio]:checked + label::before, .lp-listing-form input[type=submit], .lp-invoice-table tr td a,
        .lp-modal-list .lp-print-list, .lp-tabs .lp-pay-publsh, .lp-dropdown-menu ul li a:hover,
        .listing-second-view .online-booking-form > a.onlineform.active, .listing-second-view .online-booking-form > a.onlineform:hover,
        .listing-second-view .listing-post article figure figcaption .bottom-area .listing-cats, .top-heading-area,
        .lp-dropdown-menu ul li a:hover,
        .listing-second-view .online-booking-form .booking-form input[type="submit"], .lp-price-main .lp-title,
        .ui-datepicker-header.ui-widget-header.ui-helper-clearfix.ui-corner-all, .calendar-month-header,
        .lp-search-bar-all-demo .lp-search-btn:hover, .lp-bg-grey .input-group-addon, .lp-search-bar-all-demo .lp-search-btn:hover,
        .lp-bg-grey .input-group-addon, .hours-select > li > button.add-hours, .typeahead__container .typeahead__button > button,
        .form-group .lp-search-bar-right, a.watch-video.popup-youtube:hover, .active-packages-area .table-responsive .top-area,
        .lp-grid-box-contianer .md-close i:hover, .listing-second-view a.secondary-btn.make-reservation,
        .list-st-img.list-style-none li a.edit-list:hover, .mm-menu .mm-navbars_top .mm-navbar, .lp-user-menu li a:hover,
        .fc-widget-content .fc-content-skeleton .fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end:hover,
        .lp-primary-btn:hover, .lp-search-btn, .lp-home-categoires li a:hover, .lp-post-quick-links li a.icon-quick-eye,
        .md-close i, .menu ul.sub-menu li a:hover, .menu ul.children li a:hover, .user-portfolio-stat ul li i,
        .lp-submit-btn:hover, .secondary-btn, .list-st-img li a:hover, .price-plan-box, .btn-first-hover, .btn-second-hover:hover,
        .ui-autocomplete li:hover, .tes-icon i, .menu ul.sub-menu li:hover > a, .menu ul.children li:hover > a,	.mm-listview .mm-next,
        .mm-navbar-size-1 a, .mm-listview a:hover, .active-tag:hover, .dashboard-content .lp-pay-options .lp-promotebtn:hover,
        .double-bounce1, .double-bounce2, [data-tooltip].simptip-position-top::after,
        [data-tooltip].simptip-position-top::after, [data-tooltip].simptip-position-bottom::after,
        [data-tooltip].simptip-position-left::after, [data-tooltip].simptip-position-right::after,
        .menu ul.children li > a::before, .menu ul.sub-menu li > a::before, .lp-user-menu li > a::before,
        .currency-signs > ul > li > a.active, .search-filters > ul > li > a.active,div#lp-find-near-me ul li a.active,
        .select2-container--default .select2-results__option--highlighted[aria-selected], .bookingjs-form .bookingjs-form-button:hover, a.googleAddressbtn:hover, a.googleAddressbtn.active, .lp-recurring-button-wrap input[type=checkbox]:checked + label::before {
        background-color: <?php echo esc_html($themeClr); ?>;
        }
        a.lp-change-plan-btn:hover {
        background-color: <?php echo esc_html($themeClr); ?> !important;
        border-color: <?php echo esc_html($themeClr); ?> !important;
        }
		.lp-header-style-classic #input-dropdown li:hover span,
		.lp-header-style-classic #searchlocation_chosen.chosen-container .chosen-results li.highlighted,
		.lp-header-style-classic #searchlocation_chosen.chosen-container .chosen-results li.result-selected.highlighted,
		.lp-header-style-classic #input-dropdown>ul li:hover,
			.classic-search .active-result.highlighted,
		.classic-search .active-result.result-selected.highlighted,
		.lp-search-bar.classic-search #input-dropdown li:hover,
        .lp-tabs .panel-heading li.active a, .ui-state-default.ui-state-highlight {
        background-color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-grid-box-price .category-cion a, .ui-state-default.ui-state-highlight, .lp-header-full-width.lp-header-bg-grey .lp-add-listing-btn li a:hover,
        .lp-header-full-width.lp-header-bg-grey .lp-add-listing-btn li a, .lp-header-bg-grey .navbar-toggle, .lp-bg-grey .lp-interest-bar input[type="text"],
        .lp-bg-grey .chosen-container .chosen-single, .lp-bg-grey .lp-interest-bar input[type="text"], .lp-bg-grey .chosen-container .chosen-single,
        a.watch-video.popup-youtube, .listing-second-view a.secondary-btn.make-reservation,
        .fc-widget-content .fc-content-skeleton .fc-day-grid-event.fc-h-event.fc-event.fc-start.fc-end,
        .lpmap-icon-contianer, .dashboard-content .lp-pay-options .lp-promotebtn,.currency-signs > ul > li > a.active,
        .listing-view-layout > ul li a.active, .listing-view-layout > ul li a:hover, .search-filters > ul > li > a.active, div#lp-find-near-me ul li a.active {
        border-color: <?php echo esc_html($themeClr); ?>;
        }
        .ui-autocomplete li:hover {
        border-color: <?php echo esc_html($themeClr); ?> !important;
        }
        a.googleAddressbtn.active::after,
        .ui-tooltip::after {
        border-top-color: <?php echo esc_html($themeClr); ?>;
        }
        .dashboard-content .lp-main-tabs .nav-tabs > li.active > a, [data-tooltip].simptip-position-left::before,
        .dashboard-content .lp-main-tabs .nav-tabs > li a:hover, .dashboard-tabs.lp-main-tabs.text-center  ul  li  a.active-dash-menu {
        border-left-color: <?php echo esc_html($themeClr); ?>;
        }
        .lpmap-icon-shape.cardHighlight::after, [data-tooltip].simptip-position-top::before {
        border-top-color: <?php echo esc_html($themeClr); ?> !important;
        }
        .dashboard-tabs.lp-main-tabs.text-center > ul > li.opened:hover > a,
        .dashboard-tabs.lp-main-tabs.text-center > ul > li:hover > a,
        .dashboard-tabs.lp-main-tabs.text-center ul li a.active-dash-menu {
        border-left-color: <?php echo esc_html($themeClr); ?> !important;
        }
        [data-tooltip].simptip-position-right::before, [data-tooltip].simptip-position-top.half-arrow::before,
        [data-tooltip].simptip-position-bottom.half-arrow::before {
        border-right-color: <?php echo esc_html($themeClr); ?> !important;
        }
        [data-tooltip].simptip-position-top::before {
        border-top-color: <?php echo esc_html($themeClr); ?> !important;
        }
        [data-tooltip].simptip-position-bottom::before {
        border-bottom-color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-primary-btn, .dashboard-tabs, .nav-tabs > li > a:hover,
        .nav-tabs > li.active > a, .nav-tabs > li.active > a:hover, .nav-tabs > li.active > a:focus, .lp-submit-btn, .secondary-btn:hover,
        .list-st-img li a, .btn-first-hover:hover, .btn-second-hover, .about-box-icon, .upload-btn:hover, .chosen-container .chosen-results li.highlighted,
        .secondary-btn:active, .lp_confirmation .list-st-img li a.edit-list, .secondary-btn:focus, .resurva-booking .hidden-items input.lp-review-btn,
        input.lp-review-btn:hover, .dashboard-content .lp-list-page-list .lp-list-view .lp-rigt-icons .remove-fav i:hover,
        .lp-topbar, .lp-home-categoires li a, .lp-grid-box-bottom,  .form-group .lp-search-bar-right:hover,
        .post-submit .ui-tabs .ui-tabs-nav li.ui-state-active a, .lp-list-pay-btn a:hover, .lp-modal-list .lp-print-list:hover,
        .listing-second-view .online-booking-form > a.onlineform, .listing-second-view .contact-form ul li input[type="submit"],
        .listing-second-view .online-booking-form .booking-form, .listing-second-view .ask-question-area > a.ask_question_popup,
        .widget-box.business-contact .contact-form.quickform form.form-horizontal .form-group.pos-relative:hover input.lp-review-btn,
        .listing-second-view a.secondary-btn:hover, .submit-images:hover > a.browse-imgs, .lp-price-main .lp-upgrade-color,
        .lp-price-main .lp-upgrade-color:hover, .featured-plan .lp-price-free.lp-without-prc.btn,
        .hours-select > li > button.add-hours:hover, .dashboard-content .postbox table.widefat a.see_more_btn:hover,
        #input-dropdown li:hover span, #input-dropdown li:hover a, #input-dropdown li:hover, .thankyou-panel ul li a:hover,
        .dashboard-content .promotional-section a.lp-submit-btn:hover, .widget-box.reservation-form a.make-reservation,
        .dashboard-content .lp-pay-options .promote-btn.pull-right:hover, .lp-dashboard-right-panel-listing ul li a.reply:hover,
        .dashboard-content .lp-list-view-content-bottom ul.lp-list-view-edit > li > a:hover,
        .dashboard-content .lp-list-view-content-bottom ul.lp-list-view-edit > li > a:hover > span, .form-group.mr-bottom-0 > a.md-close:hover,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li input.lp-review-btn:hover, .lp-contact-support .secondary-btn:hover,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li a.edit-list:hover,
        .dashboard-content .user-recent-listings-inner .lp-list-page-list .remove-fav.md-close:hover,
        .resurva-booking .lp-list-view-inner-contianer ul li form:hover > span, .listing-second-view a.secondary-btn.make-reservation:hover,
        .dashboard-content .lp-pay-options .promotebtn:hover,
        #select2-searchlocation-results .select2-results__option.select2-results__option--highlighted, .bookingjs-form .bookingjs-form-button, a.googleAddressbtn {
        background-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .lp-tabs .lp-pay-publsh:hover,.lp_payment_step_next.active:hover {
        background-color: <?php echo esc_html($secThemeClr); ?> !important;
        }
        input, .form-group label, .post-stat  li,
        .post-stat  li a, .listing-page-result-row p a, p a.achor-color:hover,
        .form-group label, .blog-tags ul li a, .post-meta-left-box .breadcrumbs li a, .post-meta-left-box .breadcrumbs li span,
        .tags-container li a span, .price-plan-content ul li span, .paragraph-form, .form-review-stars li i, .form-review-stars li a ,
        .post-meta-right-box .post-stat li a, .parimary-link, .secodary-link:hover, blockquote, .upload-btn, input.lp-review-btn,
        .lp-blockquote, .listing-second-view a.secondary-btn i, .bottom-links a:hover, .resurva-booking .hidden-items input.lp-review-btn:hover,
        .lp-menu .has-menu > a::after, .listing-second-view .post-meta-right-box .post-stat a.secondary-btn i, a.browse-imgs,
        .listing-second-view a.secondary-btn, .listing-second-view .contact-form ul li input[type="submit"]:hover,
        .listing-second-view .features li span i, .listing-second-view .post-meta-right-box .post-stat > li > a span.email-icon,
        .dashboard-content .tab-content.dashboard-contnt .ui-sortable-handle, .thankyou-panel ul li a,
        .dashboard-content .postbox table.widefat a.see_more_btn, .dashboard-content .promotiona-text > h3,
        .dashboard-content .lp-face.lp-front.lp-pay-options > h3, .dashboard-content .lp-face.lp-dash-sec > h4,
        .dashboard-content .lp-pay-options .lp-promotebtn, .dashboard-content .promote-btn.pull-right::before,
        .dashboard-content .lp-list-view-content-bottom ul.lp-list-view-edit > li > a, .lp-dashboard-right-panel-listing ul li a.reply,
        .dashboard-content .lp-list-view-content-bottom ul.lp-list-view-edit > li > a > span,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li input.lp-review-btn,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li a.edit-list,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li input.lp-review-btn::before,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li a.edit-list::before, .form-group.mr-bottom-0 > a.md-close,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li a.edit-list > span, .lp-contact-support .secondary-btn i,
        .widget-box.business-contact .contact-form.quickform form.form-horizontal .form-group.pos-relative i.lp-search-icon,
        .dashboard-content .user-recent-listings-inner .lp-list-page-list .remove-fav.md-close > span,
        .dashboard-content .lp-list-page-list .lp-list-view .remove-fav i,
        .resurva-booking ul li.clearfix > form#booking > span, .dashboard-content .lp-pay-options .promotebtn {
        color: <?php echo esc_html($secThemeClr); ?>;
        }
        .nav-tabs > li > a::after{
        border-bottom-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .upload-btn, .listing-second-view a.secondary-btn, .listing-second-view .contact-form ul li input[type="submit"],
        input.lp-review-btn, a.browse-imgs,  .dashboard-content .postbox table.widefat a.see_more_btn,
        .dashboard-content .lp-pay-options .promote-btn.pull-right, .widget-box.reservation-form a.make-reservation,
        .thankyou-panel ul li a, .dashboard-content .lp-list-view-content-bottom ul.lp-list-view-edit > li > a,
        .lp-dashboard-right-panel-listing ul li a.reply, .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li input.lp-review-btn,
        .lp-rigt-icons.lp-list-view-content-bottom .list-st-img li a.edit-list,
        .dashboard-content .user-recent-listings-inner .lp-list-page-list .remove-fav.md-close, .form-group.mr-bottom-0 > a.md-close,
        .lp-contact-support .secondary-btn:hover, .resurva-booking .lp-list-view-inner-contianer ul li form > span,
        .listing-second-view a.secondary-btn.make-reservation:hover, .dashboard-content .lp-pay-options .promotebtn {
        border-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .menu ul.children li:hover > a, .menu ul.sub-menu li:hover > a, .lp-user-menu li:hover > a,
        .grid_view2 .lp-post-quick-links > li a, .list_view .lp-post-quick-links > li a {
        background-color: transparent !important;
        }
        .lp-coupns-btns:hover,
        .lp-menu-step-two-btn button:hover,
        .lp-menu-save-btns button:hover,
        .lp-form-feild-btn:hover {
        background-color: <?php echo esc_html($themeClr); ?>;
        }
        .lp-menu-step-two-btn button:hover,
        .lp-menu-save-btns button:hover,
        .lp-form-feild-btn:hover {
        background-color: <?php echo esc_html($themeClr); ?>;
        }
		<?php if ($pff != '' ) { ?>
            .lp_all_page_overflow h3,
            .lp_all_page_overflow .lp_extrafields_select-border2 .filter_checkbox_container,
             .page-container:not(.lp-sidebar-filters-style) .outer_all_page_overflow .lp_all_page_overflow h2{
                <?php if ($pff != '') { ?>
                    font-family: <?php echo esc_attr($pff); ?>;
                <?php } ?>
        }
        <?php } ?>
        .lp-tool-tip-content p:hover,
        .lp-tooltip-div ul li:hover a,
        .lp-tooltip-outer .lp-tool-tip-content .sortbyrated-outer ul li:hover a {
            background: <?php echo esc_html($themeClr); ?>;
            color:#fff !important;
         }
        .lp-menu-step-two-btn button span,
        .lp-menu-step-two-btn button,
        .lp-step-icon i,
        .lp-choose-menu .lp-listing-selecter-drop a,
        .lp-menu-container-outer .ui-tabs .ui-tabs-nav .ui-tabs-active a,.lp-header-togglesa .listing-view-layout-v2 ul li .active .fa{
        color: <?php echo esc_html($themeClr); ?>;
        }
		.lp-price-free,
        .lp-menu-step-two-btn button,.lp-header-togglesa .listing-view-layout-v2 ul li .active .fa,.lp-header-togglesa .listing-view-layout-v2 ul li .fa:hover,.listing-page-sidebar .lp-listing-price-range .claim-area a.phone-number{
        border: 1px solid <?php echo esc_html($themeClr); ?>;
        }
        .lp-form-feild-btn,
        .lp-location-picker-outer .active,.lp-listing-price-range #lp-report-listing a,#lp-find-near-me .near-me-btn:hover,.listing-simple .post-with-map-container-right #lp-find-near-me a.active:hover,.listing-with-map .post-with-map-container-right #lp-find-near-me a.active:hover{
        border-color: <?php echo esc_html($themeClr); ?>;
        }
        .lp-left-content-container a:hover,
        .lp-form-feild-btn,.lp-header-togglesa .listing-view-layout-v2 ul li .fa:hover,.lp-listing-review .lp-review-left .lp-review-name{
        color: <?php echo esc_html($themeClr); ?>;
        }
 	.lp-price-main .lp-without-prc:hover,
        .lp-review-reply-btn,.lp-dashboard-new .sidebar-nav>.sidebar-brand,.lp-public-reply-btn a:hover,
        .lp-public-reply-btn a:focus{
        background-color: <?php echo esc_html($themeClr); ?>;
        }
	.lp-price-main .lp-without-prc:hover{
			color:#fff !important;
		}
	.lp-classic-call-to-action-started-button,
        .lp-add-new-btn,
        .lp-let-start-btn,
        .lp-location-picker-outer .active,.arrow-left,.arrow-right,.lp-listing-menu-items h6:after{
        background: <?php echo esc_html($themeClr); ?>;
        }
        .lp-listings.list-style .lp-listing .lp-listing-bottom .lp-listing-cat,
        .lp-listings.grid-style .lp-listing-location a:hover,.lp-listings.grid-style .lp-listing-location:hover .fa,.listing-page-sidebar .lp-listing-price-range .claim-area a.phone-number,.grid-style .lp-listing-cat,
        .listing-slider .lp-listing .lp-listing-bottom-inner .lp-listing-cats a:hover{
        color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-sorting-filter-outer .active,.lp-sorting-filter-outer .active:hover,.lp-sorting-filter-outer li a:hover,.lp-listing-price-range #lp-report-listing a{
        border-bottom:1px solid <?php echo esc_html($themeClr); ?>!important;
        color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-sorting-filter-outer .active .fa,.lp-header-search-filters .more-filters,.lp-sorting-filter-outer li:hover a,.lp-sorting-filter-outer li:hover a .fa,#lp-find-near-me .near-me-btn:hover{
        color: <?php echo esc_html($themeClr); ?> !important;
        }
        .lp-listing-price-range .claim-area a.phone-number:hover,
        .lp-listing-price-range #lp-report-listing a:hover{
        border-color: <?php echo esc_html($themeClr); ?>;
        background-color: <?php echo esc_html($themeClr); ?>;
        }
        .hidding-form-feilds .form-group input[type="text"]:focus,
        .hidding-form-feilds .form-group input[type="email"]:focus,
        .hidding-form-feilds .form-group textarea:focus,
        .hidding-form-feilds .form-group select:focus {
        border-left: 3px solid <?php echo esc_html($themeClr); ?>;
        }
        .review-submit-btn:hover,.lp-listing-leadform-inner .form-horizontal input[type="submit"]:hover,.lp-header-search .price-filter ul li:hover,.lp-header-search .form-group .lp-search-bar-right:hover .lp-search-form-submit{
        background-color:<?php echo esc_attr( $rgba ); ?>;
        }
        <?php
        if( $headerBgcolor == '#ffffff' )
        {
            ?>
            body.home .lp-header-full-width .lp-add-listing-btn ul li a:hover,
            body.home .lp-header-add-btn a:hover{
            color: #fff;
            background-color: #333;
            border-color: #333 !important;
            }
            <?php
        }
        if( $headerBgcolorInner == '#ffffff' )
        {
            ?>
            body:not(.home) .lp-header-full-width .lp-add-listing-btn ul li a:hover,
            body:not(.home) .lp-header-add-btn a:hover{
            color: #fff;
            background-color: #333;
            border-color: #333 !important;
            }
            <?php
        }
        ?>
        .lp-header-full-width .lp-add-listing-btn ul li a:hover,
        .lp-header-middle .lp-header-add-btn a:hover{
        background-color: #fff;
        color: #333;
        border-color: #fff;
        }
        .lp-header-search .price-filter ul li .active,.lp-header-search .price-filter ul li a:hover,.lp-header-search .price-filter ul li:last-child .active,.lp-header-search .price-filter ul li:last-child a:hover,.lp-header-search-filters .open-now-filter .active,.lp-header-search-filters .open-now-filter a:hover{
        border-color: <?php echo esc_html($themeClr); ?> !important;
        background-color: <?php echo esc_html($themeClr); ?>;
        }
        .lp-booking-section-time-pill-1,.lp-booking-section-time-pill-2{
        border-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .lp-booking-section-time-pill-hover:hover,.lp-booking-form-input-confirm{
        background-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .lp-booking-section-footer-view-switch:hover , .lp-booking-send-request-success{
        color: <?php echo esc_html($secThemeClr); ?>;
        }
        .lp-booking-form-input:focus {
        border-left-color: <?php echo esc_html($secThemeClr); ?>;
        }
        .lp-booking-section .ui-datepicker a.ui-state-active,.ui-state-default.ui-state-highlight{
        border-color: <?php echo esc_html($secThemeClr); ?>;
        background-color: <?php echo esc_html($secThemeClr); ?> !important;
        color:#fff;
        border-collapse: collapse;
        }
        <?php if( $nav_ff != '' || $nav_fz != '' || $nav_fw != '' || $nav_col != '' ) { ?>
            .lp-menu-container .lp-menu > div > ul > li > a{
            <?php if($nav_ff != '') { ?>
                font-family: <?php echo esc_attr($nav_ff); ?>;
            <?php } ?>
            <?php if($nav_fz != '') { ?>
                font-size:<?php echo esc_attr($nav_fz); ?>;
            <?php } ?>
            <?php if($nav_fw != '') { ?>
                font-weight:<?php echo esc_attr($nav_fw); ?>;
            <?php } ?>
            <?php if($nav_col != '') { ?>
                color:<?php echo esc_attr($nav_col); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $bodyff != '' || $bodyfz != '' || $bodyfw != '' || $bodycol != '' || $bodylh != '' ) { ?>
            body{
            <?php if($bodyff != '') { ?>
                font-family: <?php echo esc_attr($bodyff); ?>;
            <?php } ?>
            <?php if($bodyfz != '') { ?>
                font-size:<?php echo esc_attr($bodyfz); ?>;
            <?php } ?>
            <?php if($bodyfw != '') { ?>
                font-weight:<?php echo esc_attr($bodyfw); ?>;
            <?php } ?>
            <?php if($bodycol != '') { ?>
                color:<?php echo esc_attr($bodycol); ?>;
            <?php } ?>
            <?php if($bodylh != '') { ?>
                line-height:<?php echo esc_attr($bodylh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h1ff != '' || $h1fz != '' || $h1fw != '' || $h1col != '' || $h1lh != '' ) { ?>
            h1, h1 a, .lp-h1, .lp-h1 a {
            <?php if($h1ff != '') { ?>
                font-family: <?php echo esc_attr($h1ff); ?>;
            <?php } ?>
            <?php if($h1fz != '') { ?>
                font-size:<?php echo esc_attr($h1fz); ?>;
            <?php } ?>
            <?php if($h1fw != '') { ?>
                font-weight:<?php echo esc_attr($h1fw); ?>;
            <?php } ?>
            <?php if($h1col != '') { ?>
                color:<?php echo esc_attr($h1col); ?>;
            <?php } ?>
            <?php if($h1lh != '') { ?>
                line-height:<?php echo esc_attr($h1lh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h2ff != '' || $h2fz != '' || $h2fw != '' || $h2col != '' || $h2lh != '' ) { ?>
            h2, h2 a, .lp-h2, .lp-h2 a {
            <?php if($h2ff != '') { ?>
                font-family: <?php echo esc_attr($h2ff); ?>;
            <?php } ?>
            <?php if($h2fz != '') { ?>
                font-size:<?php echo esc_attr($h2fz); ?>;
            <?php } ?>
            <?php if($h2fw != '') { ?>
                font-weight:<?php echo esc_attr($h2fw); ?>;
            <?php } ?>
            <?php if($h2col != '') { ?>
                color:<?php echo esc_attr($h2col); ?>;
            <?php } ?>
            <?php if($h2lh != '') { ?>
                line-height:<?php echo esc_attr($h2lh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h3ff != '' || $h3fz != '' || $h3fw != '' || $h3col != '' || $h3lh != '' ) { ?>
            h3, h3 a, .lp-h3, .lp-h3 a {
            <?php if($h3ff != '') { ?>
                font-family: <?php echo esc_attr($h3ff); ?>;
            <?php } ?>
            <?php if($h3fz != '') { ?>
                font-size:<?php echo esc_attr($h3fz); ?>;
            <?php } ?>
            <?php if($h3fw != '') { ?>
                font-weight:<?php echo esc_attr($h3fw); ?>;
            <?php } ?>
            <?php if($h3col != '') { ?>
                color:<?php echo esc_attr($h3col); ?>;
            <?php } ?>
            <?php if($h3lh != '') { ?>
                line-height:<?php echo esc_attr($h3lh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h4ff != '' || $h4fz != '' || $h4fw != '' || $h4col != '' || $h4lh != '' ) { ?>
            h4, .lp-h4, h4 a, .lp-h4 a {
            <?php if($h4ff != '') { ?>
                font-family: <?php echo esc_attr($h4ff); ?>;
            <?php } ?>
            <?php if($h4fz != '') { ?>
                font-size:<?php echo esc_attr($h4fz); ?>;
            <?php } ?>
            <?php if($h4fw != '') { ?>
                font-weight:<?php echo esc_attr($h4fw); ?>;
            <?php } ?>
            <?php if($h4col != '') { ?>
                color:<?php echo esc_attr($h4col); ?>;
            <?php } ?>
            <?php if($h4lh != '') { ?>
                line-height:<?php echo esc_attr($h4lh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h5ff != '' || $h5fz != '' || $h5fw != '' || $h5col != '' || $h5lh != '' ) { ?>
            h5, .lp-h5, h5 a, .lp-h5 a {
            <?php if($h5ff != '') { ?>
                font-family: <?php echo esc_attr($h5ff); ?>;
            <?php } ?>
            <?php if($h5fz != '') { ?>
                font-size:<?php echo esc_attr($h5fz); ?>;
            <?php } ?>
            <?php if($h5fw != '') { ?>
                font-weight:<?php echo esc_attr($h5fw); ?>;
            <?php } ?>
            <?php if($h5col != '') { ?>
                color:<?php echo esc_attr($h5col); ?>;
            <?php } ?>
            <?php if($h5lh != '') { ?>
                line-height:<?php echo esc_attr($bodylh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $h6ff != '' || $h6fz != '' || $h6fw != '' || $h6col != '' || $h6lh != '' ) { ?>
            h6, .lp-h6, h6 a, .lp-h6 a {
            <?php if($h6ff != '') { ?>
                font-family: <?php echo esc_attr($h6ff); ?>;
            <?php } ?>
            <?php if($h6fz != '') { ?>
                font-size:<?php echo esc_attr($h6fz); ?>;
            <?php } ?>
            <?php if($h6fw != '') { ?>
                font-weight:<?php echo esc_attr($h6fw); ?>;
            <?php } ?>
            <?php if($h6col != '') { ?>
                color:<?php echo esc_attr($h6col); ?>;
            <?php } ?>
            <?php if($h6lh != '') { ?>
                line-height:<?php echo esc_attr($h6lh); ?>;
            <?php } ?>
            }
        <?php } ?>
        <?php if( $pff != '' || $pfz != '' || $pfw != '' || $pcol != '' || $plh != '' ) { ?>
            p,span,input,.post-detail-content,li a,.show a,.lp-grid-box-description ul,.chosen-container,.accordion-title,.lp-grid-box-bottom a,time,label,#input-dropdown li a,#input-dropdown span, .lpdoubltimes em {
            <?php if($pff != '') { ?>
                font-family: <?php echo esc_attr($pff); ?>;
            <?php } ?>
            <?php if($pfz != '') { ?>
                font-size:<?php echo esc_attr($pfz); ?>;
            <?php } ?>
            <?php if($pfw != '') { ?>
                font-weight:<?php echo esc_attr($pfw); ?>;
            <?php } ?>
            <?php if($pcol != '') { ?>
                color:<?php echo esc_attr($pcol); ?>;
            <?php } ?>
            <?php if($plh != '') { ?>
                line-height:<?php echo esc_attr($plh); ?>;
            <?php } ?>
            }
        <?php } ?>
        .home-categories-area .banner-default-view-category2 li a {
            background: transparent !important;
        }
        .home-categories-area .banner-default-view-category2 li a:hover p#cat-img-bg {
            background: <?php echo esc_html($categories_background_hover_color); ?> !important;
        }
        .lp-upper-cat-view3.lp-home-categoires li a {
            background: transparent !important;
        }
        .new-banner-category-view2.lp-home-categoires li a {
            background: transparent !important;
        }
		<?php /* classic new style */ ?>
		<?php if ($pff != '' ) { ?>
            .lp_all_page_overflow h3,
            .lp_all_page_overflow .lp_extrafields_select-border2 .filter_checkbox_container,
             .page-container:not(.lp-sidebar-filters-style) .outer_all_page_overflow .lp_all_page_overflow h2{
                <?php if ($pff != '') { ?>
                    font-family: <?php echo esc_attr($pff); ?>;
                 
                <?php } ?>
        }
        <?php } ?>
        .widget-box.business-contact.classic-business-lead-form.lp-lead-form-st .lp-review-btn{
            background: <?php echo esc_html($themeClr); ?> !important;
        }


        .visit-profile-status:hover,
        .sidebar-post-classic .widget-classic-box .listing-detail-infos li.contacts .get-direction a{
            border: 1px solid <?php echo esc_html($themeClr); ?>;
            color: <?php echo esc_html($themeClr); ?>;
        }
        .sidebar-post-classic .widget-classic-box .listing-detail-infos li.contacts .get-direction a:hover {
            color: #fff !important;
            background: <?php echo esc_html($themeClr); ?> !important;
        }
        #lp-report-listing a:hover,
        .listing-second-view .claim-area a.phone-number.md-trigger.claimformtrigger2:hover,
        .classic-content-widget .list-style-none i:hover,
        .sidebar-post-classic .widget-classic-box .listing-detail-infos li.contacts a:hover {
            color:<?php echo esc_html($themeClr); ?> !important;
        }
        
        .classic-review-form input:focus,
    .classic-review-form textarea:focus,
        .video-option.classic .play-icons,
        .widget-box.business-contact.classic-business-lead-form.lp-lead-form-st .hidding-form-feilds input:focus,
        .widget-box.business-contact.classic-business-lead-form.lp-lead-form-st .hidding-form-feilds textarea:focus ,
        .visit-profile-status
        .video-option.classic .play-icons{
            border: 1px solid <?php echo esc_html($themeClr); ?>;
        }
        .classic-review-form input#submit,
        .post-meta-info-classic .secondary-btn{
            background:<?php echo esc_html($themeClr); ?> !important;
            border: 1px solid <?php echo esc_html($themeClr); ?> !important;  color:#fff !important;
        }
       
        .lp-tool-tip-content p:hover,
        .lp-tooltip-div ul li:hover a,
        .lp-tooltip-outer .lp-tool-tip-content .sortbyrated-outer ul li:hover a {
            background: <?php echo esc_html($themeClr); ?>;
            color:#fff !important;
         }


        .lp-header-style-classic .header-list-icon-st6 {
        background: <?php echo esc_html($themeClr); ?>;
        border: 1px solid <?php echo esc_html($themeClr); ?> !important;
        }


        .classic-testimonial.lp-section-content-container .overlay-video-thumb:hover i{
        color: <?php echo esc_html($themeClr); ?>;
        background-color:#fff;
        }
        .testimonial.lp-section-content-container i::after {
        background: <?php echo esc_html($themeClr); ?>;
        }
        .listing-second-view .features.list-style-none>li a i,
        .video-option.classic .watch-video.popup-youtube{
            color:<?php echo esc_html($themeClr); ?>;  
        }
        .search-row-classic .listing-view-layout>ul li a.active,
        .search-row-classic .listing-view-layout>ul li a:hover{
        background: <?php echo esc_html($themeClr); ?>;
        color:#fff !important;
        }
		body .home-categories-area .lp-home-categoires li a:hover {
        background: <?php echo esc_html($categories_background_hover_color); ?> !important;
        }

        .search-row-classic .lp_add_more_filter a:hover,
        .search-row-classic .input-group.border-dropdown:hover span,
        .search-row-classic .input-group.border-dropdown:hover span:after,
        .search-row-classic .input-group.border-dropdown:hover .input-group-addon.lp-border i,
        .search-row-classic .input-group.border-dropdown:hover,
        .open-clasic-page-container .active-result.highlighted,
        .open-clasic-page-container .active-result.result-selected {
        background: <?php echo esc_html($themeClr); ?> !important;
        color: #fff !important;
        }
        .classic-filters-new-style #lp-find-near-me>ul>li>a:hover,
        .listing-classic-filters.listing-style-6 .search-filters > ul > li > .lp-tooltip-div.active ul li a:hover,
        .listing-classic-filters.listing-style-6 .search-filters>ul>li>a:hover,
        .listing-classic-filters.listing-style-6 .currency-signs>ul>li>a:hover{
        background: <?php echo esc_html($themeClr); ?>;
        color:#fff !important;
        }
		.footer-social-icons.font-awesome-icons li i {
            color:<?php echo esc_html($footer_icon_Clr); ?>;
        }

        .lp-header-style-classic .lp-add-listing-btn a.header-list-icon-st6:hover {
            background: <?php echo esc_html($secThemeClr); ?>;
            border-color: <?php echo esc_html($secThemeClr); ?> !important;
            color: #fff !important;
        }

        .lp-search-bar.classic-search .form-group .lp-search-bar-right:hover {
            background-color: <?php echo esc_html($secThemeClr); ?> !important;
        }

        .footer-social-icons.font-awesome-icons li i:hover {
            color: <?php echo esc_html($themeClr); ?>;
        }
		<!-- classic new style end !-->
        <?php
        $top_banner_styles = lp_get_banner_style();
        if ($top_banner_styles == 'map_view' && wp_is_mobile()) {
            ?>
            .lp-header-overlay {
                display: none;
            }
            <?php
        }
    }
}
//add_action('wp_head', 'listingpro_dynamic_options', 100);
if( !function_exists('listingpro_dynamic_css_options')){
    function listingpro_dynamic_css_options() {
        $css_editor = lp_theme_option('css_editor');
        ?>
        <?php echo esc_html( $css_editor ); ?>
        <?php
    }
}
//add_action('wp_head', 'listingpro_dynamic_css_options', 100);
if( !function_exists('listingpro_dynamic_js_options')){
    function listingpro_dynamic_js_options() {
        $script_editor = lp_theme_option('script_editor');
        echo '<script type="text/javascript">
                '.$script_editor.' 
         </script>';
    }
}
add_action('wp_head', 'listingpro_dynamic_js_options', 100);