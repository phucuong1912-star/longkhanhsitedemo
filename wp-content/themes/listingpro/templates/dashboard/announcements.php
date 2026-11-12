<?php
$current_user = wp_get_current_user();
$user_id = $current_user->ID;

$a_args = array(
    'post_type' => 'listing',
    'post_status' => 'publish',
    'posts_per_page' => -1,
    'author' => $user_id,
    'meta_key' => 'lp_listing_announcements',
    'meta_compare' => 'EXISTS'
);
$a_listings = new WP_Query($a_args);
$count_a_listings = $a_listings->found_posts;
//$lp_user_announcements = get_user_meta($user_id, 'lp_user_announcements', true);

?>


<?php
ajax_response_markup();
?>
<!-- Modal -->
<div class="modal fade" id="dashboard-delete-modal" tabindex="-1" role="dialog" aria-labelledby="dashboard-delete-modal" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <?php echo esc_html__( 'are you sure you want to delete?', 'listingpro' ); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                <button type="button" class="btn btn-primary dashboard-confirm-del-btn"><?php echo esc_html__( 'Delete', 'listingpro' ); ?></button>
            </div>
        </div>
    </div>
</div>
<div class="tab-pane fade in active lp-announcement-form padding-bottom-30" id="lp-announcement-form">
    <?php
    if( $count_a_listings == 0 ):
        ?>
        <div class="lp-blank-section">
            <div class="col-md-12 blank-left-side">
                <img alt="image" src="<?php echo listingpro_icons_url('lp_blank_trophy'); ?>">
                <h1><?php echo esc_html__('Nothing but this golden trophy!', 'listingpro'); ?></h1>
                <p class="margin-bottom-20"><?php echo esc_html__('You must be here for the first time. If you like to add some thing, click the button below.', 'listingpro'); ?></p>
                <button data-form="announcements" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span><?php echo esc_html__('Add new announcement', 'listingpro'); ?> </button>
            </div>
        </div>
        <?php
    else:
        ?>
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-11 align-center">
		
            <div class="lp-add-menu-outer clearfix">
                <h5><?php esc_html_e('All Announcements', 'listingpro'); ?></h5>
                <button data-form="announcements" class="lp-add-new-btn add-new-open-form"><span><i class="fa fa-plus" aria-hidden="true"></i></span> <?php esc_html_e('Add new', 'listingpro'); ?></button>
            </div>
            <div class="panel-body">
                <div class="lp-main-title clearfix">
                    <div class="col-md-3"><p><?php esc_html_e('Listing title', 'listingpro'); ?></p></div>
                    <div class="col-md-7"><p><?php esc_html_e('Description', 'listingpro'); ?></p></div>
                    <div class="col-md-2"><p class="text-center"><?php esc_html_e('On/off', 'listingpro'); ?></p></div>
                </div>
                <div class="tab-content clearfix">
                    <div class="tab-pane fade in active" id="tab1default">
                        <?php
                        while ( $a_listings->have_posts() ): $a_listings->the_post();
                            global $post;
                            $lid    =   get_the_ID();
                            $lp_listing_announcements  =   get_post_meta( $lid, 'lp_listing_announcements', true );
                            foreach ( $lp_listing_announcements as $k => $v ):
                                $annID  =   $lid.'-'.$k;
                                $ann_status =   'active';
                                $ann_status_data    =   'active';
                                if( isset( $v['annStatus'] ) && $v['annStatus'] == 0 )
                                {
                                    $ann_status =   '';
                                    $ann_status_data    =   'inactive';
                                }
                                $annSt  =   'style1';
                                if( isset($v['annSt'] ) && !empty( $v['annSt'] ) )
                                {
                                    $annSt  =    $v['annSt'];
                                }
                                $checked    =   '';
                                if( $ann_status == 'active' )
                                {
                                    $checked    =   'checked';
                                }
                                ?>
                                <div class="lp-listing-outer-container clearfix lp-coupon-outer-container">
                                    <div class="col-md-3 lp-content-before-after" data-content="<?php esc_html_e('Call to action title','listingpro'); ?>">
                                        <div class="lp-announcement-title">
                                            <p><?php echo get_the_title($v['annLI']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-7 lp-content-before-after" data-content="<?php esc_html_e('Description','listingpro'); ?>">
                                        <div class="lp-deal-title">
                                            <p><?php echo wp_kses_post($v['annMsg']); ?></p>
                                        </div>
                                    </div>
                                    <div class="col-md-2 text-center lp-content-before-after" data-content="<?php esc_html_e('On/Off','listingpro'); ?>">
                                        <div class="clearfix">

                                            <div class="lp-invoices-all-stats-on-off lp-display-inline">
                                                <h5 class="clearfix">
                                                    <label class="switch pull-left">
                                                        <input data-uid="<?php echo esc_attr($user_id); ?>" data-annID="<?php echo esc_attr($annID); ?>" <?php echo wp_kses_post($checked); ?> data-status="<?php echo wp_kses_post($ann_status_data); ?>" class="form-control switch-checkbox on-off-ann" type="checkbox" >
                                                        <div class="slider round"></div>
                                                    </label>
                                                </h5>
                                            </div>
                                            <div class="lp-display-inline lp-pull-left-new">
                                                <div class="lp-dot-extra-buttons">
                                                    <img alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAABtSURBVEhLYxgFgwN4R2UKekXl7gJhEBsqTDnwiM4N8YrO/Q/GUTlBUGHKAciVntG5O0DYJTSNHyo8UoFnVI61V0yuFZRLHQAyEBZ5PpHZllBhygHIMKjB/6hqMAiADKS6oUMPjGbpUUANwMAAAIAtN4uDPUCkAAAAAElFTkSuQmCC">
                                                    <ul class="lp-user-menu list-style-none">
                                                        <li><a class="ann-edit" href="" data-targetid="<?php echo esc_attr($annID); ?>" data-annID="<?php echo esc_attr($annID); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-pencil-square-o"></i><span><?php esc_html_e('Edit', 'listingpro'); ?></span></a></li>
                                                        <li><a class="ann-del del-this" href="" data-targetID="<?php echo esc_attr($annID); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><i class="fa fa-trash-o"></i><span><?php esc_html_e('Delete', 'listingpro'); ?></span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div style="display: none;" id="update-wrap-<?php echo esc_attr($annID); ?>" class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height margin-top-30">
                                        <div class="lp-coupns-form-outer">
                                            <div class="lp-voupon-box">
                                                <form class="lp-coupons-form-inner">
                                                    <div class="lp-coupon-box-row">
                                                        <div class="row">
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="margin-bottom-20">
                                                                    <label class="lp-dashboard-top-label" for="announcements-title-<?php echo esc_attr($annID); ?>"><?php echo esc_html__('Call to action title', 'listingpro'); ?></label>
                                                                    <input type="text" class="lp-dashboard-text-field form-control"
                                                                           value="<?php echo wp_kses_post($v['annTI']); ?>"
                                                                           name="announcements-title-<?php echo esc_attr($annID); ?>" id="announcements-title-<?php echo esc_attr($annID); ?>"
                                                                           placeholder="<?php echo esc_html__('Call to action title', 'listingpro'); ?>"/>
                                                                </div>
                                                                <div class="form-group margin-bottom-20">
                                                                    <label class="lp-dashboard-top-label" for="announcements-icon-<?php echo esc_attr($annID); ?>"><?php echo esc_html__('Icon', 'listingpro'); ?> (<a target="_blank" href="https://fontawesome.com/v4.7.0/icons/" style="font-size: 12px; font-weight: 400;"><?php echo esc_html__('Font Awesome', 'listingpro'); ?></a>)</label>

                                                                    <input type="text" class="lp-dashboard-text-field form-control" value="<?php echo wp_kses_post($v['annIC']); ?>" name="announcements-icon-<?php echo esc_attr($annID); ?>"
                                                                           id="announcements-icon-<?php echo wp_kses_post($annID); ?>" placeholder="<?php echo esc_html__('use font awesome', 'listingpro'); ?> class e.g fa fa-bullhorn "/>
                                                                </div>

                                                            </div>
                                                            <div class="form-group col-sm-6 ">
                                                                <div class="form-group">
                                                                    <div class="margin-bottom-20">
                                                                        <label class="lp-dashboard-top-label" for="announcements-btn-text-<?php echo esc_attr($annID); ?>"><?php echo esc_html__('Button Text', 'listingpro'); ?></label>
                                                                        <input value="<?php echo wp_kses_post($v['annBT']); ?>" name="announcements-btn-text-<?php echo esc_attr($annID); ?>" id="announcements-btn-text-<?php echo esc_attr($annID); ?>" type="text" class="lp-dashboard-text-field form-control" placeholder="Announcement">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <div class="">
                                                                        <label  class="lp-dashboard-top-label" for="announcements-btn-link-<?php echo esc_attr($annID); ?>"><?php echo esc_html__('Button Link', 'listingpro'); ?></label>
                                                                        <input value="<?php echo wp_kses_post($v['annBL']); ?>" type="url" class="lp-dashboard-text-field form-control" name="announcements-btn-link-<?php echo esc_attr($annID); ?>" id="announcements-btn-link-<?php echo esc_attr($annID); ?>" placeholder="e.g https://">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="col-sm-12 margin-top-20">
                                                                <label class="lp-dashboard-top-label" for="announcements-message-<?php echo esc_attr($annID); ?>"><?php echo esc_html__('Description', 'listingpro'); ?></label>
                                                                <textarea class="lp-dashboard-des-field form-control" name="announcements-message-<?php echo esc_attr($annID); ?>" id="announcements-message-<?php echo esc_attr($annID); ?>" rows="10"><?php echo wp_kses_post($v['annMsg']); ?></textarea>
                                                            </div>
                                                            <div class="form-group col-sm-12 margin-top-20 margin-bottom-10">
                                                                <button class="lp-coupns-btns lp-edit-announcements" data-lid="<?php echo get_the_ID(); ?>" data-annID="<?php echo esc_attr($annID); ?>" data-uid="<?php echo esc_attr($user_id); ?>"><?php echo esc_html__('Save', 'listingpro'); ?></button>
                                                                <button class="lp-coupns-btns cancel-update"><?php echo esc_html__('Cancel', 'listingpro'); ?></button>
                                                            </div>
                                                        </div>
                                                    </div>


                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; endwhile; wp_reset_postdata(); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php
    endif;
    ?>
    <div style="display: none;" id="announcements-form-toggle" class="panel with-nav-tabs panel-default lp-dashboard-tabs lp-left-panel-height  lp-container-center">
        <div class="lp-review-sorting clearfix">
            <a href="" class="lp-view-all-btn all-with-refresh"><i class="fa fa-angle-left" aria-hidden="true"></i> <?php echo esc_html__('All Announcements', 'listingpro'); ?></a>
            <h5 class="margin-top-0 clearfix"><?php echo esc_html__('Add Announcements', 'listingpro'); ?>
                <a href="#" data-imgsrc="<?php echo get_template_directory_uri(); ?>/assets/images/examples/example-announcement.jpg" data-expandimage="bird" id="pop" class="lp-view-larg-btn"><i class="fa fa-eye" aria-hidden="true"></i> <?php echo esc_html__('Full View Example', 'listingpro'); ?></a>
            </h5>
        </div>
        <div class="lp-coupns-form-outer">
            <div class="lp-coupons-form-inner">
                <form class="lp-add-announcement-form">
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <label class="lp-dashboard-top-label"><?php echo esc_html__('Live Preview', 'listingpro'); ?></label>
                            <div class="ann-preivew-wrap">
                                <div class="active-preview">
                                    <div class="lp-listing-announcement">
                                        <div class="announcement-wrap">
                                            <i class="fa fa-bullhorn" aria-hidden="true"></i>
                                            <img alt="image" src="" height="40" width="40" style="display: none;">
                                            <p>
                                                <strong><?php echo esc_html__('e.g. 46% Off - Two Vouchers Each Valid for One Large Specialty Pizza', 'listingpro'); ?></strong>
                                                <span><?php echo esc_html__('We are proud to announce launch of new branch', 'listingpro'); ?></span>
                                            </p>
                                            <a target="_blank" href="#"
                                               class="announcement-btn"><?php echo esc_html__('Announcement', 'listingpro'); ?></a>
                                            <div class="clearfix"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group select2-dash">
                            <div class="lp-pp-noa-tip">
                                <i class="fa fa-exclamation" aria-hidden="true"></i> <?php echo esc_html__('Announcements not allowed with this listing. Please upgrade your plan.', 'listingpro'); ?>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <label  class="lp-dashboard-left-label" for="announcements-listing"><?php echo esc_html__('choose a Listing', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                                </div>
                                <div class="col-md-6">
                                    <?php
                                    echo lp_get_listing_dropdown('announcements-listing', 'select2-ajax', 'announcements-listing', 'announcment', null);
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="lp-dashboard-left-label" ><?php echo esc_html__('Choose call to action type', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                                </div>
                                <div class="col-md-6">
                                    <select class="form-control lp-dashboard-select-box" name="ann-style" id="ann-style">
                                        <option data-icon="fa fa-bullhorn"
                                                data-title="<?php echo esc_html__('Announcement Title', 'listingpro'); ?>"
                                                data-st="announcement"
                                                data-des="<?php echo esc_html__('We are proud to announce launch of new branch', 'listingpro'); ?>"><?php echo esc_html__('Announcement', 'listingpro'); ?></option>
                                        <option data-icon="fa fa-adjust" data-title="Save 20% on 3 nights stay"
                                                data-st="book-now"><?php echo esc_html__('Book Now', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-cart-plus"
                                                data-title="<?php echo esc_html__('Save 20% on 3 nights stay', 'listingpro'); ?>"
                                                data-st="buy-tickets"
                                                data-des=""><?php echo esc_html__('Buy Tickets', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-id-card"
                                                data-title="<?php echo esc_html__('Contact Us Title', 'listingpro'); ?>"
                                                data-st="contact-us"
                                                data-des="<?php echo esc_html__('Mention Listingpro! save 5% on services', 'listingpro'); ?>"><?php echo esc_html__('Contact Us', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-envelope-open"
                                                data-title="<?php echo esc_html__('Free appetizer with purchase of two entrees', 'listingpro'); ?>"
                                                data-st="get-offer"
                                                data-des=""><?php echo esc_html__('Get Offer', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-bullhorn"
                                                data-title="<?php echo esc_html__('Get a quote today and save up-to 40%', 'listingpro'); ?>"
                                                data-st="get-quote"
                                                data-des=""><?php echo esc_html__('Get Quote', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-adjust"
                                                data-title="<?php echo esc_html__('Join Now Title', 'listingpro'); ?>"
                                                data-st="join-now"
                                                data-des="<?php echo esc_html__('3 Free classes + 50% off from first purchase', 'listingpro'); ?>"><?php echo esc_html__('Join Now', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-cart-plus"
                                                data-title="<?php echo esc_html__('Learn More Title', 'listingpro'); ?>"
                                                data-st="learn-more"
                                                data-des="<?php echo esc_html__('Checkout our new happy hour specialist', 'listingpro'); ?>"><?php echo esc_html__('Learn More', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-id-card"
                                                data-title="<?php echo esc_html__('Coupon Title', 'listingpro'); ?>"
                                                data-st="print-coupon"
                                                data-des="<?php echo esc_html__('Free appetizer with purchase of two entrees', 'listingpro'); ?>"><?php echo esc_html__('Print Coupon', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-cart-plus"
                                                data-title="<?php echo esc_html__('Title here10', 'listingpro'); ?>"
                                                data-st="reserve-now"
                                                data-des="<?php echo esc_html__('50% off your two purchase & first two Move-in-Trucks', 'listingpro'); ?>"><?php echo esc_html__('Reserve Now', 'listingpro'); ?></option>

                                        <option data-icon="fa fa-envelope-open"
                                                data-title="<?php echo esc_html__('Same day appointment available', 'listingpro'); ?>"
                                                data-st="schedule-appointment"
                                                data-des=""><?php echo esc_html__('Schedule Appointment', 'listingpro'); ?></option>

                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">
                            <div class="row">
                                <div class="col-md-6">
                                    <label  class="lp-dashboard-left-label" for="announcements-icon"><?php echo esc_html__('choose an Icon', 'listingpro'); ?> (<a target="_blank" href="https://fontawesome.com/v4.7.0/icons/" style="font-size: 12px; font-weight: 400;"><?php echo esc_html__('Font Awesome', 'listingpro'); ?></a>)</label>
                                </div>
                                <div class="col-md-6">
                                    <input class="lp-dashboard-text-field" type="text" class="form-control" value="" name="announcements-icon"
                                           id="announcements-icon" placeholder="<?php echo esc_html__('use font awesome class e.g fa fa-bullhorn', 'listingpro'); ?> "/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">

                            <label  class="lp-dashboard-top-label" for="announcements-btn-text"><?php echo esc_html__('Call to action title', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>

                            <input type="text" class="form-control lp-dashboard-text-field"
                            placeholder="<?php echo esc_html__('e.g. 46% Off - Two Vouchers Each Valid for One Large Specialty Pizza', 'listingpro'); ?>"
                                   name="announcements-title" id="announcements-title"/>

                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="form-group">

                            <label class="lp-dashboard-top-label" for="announcements-message"><?php echo esc_html__('Description', 'listingpro'); ?><span class="lp-requires-filed">*</span></label>
                            <textarea class="form-control lp-dashboard-des-field" name="announcements-message"
                                      id="announcements-message" rows="3"
                                      placeholder="<?php echo esc_html__('We are proud to announce launch of new branch', 'listingpro'); ?>"></textarea>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">

                                    <label class="lp-dashboard-top-label" for="announcements-btn-text"><?php echo esc_html__('Button Text', 'listingpro'); ?></label>

                                    <input type="text" class="form-control lp-dashboard-text-field"
                                           value="<?php echo esc_html__('Announcement', 'listingpro'); ?>"
                                           name="announcements-btn-text" id="announcements-btn-text"
                                           placeholder="<?php echo esc_html__('Button Text', 'listingpro'); ?>"/>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="lp-dashboard-top-label" for="announcements-btn-link"><?php echo esc_html__('Button Link', 'listingpro'); ?></label>
                                    <input type="url" class="form-control lp-dashboard-text-field" name="announcements-btn-link"
                                           id="announcements-btn-link"
                                           placeholder="<?php echo esc_html__( 'https://', 'listingpro' ); ?>"/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="lp-coupon-box-row lp-save-btn-container">
                        <div class="form-group">
                            <div class="row">
                                <div class="form-group col-sm-12 clearfix">
                                    <a href="" class="lp-unsaved-btn"><?php echo esc_html__( 'Unsaved Event', 'listingpro' ); ?></a>
                                    <button id="ad-announcement-btn" data-uid="<?php echo esc_attr($user_id); ?>" class="lp-coupns-btns pull-right "><?php echo esc_html__( 'save', 'listingpro' ); ?></button>
                                    <button data-cancel="announcements" id="cancelLpAnnouncment" class="lp-coupns-btns cancel-ad-new-btn pull-right lp-margin-right-10 "><?php echo esc_html__( 'Cancel', 'listingpro' ); ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>