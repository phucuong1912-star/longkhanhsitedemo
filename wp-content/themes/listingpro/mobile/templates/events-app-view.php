<?php
if ( have_posts() ) {
    while ( have_posts() ) {
        the_post();

        $event_id = get_the_ID();

        $listing_id = get_post_meta($event_id, 'event-lsiting-id', true);
        $event_utilities =   get_post_meta( $event_id, 'event-utilities', true );
        $timeNow    =   strtotime("-1 day");
        $event_date = get_post_meta($event_id, 'event-date', true);
        $event_date_end = get_post_meta($event_id, 'event-date-e', true);
        if (!empty($event_date_end) && $event_date_end < $timeNow) return false;
        if (empty($event_date_end) && $timeNow > $event_date) return false;
        $event_time =   get_post_meta( $event_id, 'event-time', true );
        $event_loc =   get_post_meta( $event_id, 'event-loc', true );
        $event_lat =   get_post_meta( $event_id, 'event-lat', true );
        $event_lon =   get_post_meta( $event_id, 'event-lon', true );
        $event_ticket_url =   get_post_meta( $event_id, 'ticket-url', true );
        $event_img =   get_post_meta( $event_id, 'event-img', true );
        $event_object = get_post( $event_id );
        $current_user = wp_get_current_user();
        $user_id = $current_user->ID;
        $attending_users    =   get_post_meta( $event_id, 'attending-users', true );
        $attendies_count    =   0;
        if( !empty( $attending_users ) && is_array( $attending_users ) )
        {
            $attendies_count    =   count( $attending_users );
        }

        $lp_map_pin = lp_theme_option_url('lp_map_pin');
        ?>

        <section class="lp-event-detail app-view-events">
            <div class="lp-event-top-title-header">
                <div class="row">
                    <div class="col-md-8">
                        <?php
                        if( !empty( $event_img ) ){
                            require_once (THEME_PATH . "/include/aq_resizer.php");
                            $event_img_thumb  = aq_resize( $event_img, '580', '408', true, true, true);
                            ?>
                            <div class="lp-event-detail-thumbnail">
                                <img src="<?php echo esc_url($event_img_thumb); ?>" alt="<?php echo get_the_title( $event_id ); ?>">
                            </div>
                        <?php } ?>
                        <div class="lp-event-appview-section-wrap">
                            <div class="lp-event-detail-date-title-outer">
                                <?php if( !empty( $event_date ) ){ ?>
                                    <div class="lp-event-detail-date">
                                        <span class="event-detil-date"><?php echo date_i18n('d', $event_date); ?></span>
                                        <span><?php echo date_i18n('M', $event_date); ?></span>
                                    </div>
                                <?php } ?>
                                <div class="lp-event-detail-title-hosted">
                                    <h1><?php echo get_the_title( $event_id ); ?></h1>
                                    <h2><?php echo esc_html__('Hosted by:','listingpro');?> <a href="<?php echo get_permalink($listing_id); ?>"><?php echo get_the_title($listing_id); ?></a></h2>
                                </div>
                                <div class="lp-listing-action-btns lp-event-detal-share">
                                    <ul>
                                        <li>
                                            <?php listingpro_sharing_v2();?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="lp-event-detail-sidebar-area">


                                <?php
                                if( !isset( $event_utilities['guests'] ) || $event_utilities['guests'] == 'yes' )
                                { ?>


                                    <div class="event-total-going"><span><strong><?php echo esc_html__( 'Are You going ?', 'listingpro' ); ?></strong>
                                            <?php
                                            if( !isset( $event_utilities['counter'] ) || $event_utilities['counter'] == 'yes' ):
                                                ?>

                                                <?php echo esc_attr($attendies_count); ?> <?php echo esc_html__( 'going', 'listingpro' ); ?>
                                            <?php endif; ?>

                                    </span></div>
                                    <div class="lp-detail-event-going-btn">
                                        <?php if( is_user_logged_in() ):
                                            if( is_array( $attending_users ) && in_array( $user_id, $attending_users ) ):
                                                ?>
                                                <button type="button"><?php echo esc_html__( 'already going', 'listingpro' ); ?></button>
                                            <?php else: ?>
                                                <button type="button" class="attend-event" id="attend-event" data-event="<?php echo esc_attr($event_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><?php echo esc_html__( 'Yes! i am going', 'listingpro' ); ?></button>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <button type="button" class="user-menu" data-toggle="modal" data-target="#app-view-login-popup"><?php echo esc_html__( 'Yes! i am going', 'listingpro' ); ?></button>
                                        <?php endif; ?>

                                        <?php
                                        if( !empty( $event_ticket_url ) ):

                                            $tUrl_arr = explode(',', $event_ticket_url);
                                            foreach ($tUrl_arr as $item) {
                                                $item_arr = explode('|', $item);
                                                if (isset($item_arr[0]) && isset($item_arr[1])) { ?>
                                                    <a target="_blank" href="<?php echo esc_attr($item_arr[1]); ?>"
                                                       class="lp-event-detail-ticket"><i class="fa fa-tag"
                                                                                         aria-hidden="true"></i><?php echo esc_html__('Get Tickets', 'listingpro'); ?> <?php echo sprintf(esc_html__('%s', 'listingpro'), $item_arr[0]); ?>
                                                    </a>                                                <?php
                                                }
                                            }

                                        endif; ?>
                                    </div>



                                <?php } ?>

                            </div>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="lp-event-detail-side-section">
                            <ul>
                                <li>
                                    <div id="singlepostmap" class="lp-event-detail-map singlebigmaptrigger" data-lat="<?php echo esc_attr($event_lat); ?>" data-lan="<?php echo esc_attr($event_lon); ?>" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>
                                </li>

                                <?php if( !empty( $event_loc ) ){ ?>
                                    <li>
                                        <h3><i class="fa fa-map-marker" aria-hidden="true"></i>
                                            <span><?php echo esc_attr($event_loc);?>
                                    </span></h3>
                                        <?php

                                        $event_loc = preg_replace('/\s+/', '+', $event_loc);

                                        $dirURL = "https://maps.google.com?daddr=$event_loc";

                                        ?>

                                        <a target="_blank" href="<?php echo esc_url($dirURL); ?>"><?php echo esc_html__( 'Get Direction', 'listingpro' ); ?></a>
                                    </li>
                                <?php } ?>
                                <?php if( !empty( $event_time ) ){ ?>
                                    <li>
                                        <h3><i class="fa fa-clock-o" aria-hidden="true"></i><span><?php echo esc_attr($event_time); ?> <?php echo esc_html__( '-', 'listingpro' ); ?> <?php echo date_i18n( 'l', $event_date); ?></span></h3>
                                        <?php if(!empty( $event_date)){ ?>
                                            <span><?php echo date_i18n( get_option('date_format'), $event_date); ?>
                                    </span>
                                        <?php } ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="lp-event-detail-white-bg-section">

                <div class="row">
                    <div class="col-md-8">
                        <?php if(!empty($event_object)){ ?>
                            <div class="lp-event-detail-content">
                                <p><?php echo wp_kses_post($event_object->post_content); ?></p>
                            </div>
                        <?php } ?>
                        <div class="lp-event-viewall-attende">
                            <?php
                            if( !isset( $event_utilities['guests'] ) || $event_utilities['guests'] == 'yes' )
                            { ?>
                                <h4><?php echo esc_html__('Attendees', 'listingpro' ); ?>
                                    <?php
                                    if( !isset( $event_utilities['counter'] ) || $event_utilities['counter'] == 'yes' ):
                                        ?>

                                        <?php echo esc_html__('(', 'listingpro' ); ?><?php echo esc_attr($attendies_count); ?><?php echo esc_html__(')', 'listingpro' ); ?>
                                    <?php endif; ?>

                                </h4>
                            <?php } ?>
                        </div>
                        <div class="lp-event-detail-attendes-section">
                            <?php
                            if(isset($attending_users) && is_array($attending_users)){
                                $attendenumber = count($attending_users);
                                ?>
                                <ul>
                                    <?php
                                    $count = 1;
                                    if(!empty($attending_users)) {
                                        foreach ( $attending_users as $val ) {
                                            $user_attende      = get_userdata( $val );
                                            $author_avatar_url = get_user_meta( $user_attende->ID, "listingpro_author_img_url", true );
                                            //$author_url = get_author_posts_url( get_the_author_meta($user_attende->ID) );
                                            $avatar = '';
                                            if ( ! empty( $author_avatar_url ) ) {
                                                $avatar = $author_avatar_url;
                                            } else {
                                                $avatar_url = listingpro_get_avatar_url( $user_attende->ID, $size = '90' );
                                                $avatar     = $avatar_url;
                                            }
                                            ?>
                                            <li>
                                                <div class="attende-avtar">
                                                    <img src="<?php echo esc_url($avatar); ?>"
                                                         alt="<?php echo esc_attr($user_attende->user_nicename); ?>">
                                                </div>
                                                <span><?php echo esc_attr($user_attende->user_nicename); ?></span>
                                            </li>
                                            <?php
                                            if ( $count == 5 ) {
                                                break;
                                            }
                                            $count ++;
                                        }
                                    }
                                    ?>
                                </ul>
                                <?php if($attendenumber > 5) {

                                    ?>
                                    <ul class="lp-attende-extra">
                                        <?php
                                        $count = 1;
                                        foreach ($attending_users as $val) {
                                            if( $count > 1 ){
                                                $user_attende = get_userdata($val);
                                                $author_avatar_url = get_user_meta( $user_attende->ID, "listingpro_author_img_url", true);
                                                //$author_url = get_author_posts_url( get_the_author_meta($user_attende->ID) );
                                                $avatar = '';
                                                if( !empty( $author_avatar_url ) )
                                                {
                                                    $avatar =  $author_avatar_url;
                                                }
                                                else
                                                {
                                                    $avatar_url = listingpro_get_avatar_url ( $user_attende->ID, $size = '90' );
                                                    $avatar =  $avatar_url;
                                                }
                                                ?>
                                                <li>
                                                    <div class="attende-avtar">
                                                        <img src="<?php echo esc_url($avatar); ?>" alt="<?php echo esc_attr($user_attende->user_nicename); ?>">
                                                    </div>
                                                    <span><?php echo esc_attr($user_attende->user_nicename); ?></span>
                                                </li>
                                                <?php

                                            }$count++;
                                        }
                                        ?>
                                    </ul>
                                    <div class="lp-event-attende-view-all">

                                        <span data-contract="<?php echo esc_html__( 'View Less', 'listingpro' ); ?>" data-expand="<?php echo esc_html__( 'View More', 'listingpro' ); ?>" class="lp-event-view-less"><?php echo esc_html__( 'View More', 'listingpro' ); ?></span>

                                    </div>
                                <?php } ?>
                            <?php } ?>

                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php
                        if( is_active_sidebar( 'listing_archive_sidebar' ) ){
                            ?>
                            <div class="lp-event-detail-dynamic-sidebar">
                                <?php dynamic_sidebar( 'listing_archive_sidebar' );?>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

            </div>
        </section>

        <?php
    } //end while
}
?>