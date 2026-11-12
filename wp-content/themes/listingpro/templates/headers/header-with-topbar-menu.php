<header class="lp-header style-v2">

    <?php

    global $listingpro_options;

    $topBannerView = lp_get_banner_style();

    if( $topBannerView == 'banner_view' || $topBannerView == 'banner_view_search_bottom'
       || $topBannerView == 'banner_view_category_upper' || $topBannerView == 'banner_view_search_inside' )
    {
        echo '<div class="lp-header-overlay"></div>';
    }

    $top_bar    =   $listingpro_options['top_bar_enable_new'];

    if( $top_bar == 1 )
    {
        get_template_part('templates/headers/topbar');
    }
    $header_fullwidth   =   $listingpro_options['header_fullwidth'];
    $container_class    =   'container';
    if( $header_fullwidth == 1 )
    {
        $container_class    =   'container-fluid';
    }
	$header_logo_col_class  =   '4';
	$header_nav_col_class  =   '8';
	if( !is_front_page() )
	{
		$header_logo_col_class  =   '7';
		$header_nav_col_class  =   '5';
	}
    ?>
	<!--Mobile Menu section-->
   <div id="menu" class="menu-height-0 small-screen">
       <?php
       $listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];
       $showAddListing = true;
       if( isset($listing_access_only_users)&& $listing_access_only_users==1 ){
           $showAddListing = false;
           if(is_user_logged_in()){
               $showAddListing = true;
           }
       }
       if($showAddListing==true) {
           $addURL = listingpro_url('add_listing_url_mode');
           if(!empty($addURL)) {
               $lpl_add_listing    =   'lpl-add-listing-loggedout';
               if(is_user_logged_in()) {
                   $lpl_add_listing = 'lpl-add-listing-logedin';
               }
               ?>
               <a href="<?php echo listingpro_url('add_listing_url_mode'); ?>" class="lpl-button <?php echo esc_attr($lpl_add_listing); ?>"><?php esc_html_e('Add Listing', 'listingpro');?></a>
               <?php
           }
       }
       ?>
       <?php
       if (!is_user_logged_in()) {
           $login_popup_style  =   $listingpro_options['login_popup_style'];
           if($login_popup_style == 'style2') {
               ?>
               <a class="lpl-button lp-right-15 app-view-popup-style" data-target="#app-view-login-popup"><?php esc_html_e('Sign In', 'listingpro');?></a>
               <?php
           } else {
               ?>
               <a class="lpl-button md-trigger" data-modal="modal-3"><?php esc_html_e('Sign In', 'listingpro');?></a>
               <?php
           }
           ?>
       <?php }  else { ?>                  <a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>" class="lpl-button lpl-signout
" style="right: 10px;"><?php esc_html_e('Sign out ','listingpro'); ?></a>                 <?php } ?>
       <?php
       echo listingpro_mobile_menu();
       ?>
   </div>
   <!--End Mobile Menu Section-->
    <div class="lp-header-middle fullwidth-header">
        <div class="<?php echo esc_attr($container_class); ?>">
            <div class="row">
                <div class="col-md-<?php echo esc_attr($header_logo_col_class); ?> col-xs-12 lp-logo-header4-sts">
                    <?php
                    if( has_nav_menu( 'category_menu' ) ):
                    ?>
                    <div class="lp-header-nav-btn">
                        <div class="lp-join-now after-login lp-join-user-info header-cat-menu">
                            <button><span></span><span></span><span></span></button>
                            <?php listingpro_categoies_menu(); ?>
                        </div>
                    </div>

                    <?php endif; ?>

                    <div class="lp-header-logo">

                        <a href="<?php echo esc_url(home_url('/')); ?>">

                            <?php

                            if(is_front_page()){

                                listingpro_primary_logo();

                            }

                            else{

                                listingpro_secondary_logo();

                            }

                            ?>

                        </a>

                    </div>
                    <?php
                    global $listingpro_options;
                    $headerSrch = $listingpro_options['search_switcher'];
                    if($headerSrch == 1) {
                        if(!is_front_page() && !is_page_template('template-dashboard.php')){
                            get_template_part('templates/search/top_search');
                        }
                    }
                    ?>
                    <div class="clearfix"></div>

                </div>

                <div class="col-xs-2 text-right mobile-nav-icon lp-menu-header4-sts">

                    <a href="#menu" class="nav-icon">

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>

                        <span class="icon-bar"></span>

                    </a>

                </div>

                <div class="col-md-<?php echo esc_attr($header_nav_col_class); ?> hidden-xs hidden-sm lp-menu-header4-sts-icon">
                    <?php
                    get_template_part( 'templates/join-now' );
                    ?>

                    <?php

                    global $listingpro_options;

                    $listing_access_only_users = $listingpro_options['lp_allow_vistor_submit'];

                    $showAddListing = true;

                    if( isset( $listing_access_only_users )&& $listing_access_only_users == 1 )

                    {

                        $showAddListing = false;

                        if( is_user_logged_in() )

                        {

                            $showAddListing = true;

                        }

                    }

                    $addURL = listingpro_url('add_listing_url_mode');

                    if( $showAddListing == true && !empty( $addURL ) )

                    {

                        ?>

                        <div class="lp-header-add-btn">

                            <a href="<?php echo esc_url($addURL); ?>"><i class="fa fa-plus-circle" aria-hidden="true"></i> <?php echo esc_html__('Add Listing', 'listingpro'); ?></a>

                        </div>

                        <?php



                    }



                    if( is_home() || is_front_page() )

                    {

                        echo '<div class="header-main-menu lp-menu menu">';

                        echo listingpro_primary_menu();

                        echo '</div>';

                    }

                    else

                    {

                        echo '<div class="header-main-menu inner-main-menu lp-menu menu">';

                       echo listingpro_inner_menu();

                        echo '</div>';

                    }





                    ?>



                    <div class="clearfix"></div>

                </div>

                <div class="clearfix"></div>

            </div>

        </div>

    </div>

</header>

