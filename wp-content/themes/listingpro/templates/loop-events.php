<?php
global $post;
$eid            =   get_the_ID();
$event_id            =   get_the_ID();

$event_title    =   get_the_title();
$listing_ID     =   get_post_meta( $eid, 'event-lsiting-id', true );
$listing_url    =   get_the_permalink( $listing_ID );
$event_time     =   get_post_meta( $eid, 'event-time', true );
$event_end_time     =   get_post_meta( $eid, 'event-time-e', true );
$event_loc      =   get_post_meta( $eid, 'event-loc', true );
$event_date     =   get_post_meta( $eid, 'event-date', true );
$event_date_end     =   get_post_meta( $eid, 'event-date-e', true );
$event_img      =   get_post_meta( $eid, 'event-img', true );
$event_ticket_url =   get_post_meta( $eid, 'ticket-url', true );
$event_lsiting_id   =   get_post_meta( $eid, 'event-lsiting-id', true );
if(isset($eid) && !empty($eid) && !empty($listing_ID)){

    if( has_post_thumbnail( $eid ) )
    {
        $event_img  =   get_the_post_thumbnail_url( $eid, 'listingpro-gallery-thumb2' );
    }
    else
    {
        $event_img    =   $event_img;
    }
    if(empty($event_img)) {
        $event_img  =   'https://placehold.co/360x198';
    }
    $event_content  =   $post->post_content;

    $attending_users    =   get_post_meta( $event_id, 'attending-users', true );
    $attendies_count    =   0;

    $authour_id =   get_post_field( 'post_author', $event_id );

    $current_user = wp_get_current_user();
    $user_id = $current_user->ID;

    $authour_id =   get_post_field( 'post_author', $event_id );
    $author_avatar_url = get_user_meta( $authour_id, "listingpro_author_img_url", true);
    $avatar;
    if( !empty( $author_avatar_url ) )
    {
        $avatar =  $author_avatar_url;
    }
    else
    {
        $avatar_url = listingpro_get_avatar_url ( $post->post_author, $size = '55' );
        $avatar =  $avatar_url;
    }
    ?>

    <div class="col-md-4" data-title="<?php echo esc_attr($event_title); ?>" data-postid="<?php echo get_the_ID(); ?>">
        <div class="event-slide-wrap lp-event-grid-new ">
            <div class="sidebar-post event-sidebar-wrapper">
                <div class="widget-box lp-event-outer">
                    <?php
                    if( !empty( $event_img ) ):
                        ?>
                        <div class="lp-event-image-container">
                            <div class="lp-event-image-overlay"></div>
                            <div class="lp-event-author-listing">
                                <img alt='image' src="<?php echo esc_url($avatar); ?>" class="event-author-img">
                                <span class="event-hosted-by">
                                                <?php echo esc_html__('Hosted by','listingpro');?><br>
                                    <?php echo wp_trim_words(get_the_title( $event_lsiting_id ), 3, '...'); ?>
                                            </span>
                            </div>
                            <a href="<?php echo get_permalink( $event_id ); ?>"><img src="<?php echo esc_url($event_img); ?>" alt="<?php echo get_the_title( $event_id ); ?>"></a>
                        </div>
                    <?php endif; ?>
                    <div class="lp-event-outer-container">
                        <div class="lp-event-outer-content margin-bottom-10">
                            <?php
                            if( !empty( $event_date ) ):
                                ?>
                                <div class="lp-evnt-date-container">
                                    <div class="lp-evnt-date-container-inner">
                                        <span><?php echo date_i18n( 'd', $event_date ); ?></span>
                                        <span><?php echo date_i18n( 'M', $event_date ); ?></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="lp-evnt-content-container">
                                <a href="<?php echo get_permalink($event_id); ?>"><?php echo wp_trim_words(get_the_title( $event_id ), 3, '...'); ?></a>
                                <ul class="lp-event-venue">
                                    <?php
                                    if( !empty( $event_time ) ):
                                        ?>
                                        <li>
                                            <span title="Event Start">
                                                <i class="fa fa-clock-o" aria-hidden="true" style="color: #06ff00;"></i><?php echo esc_attr($event_time); ?>
                                                <?php if(!empty( $event_date)): ?>
                                                    <?php echo esc_html__( '-', 'listingpro' ); ?>
                                                    <?php echo date_i18n( 'M d,o', $event_date ); ?>
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                    <?php endif; ?>
                                    <?php
                                    if( !empty( $event_end_time ) ):
                                        ?>
                                        <li>
                                            <span title="Event End">
                                                <i class="fa fa-clock-o" aria-hidden="true" style="color: #ff0002;"></i><?php echo esc_attr($event_end_time); ?>
                                                <?php if(!empty( $event_date_end)): ?>
                                                    <?php echo esc_html__( '-', 'listingpro' ); ?>
                                                    <?php echo date_i18n( 'M d,o', $event_date_end ); ?>
                                                <?php endif; ?>
                                            </span>
                                        </li>
                                    <?php endif; ?>
                                    <?php
                                    if( !empty( $event_loc ) ):
                                        ?>
                                        <li>
                                            <span><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo substr($event_loc, 0, 15);?>..</span>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                        <?php
                        if( !isset( $event_utilities['guests'] ) || $event_utilities['guests'] == 'yes' )
                        {

                            ?>
                            <div class="lp-attending-users-list">
                                <?php
                                if( is_array( $attending_users ) && count( $attending_users ) > 0 )
                                {
                                    ?>
                                    <ul class="lp-event-attendees">
                                        <?php
                                        $attending_counter  =   1;
                                        foreach ( $attending_users as $attending_user )
                                        {
                                            $author_avatar_url = get_user_meta( $attending_user, "listingpro_author_img_url", true);
                                            $avatar;
                                            if( !empty( $author_avatar_url ) )
                                            {
                                                $avatar =  $author_avatar_url;
                                            }
                                            else
                                            {
                                                $avatar_url = listingpro_get_avatar_url ( $attending_user, $size = '55' );
                                                $avatar =  $avatar_url;
                                            }
                                            ?>
                                            <li><img alt='image' src="<?php echo esc_url($avatar); ?>"></li>
                                            <?php
                                            if( $attending_counter == 3 ) break;
                                            $attending_counter++;
                                        }
                                        ?>
                                    </ul>
                                    <?php
                                    ?>
                                    <span><?php echo count( $attending_users ); ?> <?php echo esc_html__( 'Attendees', 'listingpro' ); ?></span>
                                    <?php
                                }
                                else
                                {
                                    ?>
                                    <span><?php echo esc_html__( '0 Attendees', 'listingpro' ); ?></span>
                                    <?php
                                }
                                ?>
                            </div>
                            <div class="lp-attending-users-outer">
                                <div class="lp-events-btns-outer">
                                    <input type="hidden" class="going-text" value="<?php echo esc_html__( 'Going?', 'listingpro' ); ?>">
                                    <input type="hidden" class="not-going-text" value="<?php echo esc_html__( 'not going?', 'listingpro' ); ?>">
                                    <?php
                                    if( is_user_logged_in() ):
                                        if( is_array( $attending_users ) && in_array( $user_id, $attending_users ) ):
                                            ?>
                                            <button type="button" class="attend-event not-going" data-event="<?php echo esc_attr($event_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><?php echo esc_html__( 'not going', 'listingpro' ); ?></button>
                                        <?php else: ?>
                                            <button type="button" class="attend-event" data-event="<?php echo esc_attr($event_id); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><?php echo esc_html__( 'Going?', 'listingpro' ); ?></button>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <button type="button" class="md-trigger" data-modal="modal-3"><?php echo esc_html__( 'GOING?', 'listingpro' ); ?></button>
                                    <?php endif; ?>
                                    <?php
                                    if( !isset( $event_utilities['counter'] ) || $event_utilities['counter'] == 'yes' ):
                                        ?>
                                        <button type="button" class="total-going"><?php echo esc_attr($attendies_count); ?> <?php echo esc_html__( 'going', 'listingpro' ); ?></button>
                                    <?php endif; ?>

                                </div>
                            </div>
                            <?php
                            echo '<div class="clearfix"></div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>