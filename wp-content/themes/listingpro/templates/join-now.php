<?php
global $listingpro_options;
$lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];
$header_viewss  =   lp_get_header_views();
$popup_style   =   $listingpro_options['login_popup_style'];
if( !isset( $popup_style ) || empty( $popup_style ) || !$popup_style )
{
    $popup_style    =   'style1';
}
if( $popup_style == 'style2' && !wp_is_mobile() )
{
    ?>
    <div class="modal fade style2-popup-login" id="app-view-login-popup" role="dialog" style="overflow: visible !important; opacity: 1;">
        <?php
        get_template_part('mobile/templates/login-reg-popup');
        ?>
    </div>
    <?php
}
if( $header_viewss == 'header_with_topbar_menu' )
{
    ?>
    <div class="lp-header-user-nav">
        <?php
        if ( !is_user_logged_in() )
        {

            if( $popup_style == 'style2' )
            {
                ?>
                <a class="header-login-btn app-view-popup-style" data-target="#app-view-login-popup">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </a>
                <?php
            }
            else
            {
                ?>
                <a class="header-login-btn md-trigger" data-modal="modal-3">
                    <i class="fa fa-user" aria-hidden="true"></i>
                </a>
                <?php
            }

        }
        else
        {
            $current_user = wp_get_current_user();
            $u_display_name = $current_user->display_name;
            if(empty($u_display_name))
            {
                $u_display_name = $current_user->nickname;
            }
            global $listingpro_options;
            $authorURL = $listingpro_options['listing-author'];
	        $D_pageID = url_to_postid($authorURL);
	        if ($D_pageID) {
		        $authorURL = get_permalink($D_pageID);
	        }
            ?>
            <div class="lp-join-now after-login lp-join-user-info lp-join-now-v2">
                <ul>
                    <li class="juname">
                        <?php
                        if (class_exists('ListingproPlugin')) {
                            ?>
                            <a href="<?php echo esc_url($authorURL); ?>" class="juname">
                                <img src="<?php echo listingpro_author_image(); ?>" alt="userimg" height="34" width="34" />
                            </a>
                        <?php } ?>
                        <?php
                        $dashURL = listingpro_url('listing-author');
                        if(!empty($dashURL)){
                            $currentURL = $dashURL;
                            $perma = '';
                            $dashQuery = 'dashboard=';
                            global $wp_rewrite;
                            if ($wp_rewrite->permalink_structure == ''){
                                $perma = "&";
                            }else{
                                $perma = "?";
                            }
                            ?>
                            <ul class="lp-user-menu list-style-none">
                                <li class="lp-user-welcome"><?php echo esc_html__( 'Hello', 'listingpro' ). ' ' .esc_html($u_display_name); ?></li>
                                <li><a href="<?php echo listingpro_url('listing-author'); ?>"><?php esc_html_e('Dashboard','listingpro'); ?> </a></li>
                                <li><a href="<?php echo esc_url($currentURL.$perma.$dashQuery.'update-profile'); ?>"><?php esc_html_e('Update Profile','listingpro'); ?></a></li>
                                <li><a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>"><?php esc_html_e('Sign out ','listingpro'); ?></a></li>
                            </ul>
                        <?php } ?>
                    </li>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
    <?php
}
else
{
    if (!is_user_logged_in()) {
        ?>
        <div class="lp-join-now">
            <span>
                <!-- Contacts icon by Icons8 -->
                <?php
				if( $header_viewss == 'header_style10' ){
					echo '<i class="fa-solid fa-user"></i>';
				}else{
					echo listingpro_icons('contacts'); }
				?>
            </span>
            <?php
            if( $popup_style == 'style2' )
            {
                ?>
                <a class="app-view-popup-style" data-target="#app-view-login-popup"><?php esc_html_e('Sign In', 'listingpro'); ?></a>
                <?php
            }
            else
            {
                ?>
                <a class="md-trigger" data-modal="modal-3"><?php esc_html_e('Sign In', 'listingpro'); ?></a>
                <?php
            }
            ?>
        </div>
    <?php }else{
        $current_user = wp_get_current_user();
        $u_display_name = $current_user->display_name;
        if(empty($u_display_name)){
            $u_display_name = $current_user->nickname;
        }
        global $listingpro_options;
        $authorURL = $listingpro_options['listing-author'];
	    $D_pageID = url_to_postid($authorURL);
	    if ($D_pageID) {
		    $authorURL = get_permalink($D_pageID);
	    }
        ?>
        <div class="lp-join-now after-login lp-join-user-info">
            <ul>
                <li>
                                        <span>
                                            <img src="<?php echo listingpro_author_image(); ?>" alt="userimg" />
                                        </span>
                    <?php

                    if (class_exists('ListingproPlugin')) {
                        ?>
                        <a href="<?php echo esc_url($authorURL); ?>">
                            <?php
                            echo  esc_html($u_display_name);
                            ?>
                        </a>
                    <?php }else{ ?>
                        <a href="<?php echo get_author_posts_url($current_user->ID); ?>">
                            <?php
                            echo  esc_html($u_display_name);
                            ?>
                        </a>
                    <?php } ?>
                    <?php
                    $dashURL = listingpro_url('listing-author');
                    if(!empty($dashURL)){
                        $currentURL = $dashURL;
                        $perma = '';
                        $dashQuery = 'dashboard=';
                        global $wp_rewrite;
                        if ($wp_rewrite->permalink_structure == ''){
                            $perma = "&";
                        }else{
                            $perma = "?";
                        }
                        ?>
                        <ul class="lp-user-menu list-style-none">
                            <li><a href="<?php echo listingpro_url('listing-author'); ?>"><?php esc_html_e('Dashboard','listingpro'); ?> </a></li>
                            <li><a href="<?php echo esc_url($currentURL.$perma.$dashQuery.'update-profile'); ?>"><?php esc_html_e('Update Profile','listingpro'); ?></a></li>
                            <li><a href="<?php echo wp_logout_url( esc_url(home_url('/')) ); ?>"><?php esc_html_e('Sign out ','listingpro'); ?></a></li>
                        </ul>
                    <?php } ?>
                </li>
            </ul>
        </div>
        <?php
    }

}
?>