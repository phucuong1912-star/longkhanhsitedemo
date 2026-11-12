<?php
global $listingpro_options;
if (is_page_template('template-dashboard.php')) { ?>
    <!-- dynamic invoice -->
    <div class="modal fade lp-modal-list" id="modal-invoice">
        <div class="modal-content">

            <div class="modal-body">
                <?php echo esc_html__('Content is loading...', 'listingpro'); ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal"><?php esc_html_e('Close', 'listingpro'); ?></button>
                <a href="#" class="lp-print-list btn-first-hover"><?php esc_html_e('Print', 'listingpro'); ?></a>
            </div>
        </div>
    </div>
    <!-- dynamic invoice -->
<?php
}
?>
<!-- Login Popup style2 -->
<?php
$popup_style  = isset($listingpro_options['login_popup_style']) ? $listingpro_options['login_popup_style'] : '';
if ($popup_style == 'style2' && wp_is_mobile()) {
?>
    <div class="modal fade style2-popup-login" id="app-view-login-popup" role="dialog" style="overflow: visible !important; opacity: 1;">
        <?php
        get_template_part('mobile/templates/login-reg-popup');
        ?>
    </div>
<?php
}
?>

<!-- Login Popup -->
<?php
$listing_mobile_view    = isset($listingpro_options['single_listing_mobile_view']) ? $listingpro_options['single_listing_mobile_view'] : '';
if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
} else {
    if (!is_user_logged_in()) {
        //login poup
?>
        <?php
        $popup_style = $listingpro_options['login_popup_style'];


        if (!isset($popup_style) || empty($popup_style) || !$popup_style) {
            $popup_style = 'style1';
        }
        if ($popup_style == 'style2') {
        ?>

            <div class="app-view-popup-style" data-target="#app-view-login-popup">
                <!--ajax based content-->
            </div>

        <?php
        } else {
        ?>
            <div class="md-modal md-effect-3" id="modal-3">
                <!--ajax based content-->
            </div>

        <?php
        } ?>

<?php }
}

?>


<!-- ../Login Popup -->
<?php if (is_singular('listing')) { ?>
    <?php
    global $post;

    $post_id          = '';
    $post_title       = '';
    $post_url         = '';
    $post_author_id   = '';
    $post_author_meta = '';
    $author_nicename  = '';
    $author_url       = '';

    $post_id    = $post->ID;
    $post_title = $post->post_title;
    $post_url   = get_permalink($post_id);

    $post_author_id   = $post->post_author;
    $post_author_meta = get_user_by('id', $post_author_id);
    //print_r($post_author_meta);
    if (!empty($post_author_meta)) {
        $author_nicename   = $post_author_meta->user_nicename;
        $author_user_email = $post_author_meta->user_email;
    }

    $author_url = get_author_posts_url($post_author_id);

    ?>

    <!-- Popup Open -->
    <div class="md-modal md-effect-3 single-page-popup" id="modal-6">
        <div class="md-content cotnactowner-box">
            <h3><?php esc_html('Contact Owner', 'listingpro'); ?></h3>
            <div class="">
                <form class="form-horizontal" method="post" id="contactowner">
                    <div class="form-group">
                        <input type="text" class="form-control" name="name" id="name" placeholder="<?php esc_html_e('Name:', 'listingpro'); ?>" required>
                    </div>
                    <div class="form-group">
                        <input type="email" class="form-control" name="email6" id="email6" placeholder="<?php esc_html_e('Email:', 'listingpro'); ?>" required>
                    </div>
                    <div class="form-group">
                        <textarea class="form-control" rows="5" name="message1" id="message1" placeholder="<?php esc_html_e('Message:', 'listingpro'); ?>"></textarea>
                    </div>
                    <div class="form-group mr-bottom-0">
                        <input type="submit" value="<?php esc_html_e('Submit', 'listingpro'); ?>" class="lp-review-btn btn-second-hover">
                        <input type="hidden" class="form-control" name="post_title" value="<?php echo esc_attr($post_title); ?>">
                        <input type="hidden" class="form-control" name="post_url" value="<?php echo esc_attr($post_url); ?>">
                        <i class="fa fa-circle-o-notch fa-spin fa-2x formsubmitting"></i>
                        <span class="statuss"></span>
                    </div>
                </form>
                <a class="md-close"><i class="fa fa-close"></i></a>
            </div>
        </div>
    </div>
    <!-- Popup Close -->
    <div class="md-modal md-effect-3" id="modal-4">
        <div class="md-content">
            <div id="map" class="singlebigpost"></div>
            <a class="md-close widget-map-click"><i class="fa fa-close"></i></a>
        </div>
    </div>
    <div class="md-modal md-effect-3" id="modal-5">
        <div class="md-content">
            <div id="mapp" class="singlebigpostfgf"></div>
            <a class="md-close widget-mapdfd-click"><i class="fa fa-close"></i></a>

        </div>
    </div>

<?php } ?>


