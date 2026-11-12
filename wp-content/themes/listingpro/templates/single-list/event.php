<?php

global $post;
$event_grid_call    =   'sidebar_area';
$col_class  =   '';
if( isset( $GLOBALS['event_grid_call'] ) && !empty( $GLOBALS['event_grid_call'] ) )
{
    $event_grid_call    =   $GLOBALS['event_grid_call'];
}
if( $event_grid_call == 'content_area' )
{
    $col_class  =   'col-md-6';
}
$attached_events   =   get_post_meta( $post->ID, 'event_id', true );

if( isset( $attached_events ) && !empty( $attached_events ) ):
    $timeNow    =   strtotime("-1 day");
    if( !is_array( $attached_events ) )
    {
        $attached_events   =   (array) $attached_events;
    }
    $attached_event_ordered =   array();
    foreach ( $attached_events as $attached_event )
    {
        $event_date     =   get_post_meta( $attached_event, 'event-date', true );
        $event_date_end =   get_post_meta( $attached_event, 'event-date-e', true );
        if( ( $timeNow < $event_date) || ( !empty( $event_date_end) && $event_date_end > $timeNow ) && get_post_status( $attached_event ) != false )
        {
            $attached_event_ordered[$attached_event]    =   $event_date;
        }
    }
    asort($attached_event_ordered);
	$event_counter  =   1;
	if( $attached_event_ordered && count( $attached_event_ordered ) > 0 )
	{
	    if( $event_grid_call == 'sidebar_area' )
	    {
	        ?>
            <div class="events-sidebar-wrap" data-num="<?php echo count( $attached_event_ordered ); ?>">
            <?php
        }
        else
        {
            ?>
            <div class="row">
				<h4 class="lp-detail-section-title-classic "><?php echo esc_html__('Events', 'listingpro') ?></h4>
            <div class="events-content-area-wrap" data-num="<?php echo count( $attached_event_ordered ); ?>">
            <?php
        }
	    ?>

            <?php
            foreach ( $attached_event_ordered as $event_id => $v )
            {

                $event_date     =   get_post_meta( $event_id, 'event-date', true );
                $event_date_end =   get_post_meta( $event_id, 'event-date-e', true );
                if( $timeNow < $event_date || ( !empty( $event_date_end) && $event_date_end > $timeNow ) )
                {
                    $event_time =   get_post_meta( $event_id, 'event-time', true );
                    $event_end_time =   get_post_meta( $event_id, 'event-time-e', true);

                    $event_time =   date(get_option( 'time_format' ), strtotime($event_time));
                    $event_end_time =   date(get_option( 'time_format' ), strtotime($event_end_time));

                    $event_loc =   get_post_meta( $event_id, 'event-loc', true );
                    $event_ticket_url =   get_post_meta( $event_id, 'ticket-url', true );
                    $event_img =   get_post_meta( $event_id, 'event-img', true );
                    $event_utilities =   get_post_meta( $event_id, 'event-utilities', true );
                    $event_lsiting_id   =   get_post_meta( $event_id, 'event-lsiting-id', true );

                    $event_lat =   get_post_meta( $event_id, 'event-lat', true );
                    $event_lon =   get_post_meta( $event_id, 'event-lon', true );

                    $event_object = get_post( $event_id );
                    $current_user = wp_get_current_user();
                    $user_id = $current_user->ID;

                    $attending_users    =   get_post_meta( $event_id, 'attending-users', true );
                    $attendies_count    =   0;
                    if( !empty( $attending_users ) && is_array( $attending_users ) )
                    {
                        $attendies_count    =   count( $attending_users );
                    }

                    if( has_post_thumbnail( $event_id ) )
                    {
                        $event_img  =   get_the_post_thumbnail_url( $event_id, 'large' );
                    } else {
                        $event_img  =   $event_img;
                    }

                    global $listingpro_options;
                    $lp_default_map_pin = $listingpro_options['lp_map_pin']['url'];

                    if(empty($lp_default_map_pin)){
                        $lp_default_map_pin = get_template_directory_uri() . '/assets/images/pins/pin.png';
                    }
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
                    <div class="event-slide-wrap lp-event-grid-new lp-classic-single-events <?php echo esc_attr( $col_class ); ?>">
                        <div class="sidebar-post event-sidebar-wrapper">
                            <div class="widget-box lp-event-outer">
                                <?php
                                if( !empty( $event_img ) ):
                                    ?>
                                    <div class="lp-event-image-container">
                                        <div class="lp-event-image-overlay"></div>
                                        <div class="lp-event-author-listing">
                                            <img alt="image" src="<?php echo esc_attr( $avatar ); ?>" class="event-author-img">
                                            <span class="event-hosted-by">
                                                <?php echo esc_html__('Hosted by','listingpro');?><br>
                                                <?php echo wp_trim_words(get_the_title( $event_lsiting_id ), 3, '...'); ?>
                                            </span>
                                        </div>
                                        <a href="<?php echo get_permalink( $event_id ); ?>"><img src="<?php echo esc_attr( $event_img ); ?>" alt="<?php echo get_the_title( $event_id ); ?>"></a>
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
                                                            <i class="fa fa-clock-o" aria-hidden="true" style="color: #06ff00;"></i><?php echo esc_attr( $event_time ); ?>
                                                            <?php if(!empty( $event_date)): ?>
                                                                <?php echo esc_html__( '-', 'listingpro' ); ?>
                                                                <?php echo date_i18n( 'M d,o', $event_date ); ?>
                                                            <?php endif; ?>
                                                        </span>
                                                    </li>
                                                <?php endif; ?>
                                                <?php
                                                if( !empty( $event_date_end ) ):
                                                    ?>
                                                    <li>
                                                        <span title="Event End">
                                                            <i class="fa fa-clock-o" aria-hidden="true" style="color: #ff0002;"></i><?php echo esc_attr( $event_end_time ); ?>
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
                                                    <span><i class="fa fa-map-marker" aria-hidden="true"></i><?php echo esc_attr( $event_loc );?></span>
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
                                                        $attending_counter  =  1;
                                                        foreach ( $attending_users as $attending_user )
                                                        {
                                                            $author_avatar_url = get_user_meta( $attending_user , "listingpro_author_img_url", true);
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
                                                            <li><img alt="image" src="<?php echo esc_attr( $avatar ); ?>"></li>
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
                                                    <button type="button" class="attend-event not-going" data-event="<?php echo esc_attr( $event_id ); ?>" data-uid="<?php echo esc_attr( $user_id ); ?>"><?php echo esc_html__( 'not going', 'listingpro' ); ?></button>
                                                <?php else: ?>
                                                    <button type="button" class="attend-event" data-event="<?php echo esc_attr( $event_id ); ?>" data-uid="<?php echo esc_attr( $user_id ); ?>"><?php echo esc_html__( 'Going?', 'listingpro' ); ?></button>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <button type="button" class="md-trigger" data-modal="modal-3"><?php echo esc_html__( 'GOING?', 'listingpro' ); ?></button>
                                            <?php endif; ?>
                                            <?php
                                            if( !isset( $event_utilities['counter'] ) || $event_utilities['counter'] == 'yes' ):
                                                ?>
                                                <button type="button" class="total-going"><?php echo esc_attr( $attendies_count ); ?> <?php echo esc_html__( 'going', 'listingpro' ); ?></button>
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
                    <?php
                }

                $event_counter++;
            }

            ?>
        </div>
        <?php

        if( $event_grid_call == 'content_area' )
        {
            ?>
            <div class="clearfix"></div>
            </div>
            <?php
        }
        ?>
        <?php
    }
	?>
<?php endif; ?>