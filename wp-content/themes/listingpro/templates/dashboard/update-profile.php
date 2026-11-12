<?php

if (!is_user_logged_in()) {
    wp_redirect(home_url());
    exit;
}
require_once(ABSPATH . 'wp-admin/includes/user.php');
$currentUserID = get_current_user_id();
$lpAllMessges  = get_user_meta($currentUserID, 'lead_messages', true);

?>


<div class="tab-pane fade in active" id="updateprofile">

    <?php
    do_action('lp_pdf_enqueue_scripts');
    global $listingpro_options;
    $current_user = wp_get_current_user();
    $user_id      = $current_user->ID;
    // User Name
    $user_fname = get_the_author_meta('first_name', $user_id);
    $user_lname = get_the_author_meta('last_name', $user_id);
    $user_dname = get_the_author_meta('display_name', $user_id);
    // User contact meta
    $user_address  = get_the_author_meta('address', $user_id);
    $user_address2 = get_the_author_meta('address2', $user_id);
    $user_city     = get_the_author_meta('city', $user_id);
    $user_zipcode  = get_the_author_meta('zipcode', $user_id);
    $user_state    = get_the_author_meta('state', $user_id);
    $user_country  = get_the_author_meta('country', $user_id);
    $user_phone    = get_the_author_meta('phone', $user_id);
    $user_email    = get_the_author_meta('user_email', $user_id);
    // User Social links
    $user_facebook  = get_the_author_meta('facebook', $user_id);
    $user_linkedin  = get_the_author_meta('linkedin', $user_id);
    $user_instagram = get_the_author_meta('instagram', $user_id);
    $user_twitter   = get_the_author_meta('twitter', $user_id);
    $user_pinterest = get_the_author_meta('pinterest', $user_id);
    // User BIO
    $user_desc = get_the_author_meta('description', $user_id);
    $user_ID   = $user_id;

    $gSiteKey      = '';
    $gSiteKey      = $listingpro_options['lp_recaptcha_site_key'];
    $enableCaptcha = lp_check_receptcha('lp_recaptcha_userprofile');

    if ($user_ID) {

        /* delete user */
        if (isset($_POST['lp_assign_data'])) {
            if (isset($_POST['lp_delte_user_nonce'])) {
                $assignListing = esc_html($_POST['lp_assign_data']);
                $current_user  = wp_get_current_user();
                if ($assignListing == "yes") {
                    //yes
                    $super_admins = get_users('role=administrator');
                    if (!empty($super_admins)) {
                        $adminID = $super_admins[0]->ID;
                        wp_delete_user($current_user->ID, $adminID);
                    } else {
                        if (is_multisite()) {
                            wpmu_delete_user($current_user->ID);
                        } else {
                            wp_delete_user($current_user->ID);
                        }
                    }
                    wp_redirect(home_url());
                    exit;
                } else {
                    if (is_multisite()) {
                        wpmu_delete_user($current_user->ID);
                    } else {
                        wp_delete_user($current_user->ID);
                    }
                    wp_redirect(home_url());
                    exit;
                }
            }
        }
        /* end delete user */

        if (isset($_POST['profileupdate'])) {
            $message = esc_html__('Your profile updated successfully.', 'listingpro');
            $mType   = 'success';
            $first         = sanitize_text_field(esc_html($_POST['first_name']));
            $last          = sanitize_text_field(esc_html($_POST['last_name']));
            $display       = sanitize_text_field(esc_html($_POST['display_name']));
            $email         = esc_html($_POST['email']);
            $user_phone    = esc_html($_POST['phone']);
            $user_address  = esc_html($_POST['address']);
            $user_address2 = esc_html($_POST['address2']);
            $user_city     = esc_html($_POST['city']);
            $user_zipcode  = esc_html($_POST['zipcode']);
            $user_state    = esc_html($_POST['state']);
            $user_country  = esc_html($_POST['country']);
            $description   = esc_html($_POST['desc']);

            $facebook  = esc_html($_POST['facebook']);
            $linkedin  = esc_html($_POST['linkedin']);
            $instagram = esc_html($_POST['instagram']);
            $twitter   = esc_html($_POST['twitter']);
            $pinterest = esc_html($_POST['pinterest']);
            $oldpassword      = esc_html($_POST['oldpassword']);
            $password         = esc_html($_POST['pwd']);
            $confirm_password = esc_html($_POST['confirm']);
            $stored_hash = $wpdb->get_var($wpdb->prepare("SELECT user_pass FROM $wpdb->users WHERE ID = %d", $user_ID));

            update_user_meta($user_ID, 'first_name', $first);
            update_user_meta($user_ID, 'last_name', $last);
            wp_update_user(array('ID' => $user_ID, 'display_name' => $display));
            update_user_meta($user_ID, 'phone', $user_phone);
            update_user_meta($user_ID, 'address', $user_address);
            update_user_meta($user_ID, 'address2', $user_address2);
            update_user_meta($user_ID, 'city', $user_city);
            update_user_meta($user_ID, 'zipcode', $user_zipcode);
            update_user_meta($user_ID, 'state', $user_state);
            update_user_meta($user_ID, 'country', $user_country);
            update_user_meta($user_ID, 'description', $description);

            update_user_meta($user_ID, 'facebook', $facebook);
            update_user_meta($user_ID, 'linkedin', $linkedin);
            update_user_meta($user_ID, 'instagram', $instagram);
            update_user_meta($user_ID, 'twitter', $twitter);
            update_user_meta($user_ID, 'pinterest', $pinterest);

            $your_image_url    = $_POST['your_author_image_url'];
            $author_avatar_url = get_user_meta($user_ID, "listingpro_author_img_url", true);
            if ($your_image_url != '') {
                update_user_meta($user_ID, 'listingpro_author_img_url', $your_image_url);
            } else {
                update_user_meta($user_ID, 'listingpro_author_img_url', $author_avatar_url);
            }

            if (isset($email) && is_email($email)) {
                wp_update_user(array('ID' => $user_ID, 'user_email' => $email));
            } else {
                $message = esc_html__('Please enter a valid email id.', 'listingpro');
                $mType   = 'error';
            }

            //new code 2.6.15
            if ($password || !empty($confirm_password) || !empty($oldpassword)) {
                if (empty($oldpassword)) {
                    $message = esc_html__('Please enter old passwrod', 'listingpro');
                    $mType   = 'error';
                } else if (!wp_check_password($oldpassword, $stored_hash, $user_id)) {
                    $message = esc_html__('Old passwrod is incorrect', 'listingpro');
                    $mType   = 'error';
                } else if (empty($password) && !empty($confirm_password)) {
                    $message = esc_html__('Password Mismatch', 'listingpro');
                    $mType   = 'error';
                }
                //end new code 2.6.15
                else if (strlen($password) < 5 || strlen($password) > 15) {
                    $message = esc_html__('Password must be 5 to 15 characters in length', 'listingpro');
                    $mType   = 'error';
                } //elseif( $password == $confirm_password ) {
                else if (isset($password) && $password != $confirm_password) {
                    $message = esc_html__('Password Mismatch', 'listingpro');
                    $mType   = 'error';
                } else if (isset($password) && !empty($password)) {
                    $lpCUser = wp_get_current_user();
                    $update  = wp_set_password($password, $user_ID);
                    wp_set_auth_cookie($lpCUser->ID);
                    wp_set_current_user($lpCUser->ID);
                    do_action('wp_login', $lpCUser->user_login, $lpCUser);
                    $message = esc_html__('Your profile updated successfully.', 'listingpro');
                    $mType   = esc_html__('success', 'listingpro');
                }
            }
            $updateTab = true;
        }
    }
    ?>
    <div class="user-recent-listings-inner tab-pane fade in active lp-update-profile-container" id="inbox">
        <div class="panel with-nav-tabs panel-default lp-dashboard-tabs col-md-12 lp-left-panel-height lp-update-profile">
            <div class="tab-header lp-update-password-outer">
                <h3><?php echo esc_html__('Update Profile Details', 'listingpro'); ?></h3>
            </div>
            <div class="updateprofile-tab">
                <div class="updateprofile-tab aligncenter">


                    <?php if (isset($message) && !empty($message)) { ?>
                        <div class="notification <?php echo esc_attr($mType); ?> clearfix">
                            <div class="noti-icon"></div>
                            <p><?php echo esc_html($message); ?></p>
                        </div>
                    <?php } ?>

                    <form class="form-horizontal" id="profileupdate" action="" method="POST" enctype="multipart/form-data" data-lp-recaptcha="<?php echo wp_kses_post($enableCaptcha); ?>" data-lp-recaptcha-sitekey="<?php echo wp_kses_post($gSiteKey); ?>">
                        <?php wp_nonce_field('profile_update_action', 'profile_update_nonce'); ?>
                        <div class="page-innner-container padding-40 lp-border lp-border-radius-8">
                            <div class="user-avatar-upload lp-border-bottom padding-bottom-45">
                                <div class="user-avatar-preview avatar-circle">
                                    <img class="author-avatar" src="<?php echo listingpro_author_image(); ?>" alt="userimg" />
                                </div>
                                <div class="user-avatar-description">
                                    <p class="paragraph-form">
                                        <?php echo esc_html__('Update your photo manually If the photo is not set the default Gravatar will be the same as your login email account', 'listingpro'); ?>
                                        <br>
                                    </p>
                                    <div class="upload-photo margin-top-25">
                                        <span class="file-input file-upload-btn btn-first-hover btn-file">
                                            <?php echo esc_html__('Upload Photo', 'listingpro'); ?>&hellip; <input class="upload-author-image" type="file" accept="image/*" />
                                        </span>
                                        <input class="criteria-image-url" id="your_image_url" type="text" size="36" name="your_author_image_url" style="display: none;" value="<?php if (isset($your_image_url)) {
                                                                                                                                                                                    echo esc_attr($your_image_url);
                                                                                                                                                                                } ?>" />
                                        <input class="criteria-image-id" id="your_image_id" type="text" size="36" name="your_author_image_id" style="display: none;" value="" />
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4">
                                    <label for="Fname"><?php echo esc_html__('First Name', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_fname); ?>" type="text" class="form-control" name="first_name" id="Fname" />
                                </div>
                                <div class="col-sm-4">
                                    <label for="Lname"><?php echo esc_html__('Last Name', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_lname); ?>" type="text" class="form-control" name="last_name" id="Lname" />
                                </div>
                                <div class="col-sm-4">
                                    <label for="Dname"><?php echo esc_html__('Display Name', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_dname); ?>" type="text" class="form-control" name="display_name" id="Dname" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="uemail"><?php echo esc_html__('Email', 'listingpro'); ?>*</label>
                                    <input value="<?php echo esc_attr($user_email); ?>" type="email" class="form-control" name="email" id="uemail" placeholder="<?php esc_html_e('eg. example@gmal.com', 'listingpro'); ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <label for="phone"><?php echo esc_html__('Phone', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_phone); ?>" type="text" class="form-control" name="phone" id="phone" placeholder="<?php echo esc_html__('+00-12345-7890', 'listingpro'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="address"><?php echo esc_html__('Adress 1st Line', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="address" id="address" placeholder="<?php esc_html_e('Your Address (1st line)', 'listingpro'); ?>" value="<?php echo esc_html($user_address); ?>">
                                </div>

                                <div class="col-sm-6">
                                    <label for="address"><?php echo esc_html__('Adress 2nd Line', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="address2" id="address2" placeholder="<?php esc_html_e('Your Address (2nd line)', 'listingpro'); ?>" value="<?php echo esc_html($user_address2); ?>">
                                </div>
                            </div>

                            <div class="form-group">

                                <div class="col-sm-3">
                                    <label for="address"><?php echo esc_html__('City', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="city" id="city" placeholder="<?php esc_html_e('Your City', 'listingpro'); ?>" value="<?php echo esc_html($user_city); ?>">
                                </div>

                                <div class="col-sm-3">
                                    <label for="address"><?php echo esc_html__('Zip Code', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="zipcode" id="zipcode" placeholder="<?php esc_html_e('Zip Code', 'listingpro'); ?>" value="<?php echo esc_html($user_zipcode); ?>">
                                </div>

                                <div class="col-sm-3">
                                    <label for="address"><?php echo esc_html__('State', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="state" id="state" placeholder="<?php esc_html_e('State', 'listingpro'); ?>" value="<?php echo esc_html($user_state); ?>">
                                </div>

                                <div class="col-sm-3">
                                    <label for="address"><?php echo esc_html__('Country *', 'listingpro'); ?></label>
                                    <input type="text" class="form-control" name="country" id="country" placeholder="<?php esc_html_e('Country', 'listingpro'); ?>" value="<?php echo esc_html($user_country); ?>" required>
                                </div>


                            </div>


                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="about"><?php echo esc_html__('About', 'listingpro'); ?></label>
                                    <textarea class="form-control" name="desc" id="about" rows="8" placeholder="<?php esc_html_e('Write about youself', 'listingpro'); ?>"><?php echo esc_html($user_desc); ?></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="facebook"><?php echo esc_html__('Facebook', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_facebook); ?>" type="text" class="form-control" name="facebook" id="facebook" placeholder="<?php esc_html_e('enter facebook profile url', 'listingpro'); ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <label for="twitter"><?php echo esc_html__('Twitter', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_twitter); ?>" type="text" class="form-control" name="twitter" id="twitter" placeholder="<?php esc_html_e('enter twitter profile url', 'listingpro'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="linkedin"><?php echo esc_html__('Linkedin', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_linkedin); ?>" type="text" class="form-control" name="linkedin" id="linkedin" placeholder="<?php esc_html_e('enter linkedin profile url', 'listingpro'); ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <label for="instagram"><?php echo esc_html__('Instagram', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_instagram); ?>" type="text" class="form-control" name="instagram" id="instagram" placeholder="<?php esc_html_e('enter instagram profile url', 'listingpro'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="pinterest"><?php echo esc_html__('Pinterest', 'listingpro'); ?></label>
                                    <input value="<?php echo esc_attr($user_pinterest); ?>" type="text" class="form-control" name="pinterest" id="pinterest" placeholder="<?php esc_html_e('enter pinterest profile url', 'listingpro'); ?>" />
                                </div>
                            </div>
                        </div>
                        <div class="clearfix margin-top-30"></div>
                        <div class="tab-header lp-update-password-outer margin-top-30">
                            <h3><?php echo esc_html__('Update Password', 'listingpro'); ?></h3>
                        </div>
                        <div class="page-innner-container padding-40 lp-border lp-border-radius-8 margin-bottom-30">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <label for="oldpassword"><?php echo esc_html__('Old Password', 'listingpro'); ?></label>
                                    <input type="password" class="form-control" name="oldpassword" id="oldpassword" placeholder="<?php esc_html_e('write old password', 'listingpro'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-6">
                                    <label for="npassword"><?php echo esc_html__('New Password', 'listingpro'); ?></label>
                                    <input type="password" class="form-control" name="pwd" id="npassword" placeholder="<?php esc_html_e('write new password', 'listingpro'); ?>" />
                                </div>
                                <div class="col-sm-6">
                                    <label for="rnpassword"><?php echo esc_html__('Repeat Password', 'listingpro'); ?></label>
                                    <input type="password" class="form-control" name="confirm" id="rnpassword" placeholder="<?php esc_html_e('write again your new password', 'listingpro'); ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <p class="paragraph-form"><?php echo esc_html__('Enter same password in both fields Use an uppercase letter and a number for stronger password', 'listingpro'); ?>
                                        .</p>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <input type="submit" name="profileupdate" value="<?php echo esc_html__('Update profile', 'listingpro'); ?>" class="lp-secondary-big-btn btn-first-hover" />
                                </div>
                            </div>

                        </div>

                    </form>

                    <!-- listingpro GDPR -->
                    <form class="form-horizontal" id="lp_delete_user_profile" action="" method="POST">
                        <div class="form-group">

                            <div class="col-sm-6 text-left">
                                <button type="button" class="btn btn-success btn-sm" id="lp_profile_pdf"><i class="fa fa-download"></i> <?php echo esc_html__('Download Profile', 'listingpro'); ?>
                                </button>
                            </div>

                            <?php
                            if (!current_user_can('administrator')) {
                            ?>

                                <div class="col-sm-6 text-right">
                                    <button type="button" id="lpwanttodelete" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#lp_delete_accountpopup"><i class="fa fa-trash"></i> <?php echo esc_html__('Delete Account', 'listingpro'); ?>
                                    </button>

                                    <?php wp_nonce_field('', 'lp_delte_user_nonce'); ?>
                                </div>

                                <!-- Modal -->
                                <div id="lp_delete_accountpopup" class="modal fade" role="dialog" data-backdrop="false">
                                    <div class="modal-dialog">

                                        <!-- Modal content-->
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">&times;
                                                </button>
                                                <h4 class="modal-title"><?php echo esc_html__('Delete Account', 'listingpro'); ?></h4>
                                            </div>
                                            <div class="modal-body">
                                                <p><?php echo esc_html__('What should be done with content owned by you?', 'listingpro'); ?></p>

                                                <div class="radio">
                                                    <label><input type="radio" class="lp_assign_data" name="lp_assign_data" value="no"><?php echo esc_html__('Delete all content.', 'listingpro'); ?>
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label><input type="radio" class="lp_assign_data" name="lp_assign_data" value="yes"><?php echo esc_html__('Attribute all content to super admin of website', 'listingpro'); ?>
                                                    </label>
                                                </div>

                                                <button type="submit" class="btn btn-danger margin-top-20 lp_delte_user_confirm" disabled><?php echo esc_html__('Confirm Deletion', 'listingpro'); ?></button>
                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo esc_html__('Close', 'listingpro'); ?></button>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                        </div>
                    </form>

                    <!-- end listingpro GDPR -->
                </div>
            </div>
            <!-- html for pdf -->
            <div id="lp_profile_for_pdf" style="display:none">
                <h2 style="margin-left:500px"><?php echo esc_html__('Profile Data', 'listingpro'); ?></h2>
                <ul class="list-group">
                    <li class="list-group-item"><?php echo esc_html__('First Name', 'listingpro'); ?>
                        : <?php echo esc_attr($user_fname); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Last Name', 'listingpro'); ?>
                        : <?php echo esc_attr($user_lname); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Email', 'listingpro'); ?>
                        : <?php echo esc_attr($user_email); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Phone', 'listingpro'); ?>
                        : <?php echo esc_attr($user_phone); ?></li>

                    <li class="list-group-item"><?php echo esc_html__('Facebook', 'listingpro'); ?>
                        : <?php echo esc_attr($user_facebook); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Twitter', 'listingpro'); ?>
                        : <?php echo esc_attr($user_twitter); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Linkedin', 'listingpro'); ?>
                        : <?php echo esc_attr($user_linkedin); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Instagram', 'listingpro'); ?>
                        : <?php echo esc_attr($user_instagram); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('Pinterest', 'listingpro'); ?>
                        : <?php echo esc_attr($user_pinterest); ?></li>

                    <li class="list-group-item"><?php echo esc_html__('Address', 'listingpro'); ?>
                        : <?php echo esc_attr($user_address); ?></li>
                    <li class="list-group-item"><?php echo esc_html__('About', 'listingpro'); ?>
                        : <?php echo esc_attr($user_desc); ?></li>

                </ul>
            </div>
            <!-- end html for pdf -->

        </div>


    </div>

</div>
<!--updateprofile-->