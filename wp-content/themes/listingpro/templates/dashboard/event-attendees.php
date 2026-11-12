<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;
//if( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST' )
//{
//    $attendeesmessage   =   $_POST['attendeesmessage'];
//    echo stripslashes( $attendeesmessage );
//}
if( isset( $_GET['eid'] ) )
{
    $eid    =   $_GET['eid'];
    $event_author   =   get_post_field('post_author', $eid);
}
if( $event_author != $user_id  ) return false;

$attendees_count    =   0;
$get_event_attending_user_ids  =   get_post_meta( $eid, 'attending-users', true );

if( is_array( $get_event_attending_user_ids ) )
{
    $attendees_count    =   count( $get_event_attending_user_ids );
}

$per_page       =   3;
$total_pages    =   ceil( $attendees_count/$per_page );

$currentURL = '';
$perma = '';
$dashQuery = 'dashboard=';
$currentURL = get_permalink();
global $wp_rewrite;
if ($wp_rewrite->permalink_structure == ''){
    $perma = "&";
}else{
    $perma = "?";
}
if( isset( $_GET['eid'] ) && isset( $_GET['downloadcsv'] ) && $_GET['downloadcsv'] == 'yes' )
{
    $event_title    =   get_the_title( $eid );
    $attendees_data =   array(
        array('username','Full Name','email', 'Phone'),
    );
    foreach ( $get_event_attending_user_ids as $attending_user_id )
    {
        $attendee_data  =   get_userdata( $attending_user_id );
        $user_email     =   $attendee_data->user_email;
        $username       =   $attendee_data->user_login;
        $full_name      =   $attendee_data->first_name.' '.$attendee_data->last_name;
        $phone          =   get_user_meta( $attending_user_id, 'phone', true );
        $attendees_data[]    =   array( $username, $full_name, $user_email, $phone );
    }

    attendees_to_csv_download($attendees_data, "attendees-for-$event_title.csv");
}

