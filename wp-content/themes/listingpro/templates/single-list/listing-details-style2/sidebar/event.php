<?php
$event_id   =   get_post_meta( $post->ID, 'event_id', true );
if( isset( $event_id ) && !empty( $event_id ) ):
    $event_date =   get_post_meta( $event_id, 'event-date', true );
    $event_time =   get_post_meta( $event_id, 'event-time', true );
    $event_loc =   get_post_meta( $event_id, 'event-loc', true );
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

    ?>
    <div class="sidebar-post">
        <div class="widget-box lp-event-outer">
            <?php
            if( !empty( $event_img ) ):
                ?>
                <div class="lp-event-image-container">
                    <img src="<?php echo esc_attr( $event_img ); ?>" alt="<?php echo get_the_title( $event_id ); ?>">
                </div>
            <?php endif; ?>
            <div class="lp-event-outer-container">
                <div class="lp-event-outer-content margin-bottom-10">
                    <?php
                    if( !empty( $event_date ) ):
                        ?>
                        <div class="lp-evnt-date-container">
                            <div class="lp-evnt-date-container-inner">
                                <span><?php echo date_i18n( 'D', $event_date ); ?></span>
                                <span><?php echo date_i18n( 'm', $event_date ); ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="lp-evnt-content-container">
                        <h3><?php echo get_the_title( $event_id ); ?></h3>
                        <p><?php echo esc_attr( $event_object->post_content ); ?></p>
                    </div>
                </div>
                <div class="lp-events-btns-outer">
                    <?php
                    if( is_user_logged_in() ):
                        if( is_array( $attending_users ) && in_array( $user_id, $attending_users ) ):
                            ?>
                            <button type="button"><?php echo esc_html__( 'already going', 'listingpro' ); ?></button>
                        <?php else: ?>
                            <button type="button" id="attend-event" data-event="<?php echo esc_attr( $event_id ); ?>" data-uid="<?php echo esc_attr( $user_id ); ?>"><?php echo esc_html__( 'Yes! i am going', 'listingpro' ); ?></button>
                        <?php endif; ?>
                    <?php else: ?>
                        <button type="button" class="md-trigger" data-modal="modal-3"><?php echo esc_html__( 'Yes! i am going', 'listingpro' ); ?></button>
                    <?php endif; ?>
                    <button type="button" class="total-going"><?php echo esc_attr( $attendies_count ); ?> <?php echo esc_html__( 'going', 'listingpro' ); ?></button>
                    <ul class="clearfix margin-top-20">
                        <?php
                        if( !empty( $event_time ) ):
                            ?>
                            <li class="pull-left"><i class="fa fa-clock-o" aria-hidden="true"></i> <?php echo esc_attr( $event_time ); ?></li>
                        <?php endif; ?>

                        <li class="pull-right">
                            <?php
                            if( !empty( $event_loc ) ):
                                ?>
                                <span><i class="fa fa-map-marker" aria-hidden="true"></i> <?php echo esc_html__( 'show map', 'listingpro' ) ?></span>
                            <?php endif; ?>
                            <?php
                            if( !empty( $event_ticket_url ) ):
                                ?>
                                <a target="_blank" href="<?php echo esc_url( $event_ticket_url ); ?>"><?php echo esc_html__( 'Get ticket', 'listingpro' ) ?></a>
                            <?php endif; ?>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>