<?php
/* ========for tempalte dashbaord======== */
if (is_page_template('template-dashboard.php')) {
?>

    <!-- Modal Packages -->
    <div class="modal fade" id="modal-packages" role="dialog">
        <div class="modal-dialog  lp-change-plan-popup">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-body">
                    <div class="lp-loadingPlans" data-default="<?php echo esc_attr__('Loading...', 'listingpro'); ?>">
                        <p></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--modal 7, for app view filters-->
    <?php
}
if (is_page() && !is_front_page()) {
    global $post;
    $_elementor_controls_usage = get_post_meta($post->ID, '_elementor_data');
    $elementor_shortcode = '';

    if (!empty($_elementor_controls_usage) && is_array($_elementor_controls_usage)) {
        $decoded_data = json_decode($_elementor_controls_usage[0]);
        if ($decoded_data && isset($decoded_data[0]->elements[0]->elements[0]->widgetType)) {
            $elementor_shortcode = $decoded_data[0]->elements[0]->elements[0]->widgetType;
        }
    }
    if (has_shortcode($post->post_content, 'listingpro_submit') || has_shortcode($post->post_content, 'listingpro_edit') || $elementor_shortcode == 'edit-listing' || $elementor_shortcode == 'submit-listing') {
    ?>
        <!--modal droppin, for custom lat and long via drag-->
        <div id="modal-doppin" class="modal fade" role="dialog" data-lploctitlemap='<?php echo esc_html__("Your Location", "listingpro"); ?>'>
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
<?php
    }
}
?>

<div class="md-overlay"></div> <!-- Overlay for Popup -->

<!-- top notificaton bar -->
<div class="lp-top-notification-bar"></div>
<!-- end top notification-bar -->


<!-- popup for quick view --->

<div class="md-modal md-effect-3" id="listing-preview-popup">
    <div class="container">
        <div class="md-content ">
            <div class="row popup-inner-left-padding ">


            </div>
        </div>
    </div>
    <a class="md-close widget-map-click"><i class="fa fa-close"></i></a>
</div>
<div class="md-overlay content-loading"></div>


<div class="md-modal md-effect-map-btn" id="grid-show-popup">
    <div class="container">
        <div class="md-content ">
            <div class="row grid-show-popup" data-loader="<?php echo get_template_directory_uri(); ?>/assets/images/content-loader.gif">
                <img alt='image' src="<?php echo get_template_directory_uri(); ?>/assets/images/content-loader.gif" />
            </div>
        </div>
    </div>
    <a class="md-close widget-map-click"><i class="fa fa-close"></i></a>
</div>

<!--hidden google map-->
<div id="lp-hidden-map" style="width:300px;height:300px;position:absolute;left:-300000px"></div>

<!-- new code for 2.6.15 -->
<div class="modal fade" id="lp_report_listing" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <input type="hidden" id="lp_posttype" name="lp_posttype" value="">
    <input type="hidden" id="lp_postid" name="lp_postid" value="">
    <input type="hidden" id="lp_reportedby" name="lp_reportedby" value="">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <button id="lp_close_reason" type="button" class="close-report-btn" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true"><i class="fa fa-close"></i></span>
            </button>
            <div class="modal-body">
                <form>
                    <div class="form-group">
                        <label for="lp_report_reason" class="col-form-label"><?php echo esc_html__('Why Are You Reporting this ', 'listingpro'); ?><span class="lp-report-title"><?php echo esc_html__('Listing?', 'listingpro'); ?></span></label>
                        <textarea class="form-control" id="lp_report_reason" name="lp_report_reason" placeholder="<?php echo esc_html__('Please provide your reason here...', 'listingpro'); ?>"></textarea>
                    </div>
                </form>
                <button id="lp-report-this-listing" type="button" class="btn btn-primary"><?php echo esc_html__('Report Now!', 'listingpro'); ?></button>
            </div>
        </div>
    </div>
</div>
<!-- new code for 2.6.15 -->