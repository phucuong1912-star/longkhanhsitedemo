<?php
do_action('lp_call_maps_scripts');
$current_user = wp_get_current_user();
$user_id      = $current_user->ID;
$d_args       = array(
	'post_type'      => 'events',
	'post_status'    => 'publish',
	'posts_per_page' => - 1,
	'author'         => $user_id,
);
$a_events     = new WP_Query($d_args);
$events_count = $a_events->found_posts;
$time_now     = strtotime("-1 day");
$currentURL   = '';
$perma        = '';
$dashQuery    = 'dashboard=';
$currentURL   = get_permalink();
global $wp_rewrite;
if ($wp_rewrite->permalink_structure == '') {
	$perma = "&";
} else {
	$perma = "?";
}
?>
<?php
ajax_response_markup();
?>
<input type="hidden" id="wp-timeformat" value="<?php echo get_option('time_format'); ?>">
<!--modal droppin, for custom lat and long via drag-->
<div id="modal-doppin" class="modal fade" role="dialog"
     data-lploctitlemap='<?php echo esc_html__("Your Location", "listingpro"); ?>'>
    <div class="modal-dialog">
        <a href="#" class="close" data-dismiss="modal"><i class="fa fa-times" aria-hidden="true"></i></a>
        <div class="md-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title"><?php echo esc_html__('Drop Pin', 'listingpro'); ?></h4>
            </div>
            <div id="lp-custom-latlong" style="height:600px; width:600px;"></div>
        </div>
    </div>