?>
<?php
ajax_response_markup();
?>
<div class="tab-pane fade in active lp-coupns-form" id="lp-events">
    <input type="hidden" value="<?php echo esc_html__( 'Something went wrong', 'listingpro' ); ?>" id="event-msg-error">
    <input type="hidden" value="<?php echo esc_html__( 'Message Sent Successfully', 'listingpro' ); ?>" id="event-msg-success">
	<?php
	if( $attendees_count == 0 ):
		?>
        <div class="lp-blank-section">
            <div class="col-md-12 blank-left-side">
                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                <p class="margin-bottom-20"><?php echo esc_html__('This event has no attendees so far.', 'listingpro'); ?></p>
            </div>
        </div>
	<?php
	else:
    ?>
        <a href="<?php echo esc_url($currentURL.$perma.$dashQuery.'events'); ?>" class="margin-left-20"><i class="fa fa-angle-left"></i>  <small><?php esc_html_e('Go Back To Events', 'listingpro') ?></small></a>
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12">
            <div class="panel-heading">
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default" data-toggle="tab" style="border-bottom: 0px !important;color: #3e3e3e;"><?php echo get_the_title($eid); ?></a></li>
                    <button data-form="message-form" class="lp-add-new-btn open-send-msg-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('Send Message','listingpro'); ?></button>
                    <a href="<?php echo esc_url($currentURL.$perma.$dashQuery.'event-attendees&eid='.$eid.'&downloadcsv=yes'); ?>" class="lp-add-new-btn"><span><i class="fa fa-download" aria-hidden="true"></i></span> <?php esc_html_e('Download CSV','listingpro'); ?></a>
                </ul>
            </div>
            <div class="panel-body">
                <div class="lp-main-title clearfix">
                    <div class="col-md-2"><p><input type="checkbox" id="toggle-attendees-check"> <?php esc_html_e('Check All','listingpro'); ?></p></div>
                    <div class="col-md-2"><p><?php esc_html_e('Username','listingpro'); ?></p></div>
                    <div class="col-md-2"><p><?php esc_html_e('Full Name','listingpro'); ?></p></div>
                    <div class="col-md-3"><p><?php esc_html_e('Email','listingpro'); ?></p></div>
                    <div class="col-md-3"><p><?php esc_html_e('Phone','listingpro'); ?></p></div>
                </div>
                <div class="tab-content clearfix">
                    <div class="tab-pane fade in active" id="tab1default">
                        <div class="attendees-pagin-wrap current-pagin-wrap attendees-pagin-wrap-1">
                            <?php
                            $pagin_wrap_counter =   1;
                            $row_counter    =   0;
                            if( $get_event_attending_user_ids )
                            {
                                foreach ( $get_event_attending_user_ids as $attending_user_id ):
                                    $row_counter++;
                                    $attendee_data  =   get_userdata( $attending_user_id );
                                    $user_email =   $attendee_data->user_email;
                                    $username   =   $attendee_data->user_login;
                                    $full_name  =   $attendee_data->first_name.' '.$attendee_data->last_name;
                                    $phone      =   get_user_meta( $attending_user_id, 'phone', true );
                                    ?>
                                    <div class="lp-listing-outer-container clearfix lp-coupon-outer-container <?php echo esc_attr($row_counter); ?>">
                                        <div class="col-md-2 lp-content-before-after" data-content="<?php esc_html_e('Checkbox','listingpro'); ?>">
                                            <div class="lp-deal-title">
                                                <p><input type="checkbox" class="attendee-checkbox" value="<?php echo esc_attr($attending_user_id); ?>"></p>
                                            </div>
                                        </div>
                                        <div class="col-md-2 lp-content-before-after" data-content="<?php esc_html_e('Username','listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><?php echo esc_attr($username); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-2 lp-content-before-after" data-content="<?php esc_html_e('Full Name','listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><?php echo esc_attr($full_name); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 lp-content-before-after" data-content="<?php esc_html_e('Email','listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><a href="mailto:<?php echo esc_attr($user_email); ?>"><?php echo esc_attr($user_email); ?></a> </p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 lp-content-before-after" data-content="<?php esc_html_e('Phone','listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><a href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_attr($phone); ?></a> </p>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
                                    <?php
                                    if( $row_counter%$total_pages == 0 )
                                    {
                                        $pagin_wrap_counter++;
                                        echo '</div><div class="attendees-pagin-wrap attendees-pagin-wrap-'. $pagin_wrap_counter .'">';
                                    }
                                endforeach;
                            }

                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php
            if( $total_pages > 1 )
            {
                ?>
                <div class="lp-pagination pagination attendees-pagination">
                    <ul class="page-numbers">
                        <?php
                        for ( $i = 1; $i <= $total_pages; $i++ )
                        {
                            $current_class  =   '';
                            if( $i == 1 )
                            {
                                $current_class  =   'current';
                            }
                        ?>
                            <li><span data-skeyword="" data-pageurl="<?php echo esc_attr($i); ?>" class="page-numbers haspaglink <?php echo esc_attr($current_class); ?>"><?php echo esc_attr($i); ?></span></li>
                        <?php
                        }
                        ?>
                    </ul>
                </div>
                <?php
            }
            ?>

        </div>
	<?php endif; ?>
    <div id="notification-form" style="display: none;">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height padding-top-0">
            <div class="lp-review-sorting clearfix padding-left-0 padding-top-0">
                <h5 class="margin-top-0"><?php echo esc_html__('Create Message', 'listingpro'); ?></h5>
            </div>
            <div class="lp-coupns-form-outer">
                <div class="lp-voupon-box">
                    <form class="lp-coupons-form-inner" id="lp-message-form">
                        <div class="lp-coupon-box-row">
                            <div class="row">
                                <div class="form-group col-sm-12 ">
                                    <textarea name="attendeesmessage" class="col-md-12 attendeesmessage" placeholder="<?php esc_html_e('Enter Your Massage', 'listingpro'); ?>"></textarea>
                                </div>
                            </div>
                        </div>
                        <div class="lp-coupon-box-row">
                            <div class="row">
                                <div class="form-group col-sm-12 clarfix">
                                    <button class="lp-coupns-btns cancel-send-mas-atten pull-left"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                    <button type="submit" id="lp-send-message" data-uid="<?php echo esc_attr($user_id); ?>" class="lp-coupns-btns pull-right"><?php echo esc_html__( 'Send', 'listingpro' ); ?></button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>