</div>
<div class="modal fade" id="dashboard-delete-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-delete-modal"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
				<?php echo esc_html__('Are you sure you want to delete?', 'listingpro'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary"
                        data-dismiss="modal"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                <button type="button"
                        class="btn btn-primary dashboard-confirm-del-btn"><?php echo esc_html__('Delete', 'listingpro'); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade in active lp-coupns-form" id="lp-events">
	<?php
	if ($events_count == 0) :
		?>
        <div class="lp-blank-section">
            <div class="col-md-12 blank-left-side">
                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                <p class="margin-bottom-20">
					<?php echo esc_html__('You must be here for the first time. If you like to add some thing, click the button below.', 'listingpro'); ?>
                </p>
                <button data-form="events" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus"
                                                                                             aria-hidden="true"></i></span> <?php echo esc_html__('Add new', 'listingpro'); ?>
                </button>
            </div>
        </div>
	<?php
	else :
		?>
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 align-center">
            <div class="panel-heading">
                <h5 class="margin-bottom-20"><?php esc_html_e('All Events', 'listingpro'); ?></h5>
                <ul class="nav nav-tabs">
                    <li class="active"><a href="#tab1default"
                                          data-toggle="tab"><?php esc_html_e('all events', 'listingpro'); ?></a></li>
                    <li><a href="#tab4default" data-toggle="tab"><?php esc_html_e('upcoming', 'listingpro'); ?></a></li>
                    <li><a href="#tab2default" data-toggle="tab"><?php esc_html_e('active', 'listingpro'); ?></a></li>
                    <li><a href="#tab3default" data-toggle="tab"><?php esc_html_e('inactive', 'listingpro'); ?></a></li>
                    <button data-form="events" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus"
                                                                                                 aria-hidden="true"></i></span> <?php esc_html_e('add new', 'listingpro'); ?>
                    </button>
                    <div class="form-group clearfix  margin-0">
                        <div class="select2-dash  col-sm-5 pull-right" style="width: 32%;">
							<?php
							$current_user     = wp_get_current_user();
							$user_id          = $current_user->ID;
							$events_displayin = get_user_meta($user_id, 'event_display_area', true);
							?>
                            <div class="discount_displayin_wrap clearfix pull-right">
                                <span class="discount_displayin_wrap_title"><?php echo esc_html__('Display In', 'listingpro'); ?></span>
                                <select class="form-control select2" id="event_displayin"
                                        data-udi="<?php echo esc_attr($user_id); ?>">
                                    <option value="content" <?php if ($events_displayin == 'content') : echo 'selected="selected"';
									endif; ?>>
										<?php echo esc_html__('Content Area', 'listingpro'); ?></option>
                                    <option value="sidebar" <?php if ($events_displayin == 'sidebar') : echo 'selected="selected"';
									endif; ?>>
										<?php echo esc_html__('Sidebar Area', 'listingpro'); ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                </ul>
            </div>
            <div class="panel-body">
                <div class="lp-main-title clearfix">
                    <div class="col-md-3">
                        <p><?php esc_html_e('event', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><?php esc_html_e('Listing', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-2">
                        <p><?php esc_html_e('start date', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-1 padding-0">
                        <p><?php esc_html_e('start time', 'listingpro'); ?></p>
                    </div>
                    <div class="col-md-3 text-center" style="padding-right: 8%;">
                        <p><?php esc_html_e('status', 'listingpro'); ?></p>
                    </div>
                </div>
                <div class="tab-content clearfix">
                    <div class="tab-pane fade in active" id="tab1default">
						<?php
						if ($a_events->have_posts()) : while ($a_events->have_posts()) : $a_events->the_post();
							$eID    = get_the_ID();
							$eImg   = get_post_meta($eID, 'event-img', true);
							$eLID   = get_post_meta($eID, 'event-lsiting-id', true);
							$eLoc   = get_post_meta($eID, 'event-loc', true);
							$tUrl   = get_post_meta($eID, 'ticket-url', true);
							$eTime  = get_post_meta($eID, 'event-time', true);
							$eTimeE = get_post_meta($eID, 'event-time-e', true);
							$eDate  = get_post_meta($eID, 'event-date', true);
							$eDateE = get_post_meta($eID, 'event-date-e', true);
							$eLat   = get_post_meta($eID, 'event-lat', true);
							$eLon   = get_post_meta($eID, 'event-lon', true);
							$eTitle = get_the_title(get_the_ID());
							?>
                            <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                <div class="col-md-3 lp-content-before-after"
                                     data-content="<?php esc_html_e('Title', 'listingpro'); ?>">
                                    <div class="lp-deal-title">
                                        <p title="<?php echo get_the_title(get_the_ID()); ?>">
											<?php echo get_the_title(get_the_ID()); ?></p>
                                        <a href="<?php echo esc_url($currentURL . $perma . $dashQuery . 'event-attendees&eid=' . get_the_ID()); ?>"
                                           class="view-event-attendees"
                                           data-eid="<?php echo get_the_ID(); ?>"><?php echo esc_html__('View Attendees', 'listingpro'); ?></a>
                                    </div>
                                </div>
                                <div class="col-md-3 lp-content-before-after"
                                     data-content="<?php esc_html_e('Listing', 'listingpro'); ?>">
                                    <div class="lp-listing-expire-section">
                                        <p><a href="<?php echo esc_url(get_permalink($eLID)); ?>"
                                              target="_blank"><?php echo wp_kses_post(get_the_title($eLID)); ?></a></p>
                                    </div>
                                </div>
                                <div class="col-md-2 lp-content-before-after"
                                     data-content="<?php esc_html_e('Event Date', 'listingpro'); ?>">
                                    <div class="lp-listing-expire-section">
										<?php if ( ! empty($eDate)) { ?>
                                            <p><?php echo date_i18n(get_option('date_format'), $eDate); ?></p>
										<?php } ?>
                                    </div>
                                </div>
                                <div class="col-md-1 lp-content-before-after padding-0"
                                     data-content="<?php esc_html_e('Event Time', 'listingpro'); ?>">
                                    <div class="lp-listing-expire-section">
                                        <p><?php echo esc_attr($eTime); ?></p>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center lp-content-before-after"
                                     data-content="<?php esc_html_e('Status', 'listingpro'); ?>">
                                    <div class="clearfix">
                                        <div class="lp-listing-pay-outer lp-display-inline">
											<?php
											if (date("Y-m-d", (int) $eDate) == date("Y-m-d")) {
												?>
                                                <a class="lp-listing-pay-button"> <?php esc_html_e('active', 'listingpro'); ?></a>
												<?php
											} else if ($time_now < $eDate) {
												?>
                                                <a class="lp-listing-pay-button button-upcoming">
													<?php esc_html_e('upcoming', 'listingpro'); ?></a>
												<?php
											} else if ($eDateE && $eDateE > $time_now) {
												?>
                                                <a class="lp-listing-pay-button"> <?php esc_html_e('active', 'listingpro'); ?></a>
												<?php
											} else {
												?>
                                                <a class="lp-listing-pay-button inactive">
													<?php esc_html_e('inactive', 'listingpro'); ?></a>
												<?php
											}
											?>
                                        </div>
                                        <div class="lp-display-inline">
                                            <div class="lp-dot-extra-buttons">
                                                <img alt="image"
                                                     src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABtSURBVEhLYxgFgwN4R2UKekXl7gJhEBsqTDnwiM4N8YrO/Q/GUTlBUGHKAciVntG5O0DYJTSNHyo8UoFnVI61V0yuFZRLHQAyEBZ5PpHZllBhygHIMKjB/6hqMAiADKS6oUMPjGbpUUANwMAAAIAtN4uDPUCkAAAAAElFTkSuQmCC">
                                                <ul class="lp-user-menu list-style-none">
                                                    <li><a href="" class="event-edit"
                                                           data-targetid="<?php echo esc_attr($eID); ?>"
                                                           data-disID="<?php echo esc_attr($eID); ?>"
                                                           data-uid="<?php echo esc_attr($user_id); ?>"><i
                                                                    class="fa fa-pencil-square-o"></i><span><?php esc_html_e('Edit', 'listingpro'); ?></span></a>
                                                    </li>
                                                    <li><a href="#" class="del-this event-del"
                                                           data-uid="<?php echo esc_attr($user_id); ?>"
                                                           data-lid="<?php echo esc_attr($eLID); ?>"
                                                           data-eid="<?php echo esc_attr($eID); ?>"
                                                           data-targetid="<?php echo esc_attr($eID); ?>"><i
                                                                    class="fa fa-trash-o"></i><span><?php esc_html_e('Delete', 'listingpro'); ?></span></a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <div style="display: none;" id="update-wrap-<?php echo esc_attr($eID); ?>">
                                    <div class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height margin-top-40">
                                        <div class="lp-coupns-form-outer">
                                            <div class="lp-voupon-box">
                                                <form class="lp-coupons-form-inner"
                                                      id="lp-events-form-<?php echo esc_attr($eID); ?>">
                                                    <div class="lp-coupon-box-row">
                                                        <div class="row">
                                                            <div class="form-group col-sm-7 col-md-7">
                                                                <div class="margin-bottom-20">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-title-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event Name', 'listingpro'); ?></label>
                                                                    <input name="event-title-<?php echo esc_attr($eID); ?>"
                                                                           id="event-title-<?php echo esc_attr($eID); ?>"
                                                                           class="lp-dashboard-text-field form-control"
                                                                           value="<?php echo esc_attr($eTitle); ?>"
                                                                           type="text"
                                                                           placeholder="<?php echo esc_html__('Give it a short quick name', 'listingpro'); ?>">
                                                                </div>
                                                                <div class="">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-description-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event Description', 'listingpro'); ?></label>
                                                                    <textarea
                                                                            id="event-description-<?php echo esc_attr($eID); ?>"
                                                                            name="event-description-<?php echo esc_attr($eID); ?>"
                                                                            type="text"
                                                                            class="form-control lp-dashboard-des-field"
                                                                            rows="10"><?php echo get_the_content(); ?></textarea>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-5">
                                                                <div class="jFiler-input-dragDrop pos-relative">
                                                                    <input type="hidden"
                                                                           id="event-old-img-<?php echo esc_attr($eID); ?>"
                                                                           value="<?php echo esc_attr($eImg); ?>">
                                                                    <div class="removeable-image upload-field dashboard-upload-field edit-upload-<?php echo esc_attr($eID); ?>">
																		<?php
																		if ( ! empty($eImg)) {
																			?>
                                                                            <span class="remove-event-img remove-eei"
                                                                                  data-targetid="<?php echo esc_attr($eID); ?>">X</span>
																			<?php
																		}
																		?>
																		<?php echo do_shortcode('[frontend-button]'); ?>
																		<?php
																		if ( ! empty($eImg)) :
																			?>
                                                                            <img class="lp-uploaded-img event-old-img-<?php echo esc_attr($eID); ?>"
                                                                                 src="<?php echo esc_url($eImg); ?>"
                                                                                 alt="image">
																		<?php endif; ?>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="lp-coupon-box-row">
                                                        <div class="row margin-bottom-20">
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-date-s-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event Starts', 'listingpro'); ?></label>
                                                                    <input value="<?php if ( ! empty($eDate)) {
																		echo date('M d, Y', $eDate);
																	} ?>"
                                                                           name="event-date-s-<?php echo esc_attr($eID); ?>"
                                                                           id="event-date-s-<?php echo esc_attr($eID); ?>"
                                                                           type="text"
                                                                           class="lp-dashboard-text-field form-control discount-date"
                                                                           placeholder="MM/DD/YYYY">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-time-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event start Time', 'listingpro'); ?></label>
                                                                    <input value="<?php echo esc_attr($eTime); ?>"
                                                                           name="event-time-<?php echo esc_attr($eID); ?>"
                                                                           id="event-time-<?php echo esc_attr($eID); ?>"
                                                                           type="text"
                                                                           class="lp-dashboard-text-field form-control datetimepicker1"
                                                                           placeholder="<?php echo esc_html__('1:00 am', 'listingpro'); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-date-e-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event Ends', 'listingpro'); ?></label>
                                                                    <input value="<?php if ( ! empty($eDateE)) {
																		echo date('M d, Y', $eDateE);
																	} ?>"
                                                                           name="event-date-e-<?php echo esc_attr($eID); ?>"
                                                                           id="event-date-e-<?php echo esc_attr($eID); ?>"
                                                                           type="text"
                                                                           class="lp-dashboard-text-field form-control discount-date"
                                                                           placeholder="MM/DD/YYYY">
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="">
                                                                    <label class="lp-dashboard-top-label"
                                                                           for="event-time-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Event End Time', 'listingpro'); ?></label>
                                                                    <input value="<?php echo esc_attr($eTimeE); ?>"
                                                                           name="event-time-e-<?php echo esc_attr($eID); ?>"
                                                                           id="event-time-e-<?php echo esc_attr($eID); ?>"
                                                                           type="text"
                                                                           class="lp-dashboard-text-field form-control datetimepicker1"
                                                                           placeholder="<?php echo esc_html__('1:00 am', 'listingpro'); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="lp-coupon-box-row">
                                                        <div class="row">
                                                            <div class="form-group col-sm-12 ">
                                                                <div class="">
                                                                    <label for="event-location-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Address ', 'listingpro'); ?></label>
                                                                    <input value="<?php echo esc_attr($eLoc); ?>"
                                                                           type="text"
                                                                           class="lp-dashboard-text-field form-control event-addr"
                                                                           name="event-location-<?php echo esc_attr($eID); ?>"
                                                                           id="event-location-<?php echo esc_attr($eID); ?>"
                                                                           placeholder="<?php echo esc_html__('e.g. Country, City, Location', 'listingpro'); ?>">
                                                                    <input type="hidden"
                                                                           class="lp-dashboard-text-field latitude"
                                                                           name="latitude"
                                                                           value="<?php echo esc_attr($eLat); ?>">
                                                                    <input type="hidden"
                                                                           class="lp-dashboard-text-field longitude"
                                                                           name="longitude"
                                                                           value="<?php echo esc_attr($eLon); ?>">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="lp-coupon-box-row">
                                                        <div class="">
                                                            <label class="lp-dashboard-top-label"
                                                                   for="event-ticket-url-<?php echo esc_attr($eID); ?>"><?php echo esc_html__('Ticket URL', 'listingpro'); ?></label>
                                                            <div class="event-tickets-list-dash">
                                                                <input id="event-tickets-data-<?php echo esc_attr($eID); ?>"
                                                                       name="event-tickets-data-<?php echo esc_attr($eID); ?>"
                                                                       type="hidden"
                                                                       class="lp-dashboard-text-field form-control"
                                                                       value="<?php echo esc_attr($tUrl); ?>">
                                                                <ul class="clearfix margin-top-10">
																	<?php
																	$tUrl_arr = explode(',', $tUrl);
																	foreach ($tUrl_arr as $item) {
																		$item_arr = explode('|', $item);
																		if (isset($item_arr[0]) && isset($item_arr[1])) {
																			?>
                                                                            <li>
                                                                                <strong><?php echo esc_attr($item_arr[0]); ?></strong>
																				<?php echo esc_attr($item_arr[1]); ?> <i
                                                                                        class="fa fa-times"
                                                                                        data-ticket-platform="<?php echo esc_attr($item_arr[0]); ?>"
                                                                                        data-ticket-url="<?php echo esc_attr($item_arr[1]); ?>"></i>
                                                                            </li>
																			<?php
																		}
																	}
																	?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                        <div class="row">
                                                            <div class="form-group">
                                                                <div class="col-md-4">
                                                                    <select class="form-control event-tickets-platforms lp-dashboard-select-box">
                                                                        <option>
																			<?php echo esc_html__('Facebook', 'listingpro'); ?>
                                                                        </option>
                                                                        <option>
																			<?php echo esc_html__('Twitter', 'listingpro'); ?>
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-7">
                                                                    <input type="text"
                                                                           class="lp-dashboard-text-field form-control event-ticket-url"
                                                                           placeholder="<?php echo esc_html__('https://facebook.com/listingprowp', 'listingpro'); ?>">
                                                                </div>
                                                                <div class="col-md-1 lp-dashboard-event-tick-btn padding-left-0">
                                                                    <i class="fa fa-plus-square"
                                                                       id="add-event-ticket-platform"></i>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="lp-coupon-box-row">
                                                        <div class="row">
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="">
                                                                    <input value="<?php echo get_the_title($eLID); ?>"
                                                                           type="text" class="form-control" disabled>
                                                                </div>
                                                            </div>
                                                            <div class="form-group col-sm-6 text-right">
                                                                <button class="lp-coupns-btns cancel-update"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                                                <button class="lp-save-events lp-coupns-btns"
                                                                        data-eid="<?php echo esc_attr($eID); ?>"
                                                                        data-uid="<?php echo esc_attr($user_id); ?>"
                                                                        class="lp-coupns-btns"><?php echo esc_html__('save', 'listingpro'); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
						<?php
						endwhile;
							wp_reset_postdata();
						endif;
						?>
                    </div>
                    <div class="tab-pane fade" id="tab4default">
						<?php
						if ($a_events->have_posts()) : while ($a_events->have_posts()) : $a_events->the_post();
							$eID        = get_the_ID();
							$time_noww  = strtotime("today");
							$eDate      = get_post_meta($eID, 'event-date', true);
							$eDateE     = get_post_meta($eID, 'event-date-e', true);
							if ($eDate > $time_noww) :
								$eLID = get_post_meta($eID, 'event-lsiting-id', true);
								$eLoc   = get_post_meta($eID, 'event-loc', true);
								$tUrl   = get_post_meta($eID, 'ticket-url', true);
								$eTime  = get_post_meta($eID, 'event-time', true);
								$eTitle = get_the_title(get_the_ID());
								?>
                                <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                    <div class="col-md-3 lp-content-before-after"
                                         data-content="<?php esc_html_e('Title', 'listingpro'); ?>">
                                        <div class="lp-deal-title">
                                            <p title="<?php echo get_the_title(get_the_ID()); ?>">
												<?php echo get_the_title(get_the_ID()); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 lp-content-before-after"
                                         data-content="<?php esc_html_e('Listing', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
                                            <p><a href="<?php echo esc_url(get_permalink($eLID)); ?>"
                                                  target="_blank"><?php echo wp_kses_post(get_the_title($eLID)); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-2 lp-content-before-after"
                                         data-content="<?php esc_html_e('Event Date', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
											<?php if ( ! empty($eDate)) { ?>
                                                <p><?php echo date('M d, Y', $eDate); ?></p>
											<?php } ?>
                                        </div>
                                    </div>
                                    <div class="col-md-1 lp-content-before-after  padding-0"
                                         data-content="<?php esc_html_e('Event Time', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
                                            <p><?php echo esc_attr($eTime); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center lp-content-before-after"
                                         data-content="<?php esc_html_e('Status', 'listingpro'); ?>">
                                        <div class="clearfix">
                                            <div class="lp-listing-pay-outer">
                                                <a class="lp-listing-pay-button button-upcoming">
													<?php esc_html_e('upcoming', 'listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
							<?php
							endif;
						endwhile;
							wp_reset_postdata();
						endif;
						?>
                    </div>
                    <div class="tab-pane fade" id="tab2default">
						<?php
						if ($a_events->have_posts()) : while ($a_events->have_posts()) : $a_events->the_post();
							$eID            = get_the_ID();
							$time_noww      = strtotime("today");
							$eDate          = get_post_meta($eID, 'event-date', true);
							$eDateE         = get_post_meta($eID, 'event-date-e', true);
							if (($eDateE && $time_now < $eDateE) || ( ! $eDateE && $time_now < $eDate) || (date("Y-m-d", $eDate) == date("Y-m-d"))) :
								if (($time_now >= $eDate) || ( ! $eDate && $time_now >= $eDateE) || (date("Y-m-d", $eDate) == date("Y-m-d"))) :
									$eLID = get_post_meta($eID, 'event-lsiting-id', true);
									$eLoc   = get_post_meta($eID, 'event-loc', true);
									$tUrl   = get_post_meta($eID, 'ticket-url', true);
									$eTime  = get_post_meta($eID, 'event-time', true);
									$eTitle = get_the_title(get_the_ID());
									?>
                                    <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                        <div class="col-md-3 lp-content-before-after"
                                             data-content="<?php esc_html_e('Title', 'listingpro'); ?>">
                                            <div class="lp-deal-title">
                                                <p title="<?php echo get_the_title(get_the_ID()); ?>">
													<?php echo get_the_title(get_the_ID()); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 lp-content-before-after"
                                             data-content="<?php esc_html_e('Listing', 'listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><a href="<?php echo esc_url(get_permalink($eLID)); ?>"
                                                      target="_blank"><?php echo wp_kses_post(get_the_title($eLID)); ?></a>
                                                </p>
                                            </div>
                                        </div>
                                        <div class="col-md-2 lp-content-before-after"
                                             data-content="<?php esc_html_e('Event Date', 'listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
												<?php if ( ! empty($eDate)) { ?>
                                                    <p><?php echo date('M d, Y', $eDate); ?></p>
												<?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-1 lp-content-before-after  padding-0"
                                             data-content="<?php esc_html_e('Event Time', 'listingpro'); ?>">
                                            <div class="lp-listing-expire-section">
                                                <p><?php echo esc_attr($eTime); ?></p>
                                            </div>
                                        </div>
                                        <div class="col-md-3 text-center lp-content-before-after"
                                             data-content="<?php esc_html_e('Status', 'listingpro'); ?>">
                                            <div class="clearfix">
                                                <div class="lp-listing-pay-outer">
                                                    <a class="lp-listing-pay-button"> <?php esc_html_e('active', 'listingpro'); ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="clearfix"></div>
                                    </div>
								<?php
								endif;
							endif;
						endwhile;
							wp_reset_postdata();
						endif;
						?>
                    </div>
                    <div class="tab-pane fade" id="tab3default">
						<?php
						if ($a_events->have_posts()) : while ($a_events->have_posts()) : $a_events->the_post();
							$eID        = get_the_ID();
							$eDate      = get_post_meta($eID, 'event-date', true);
							$eDateE     = get_post_meta($eID, 'event-date-e', true);
							if (($eDateE && $time_now > $eDateE) || ( ! $eDateE && $time_now > $eDate)) :
								$eLID = get_post_meta($eID, 'event-lsiting-id', true);
								$eLoc   = get_post_meta($eID, 'event-loc', true);
								$tUrl   = get_post_meta($eID, 'ticket-url', true);
								$eTime  = get_post_meta($eID, 'event-time', true);
								$eTitle = get_the_title(get_the_ID());
								?>
                                <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                    <div class="col-md-3 lp-content-before-after"
                                         data-content="<?php esc_html_e('Title', 'listingpro'); ?>">
                                        <div class="lp-deal-title">
                                            <p title="<?php echo get_the_title(get_the_ID()); ?>">
												<?php echo get_the_title(get_the_ID()); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 lp-content-before-after"
                                         data-content="<?php esc_html_e('Listing', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
                                            <p><a href="<?php echo esc_url(get_permalink($eLID)); ?>"
                                                  target="_blank"><?php echo wp_kses_post(get_the_title($eLID)); ?></a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-2 lp-content-before-after"
                                         data-content="<?php esc_html_e('Event Date', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
                                            <p><?php echo date('M d, Y', $eDate); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-1 lp-content-before-after padding-0"
                                         data-content="<?php esc_html_e('Event Time', 'listingpro'); ?>">
                                        <div class="lp-listing-expire-section">
                                            <p><?php echo esc_attr($eTime); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-3 text-center lp-content-before-after"
                                         data-content="<?php esc_html_e('Status', 'listingpro'); ?>">
                                        <div class="clearfix">
                                            <div class="lp-listing-pay-outer">
                                                <a class="lp-listing-pay-button inactive">
													<?php esc_html_e('Inactive', 'listingpro'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
							<?php
							endif;
						endwhile;
							wp_reset_postdata();
						endif;
						?>
                    </div>
                </div>
            </div>
        </div>
	<?php endif; ?>
    <div id="events-form-toggle" style="display: none;">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height lp-container-center">
            <div class="margin-bottom-10 clearfix">
                <a href="" class="lp-view-all-btn"><i class="fa fa-angle-left" aria-hidden="true"></i>
					<?php echo esc_html__('All Events', 'listingpro'); ?></a>
                <h5 class="margin-top-0 clearfix"><?php echo esc_html__('Create New event', 'listingpro'); ?>
                    <a href=""
                       data-imgsrc="<?php echo get_template_directory_uri(); ?>/assets/images/examples/example-event.jpg"
                       data-expandimage="bird" id="pop" class="lp-view-larg-btn"><i class="fa fa-eye"
                                                                                    aria-hidden="true"></i> <?php echo esc_html__('Full View Example', 'listingpro'); ?>
                    </a>
                </h5>
            </div>
            <div class="lp-coupns-form-outer">
                <form class="lp-coupons-form-inner" id="lp-events-form">
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Choose an event organizer's listing", 'listingpro'); ?>
                                <span>*</span></label>
                            <div class="lp-listing-selecter-drop">
                                <div class="lp-pp-noa-tip">
                                    <i class="fa fa-exclamation" aria-hidden="true"></i>
									<?php echo esc_html__('Event not allowed or already an event with this listing.', 'listingpro'); ?>
                                </div>
								<?php
								echo lp_get_listing_dropdown('event-listing', 'select2-ajax', 'event-listing', 'event_id', 'events');
								?>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Event Title", 'listingpro'); ?>
                                <span>*</span></label>
                            <input name="event-title" id="event-title" class="form-control lp-dashboard-text-field"
                                   value="" type="text"
                                   placeholder="<?php echo esc_html__('Give it a short quick name', 'listingpro'); ?>">
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group events-map-wrap">
                            <div class="lp-coordinates clearfix">
                                <a data-type="gaddress"
                                   class="btn-link googleAddressbtn active events-dash"><?php esc_html_e('Search By Google', 'listingpro'); ?></a>
                                <a data-type="gaddresscustom"
                                   class="btn-link googleAddressbtn events-dash"><?php esc_html_e('Manual Coordinates', 'listingpro'); ?></a>
                                <a data-type="gaddresscustom" class="btn-link googledroppin events-dash"
                                   data-toggle="modal" data-target="#modal-doppin"><i
                                            class="fa fa-map-pin"></i><?php esc_html_e('Drop Pin', 'listingpro'); ?>
                                </a>
                            </div>
                            <label class="lp-dashboard-top-label" for="inputAddress"
                                   class="googlefulladdress lp-dashboard-top-label"><?php esc_html_e('Event Location', 'listingpro'); ?>
                                <span>*</span></label>
                            <input type="text" class="form-control form-control-st lp-dashboard-text-field"
                                   name="gAddress" id="inputAddress"
                                   placeholder="<?php echo esc_html__('Address for Google Maps', 'listingpro'); ?>
" autocomplete="off">
                            <div class="lp-custom-lat clearfix">
                                <label class="lp-dashboard-top-label"
                                       for="inputAddress"><?php esc_html_e('Add Custom Address', 'listingpro'); ?></label>
                                <input type="text" class="form-control form-control-st lp-dashboard-text-field"
                                       name="gAddresscustom" id="inputAddresss"
                                       placeholder="<?php echo esc_html__('Add address here', 'listingpro'); ?>">
                                <div class="row hiddenlatlong">
                                    <div class="col-md-6 col-xs-6">
                                        <label for="latitude"><?php esc_html_e('Latitude', 'listingpro'); ?></label>
                                        <input class="form-control lp-dashboard-text-field" type="hidden"
                                               placeholder="40.7143528" id="latitude" name="latitude">
                                    </div>
                                    <div class="col-md-6 col-xs-6">
                                        <label for="longitude lp-dashboard-text-field"><?php esc_html_e('Longitude', 'listingpro'); ?></label>
                                        <input class="form-control" type="hidden" placeholder="-74.0059731"
                                               id="longitude" name="longitude">
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row event_start_end">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Event Starts", 'listingpro'); ?>
                                <span>*</span></label>
                            <div class="row">
                                <div class="col-sm-6 ">
                                    <div class="pos-relative">
                                        <span class="lp-field-icon"><i class="fa fa-calendar"
                                                                       aria-hidden="true"></i></span>
                                        <input name="event-date-s" id="event-date-s" type="text"
                                               class="lp-pos-relative-input form-control lp-dashboard-text-field"
                                               placeholder="<?php echo esc_html__('Date', 'listingpro'); ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="pos-relative" id="time-switch">
                                        <span class="lp-field-icon"><i class="fa fa-clock-o"
                                                                       aria-hidden="true"></i></span>
                                        <input name="event-time" id="event-time" type="text"
                                               class="form-control datetimepicker1 lp-dashboard-text-field lp-pos-relative-input "
                                               placeholder="<?php echo esc_html__(' Time', 'listingpro'); ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="lp-invoices-all-stats-on-off coupons-fields-switch clearfix lp-dashboard-switcher padding-0">
                            <h5 class="clearfix"> <?php esc_html_e('Set end date ', 'listingpro'); ?> <label
                                        class="switch">
                                    <input data-target="date"
                                           class="form-control switch-checkbox lp-event-end-date-switch" type="checkbox"
                                           checked="">
                                    <div class="slider round"></div>
                                </label>
                            </h5>
                        </div>
                        <div class="form-group " id="date-switch">
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Event Ends", 'listingpro'); ?>
                                <span>*</span></label>
                            <div class="row">
                                <div class="col-sm-6 ">
                                    <div class="pos-relative" id="date-switch">
                                        <span class="lp-field-icon"><i class="fa fa-calendar"
                                                                       aria-hidden="true"></i></span>
                                        <input name="event-date-e" id="event-date-e" type="text"
                                               class="lp-pos-relative-input form-control lp-dashboard-text-field"
                                               placeholder="<?php echo esc_html__('Date', 'listingpro'); ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-sm-6 ">
                                    <div class="pos-relative" id="time-switch">
                                        <span class="lp-field-icon"><i class="fa fa-clock-o"
                                                                       aria-hidden="true"></i></span>
                                        <input name="event-time-e" id="event-time-e" type="text"
                                               class="lp-pos-relative-input form-control datetimepicker1 lp-dashboard-text-field"
                                               placeholder="<?php echo esc_html__('Time', 'listingpro'); ?>"
                                               autocomplete="off">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"
                                   for="event-description"><?php echo esc_html__('Event Description', 'listingpro'); ?></label>
                            <textarea
                                    placeholder="<?php echo esc_html__('e.g Enter description about your event', 'listingpro'); ?>"
                                    id="event-description" name="event-description" type="text"
                                    class="form-control lp-dashboard-des-field" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"><?php esc_html_e("Event Tickets", 'listingpro'); ?></label>
                            <div class="event-tickets-list-dash">
                                <input id="event-tickets-data" name="event-tickets-data" type="hidden"
                                       class="form-control lp-dashboard-text-field">
                                <ul class="clearfix margin-top-10"></ul>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <select class="form-control event-tickets-platforms lp-dashboard-select-box">
                                        <option>Facebook</option>
                                        <option>Twitter</option>
                                    </select>
                                </div>
                                <div class="col-md-7">
                                    <input type="text" class="form-control event-ticket-url lp-dashboard-text-field"
                                           placeholder="<?php echo esc_html__('Event URL', 'listingpro'); ?>">
                                </div>
                                <div class="col-md-1 lp-dashboard-event-tick-btn padding-left-0">
                                    <i class="fa fa-plus-square" id="add-event-ticket-platform"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <label class="lp-dashboard-top-label"><?php esc_html_e("Event Featured Image", 'listingpro'); ?></label>
                        <div class="jFiler-input-dragDrop pos-relative event-featured-image-wrap-dash">
                            <div class="removeable-image upload-field dashboard-upload-field new-file-upload">
								<?php echo do_shortcode('[frontend-button]'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row lp-save-btn-container">
                        <div class="row">
                            <div class="form-group col-sm-12 clarfix">
                                <a href=""
                                   class="lp-unsaved-btn"><?php echo esc_html__('Unsaved Event', 'listingpro'); ?></a>
                                <button id="lp-save-events" data-uid="<?php echo esc_attr($user_id); ?>"
                                        class="lp-coupns-btns pull-right"><?php echo esc_html__('save', 'listingpro'); ?></button>
                                <button data-cancel="events"
                                        class="lp-coupns-btns cancel-ad-new-btn pull-right lp-margin-right-10 "><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>