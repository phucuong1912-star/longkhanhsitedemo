<?php
require_once(ABSPATH . 'wp-admin/includes/screen.php');

//for paypal & stripe
if (!empty($_POST['subscr_id']) && isset($_POST['subscr_id'])) {
    $subscrip_id = $_POST['subscr_id'];
    $uid = $_POST['subscriber_id'];
    global $listingpro_options;
    if (strpos($subscrip_id, 'sub_') !== false && !isset($_POST['payment_method'])) {
        /* stripe */
        include_once (WP_PLUGIN_DIR ."/listingpro-plugin/inc/stripe/stripe-php/init.php");
        $strip_sk = $listingpro_options['stripe_secrit_key'];
        \Stripe\Stripe::setApiKey($strip_sk);
        try {
            $subscription = \Stripe\Subscription::retrieve($subscrip_id);
            $subscription->cancel();
        } catch (Exception $e) {

        }
    }elseif (strpos($subscrip_id, 'SUB_') !== false && isset($_POST['payment_method']) && $_POST['payment_method'] == 'paystack' ) {
        do_action( 'lp_paystack_unsubscribe', $subscrip_id, $_POST['email_token'] );
    }elseif (isset($_POST['payment_method']) && $_POST['payment_method'] == 'payfast' ) {
        do_action( 'lp_payfast_unsubscribe', $subscrip_id, $_POST['token'] );
    }elseif (isset($_POST['payment_method']) && $_POST['payment_method'] == 'mollie' ) {
        do_action( 'lp_mollie_unsubscribe', $subscrip_id, $uid );
    }elseif (isset($_POST['payment_method']) && $_POST['payment_method'] == 'flutterwave' ) {
        do_action( 'lp_flutterwave_unsubscribe', $subscrip_id, $uid );
    }elseif (strpos($subscrip_id, 'sub_') !== false && isset($_POST['payment_method']) && $_POST['payment_method'] == 'razorpay' ) {
        do_action( 'lp_razorpay_unsubscribe', $subscrip_id );
    } else {
        /* paypal */
        lp_cancel_recurring_profile($subscrip_id);
    }
    $userSubscriptions = get_user_meta($uid, 'listingpro_user_sbscr', true);
    if (!empty($userSubscriptions)) {
        foreach ($userSubscriptions as $key => $subscription) {
            $subscr_id = $subscription['subscr_id'];
            $subscr_listing_id = $subscription['listing_id'];


            if ($subscr_id == $subscrip_id) {

                $table = 'listing_orders';
                $summary = 'expired';
                $data = array('summary' => $summary);
                $where = array('post_id' => $subscr_listing_id);
                lp_update_data_in_db($table, $data, $where);

                unset($userSubscriptions[$key]);
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                /* user email */
                $author_obj = get_user_by('id', $uid);
                $user_email = $author_obj->user_email;
                $usubject = $listingpro_options['listingpro_subject_cancel_subscription'];
                $ucontent = $listingpro_options['listingpro_content_cancel_subscription'];

                $website_url = site_url();
                $website_name = get_option('blogname');
                $listing_title = get_the_title($subscr_listing_id);
                $listing_url = get_the_permalink($subscr_listing_id);
                $user_name = $author_obj->user_login;
                $usubject = lp_sprintf2("$usubject", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'user_name' => "$user_name",
                    'website_name' => "$website_name"
                ));

                $ucontent = lp_sprintf2("$ucontent", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'user_name' => "$user_name",
                    'website_name' => "$website_name"
                ));

                lp_mail_headers_append();
                wp_mail($user_email, $usubject, $ucontent, $headers);
                /* admin email */
                $adminemail = get_option('admin_email');
                $asubject = $listingpro_options['listingpro_subject_cancel_subscription_admin'];
                $asubject = lp_sprintf2("$asubject", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'website_name' => "$website_name",
                ));
                $acontent = $listingpro_options['listingpro_content_cancel_subscription_admin'];
                $acontent = lp_sprintf2("$acontent", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'website_name' => "$website_name",
                ));
                wp_mail($adminemail, $asubject, $acontent, $headers);
                lp_mail_headers_remove();
            }
        }
    }
    /* removing user meta */
    if (!empty($userSubscriptions)) {
        update_user_meta($uid, 'listingpro_user_sbscr', $userSubscriptions);
    } else {
        delete_user_meta($uid, 'listingpro_user_sbscr');
    }
    /* end removing user meta */
}





/* for stripe */
if (!empty($_POST['subscr_idss']) && isset($_POST['subscr_idss'])) {
    $subscrip_id = $_POST['subscr_id'];
    $uid = $_POST['subscriber_id'];
    global $listingpro_options;
    include_once (WP_PLUGIN_DIR ."/listingpro-plugin/inc/stripe/stripe-php/init.php");
    $strip_sk = $listingpro_options['stripe_secrit_key'];
    \Stripe\Stripe::setApiKey($strip_sk);
    $subscription = \Stripe\Subscription::retrieve($subscrip_id);
    $subscription->cancel();
    $userSubscriptionss = get_user_meta($uid, 'listingpro_user_sbscr', true);
    $userSubscriptions = array_reverse($userSubscriptionss);
    if (!empty($userSubscriptions)) {
        foreach ($userSubscriptions as $key => $subscription) {
            $subscr_id = $subscription['subscr_id'];
            $subscr_listing_id = $subscription['listing_id'];

            if ($subscr_id == $subscrip_id) {

                $table = 'listing_orders';
                $summary = 'expired';
                $data = array('summary' => $summary);
                $where = array('post_id' => $subscr_listing_id);
                lp_update_data_in_db($table, $data, $where);

                unset($userSubscriptions[$key]);
                $headers[] = 'Content-Type: text/html; charset=UTF-8';
                /* user email */
                $author_obj = get_user_by('id', $uid);
                $user_email = $author_obj->user_email;
                $usubject = $listingpro_options['listingpro_subject_cancel_subscription'];
                $ucontent = $listingpro_options['listingpro_content_cancel_subscription'];

                $website_url = site_url();
                $website_name = get_option('blogname');
                $listing_title = get_the_title($subscr_listing_id);
                $listing_url = get_the_permalink($subscr_listing_id);
                $user_name = $author_obj->user_login;
                $usubject = lp_sprintf2("$usubject", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'user_name' => "$user_name",
                    'website_name' => "$website_name"
                ));

                $ucontent = lp_sprintf2("$ucontent", array(
                    'website_url' => "$website_url",
                    'listing_title' => "$listing_title",
                    'listing_url' => "$listing_url",
                    'user_name' => "$user_name",
                    'website_name' => "$website_name"
                ));


                lp_mail_headers_append();
                wp_mail($user_email, $usubject, $ucontent, $headers);
                /* admin email */
                $adminemail = get_option('admin_email');
                $asubject = $listingpro_options['listingpro_subject_cancel_subscription_admin'];
                $acontent = $listingpro_options['listingpro_content_cancel_subscription_admin'];
                wp_mail($adminemail, $asubject, $acontent, $headers);
                lp_mail_headers_remove();
            }
        }
    }
    /* removing user meta */
    if (!empty($userSubscriptions)) {
        update_user_meta($uid, 'listingpro_user_sbscr', $userSubscriptions);
    } else {
        delete_user_meta($uid, 'listingpro_user_sbscr');
    }
    /* end removing user meta */
}


/* ---------------------------------------------------
  adding invoice page
  ---------------------------------------------------- */

if (!function_exists('listingpro_register_subscription_page')) {

    function listingpro_register_subscription_page() {
        add_menu_page(
            __('Subscriptions', 'listingpro-plugin'), 'Subscription', 'manage_options', 'lp-listings-subscription', 'listingpro_subscription_page', plugins_url('listingpro-plugin/images/icon-subscr.png'), 30
        );
        wp_enqueue_style("panel_style", WP_PLUGIN_URL . "/listingpro-plugin/assets/css/custom-admin-pages.css", false, "1.0", "all");
    }

}

add_action('admin_menu', 'listingpro_register_subscription_page');

if (!function_exists('listingpro_subscription_page')) {

    function listingpro_subscription_page() {
        global $listingpro_options;
        $userSubscriptions;
        $userSubscriptionsp = array();
        $subscription_exist = false;
        include_once (WP_PLUGIN_DIR ."/listingpro-plugin/inc/stripe/stripe-php/init.php");
        $strip_sk = $listingpro_options['stripe_secrit_key'];
        \Stripe\Stripe::setApiKey($strip_sk);
        $currency = listingpro_currency_sign();
        ?>
        <div class="wrap listingpro-coupons">
        <h1 class="wp-heading-inline"><?php esc_html_e('Subscriptions', 'listingpro-plugin'); ?></h1>


        <div id="posts-filter" method="get">

            <div class="tablenav top">

                <div class="alignright">
                    <p class="search-box">
                        <input type="search" id="lp_invoiceInput" onkeyup="lpSearchDataInInvoice()" class="button" placeholder="<?php echo esc_html__('Search Subscriptions', 'listingpro-plugin'); ?>">
                    </p>
                </div>

                <br class="clear">
            </div>


            <div class="listingpro_coupon_table">
                <table class="table wp-list-table widefat fixed striped posts">
                    <thead>
                    <tr>
                        <!-- <th><?php echo esc_html__('No.', 'listingpro-plugin'); ?></th> -->

                        <th id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>


                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a><span><?php echo esc_html__('Status', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a>
                        </th>

                        <th class="manage-column column-tags"><?php echo esc_html__('User', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Listing', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Subscription', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Total', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Next Payment', 'listingpro-plugin'); ?></th>

                        <th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $users = get_users(array('fields' => array('ID')));
                    ?>
                    <?php
                    global $wpdb;
                    $currency_position = lp_theme_option('pricingplan_currency_position');
                    foreach ($users as $user_id) {
                        $user_id = $user_id->ID;
                        $user_obj = get_user_by('id', $user_id);
                        $user_login = $user_obj->user_login;
                        $userSubscriptions = '';
                        $userSubscriptionss = get_user_meta($user_id, 'listingpro_user_sbscr', true);
                        if (is_array($userSubscriptionss)) :
                            $userSubscriptions = array_reverse($userSubscriptionss);
                        else :
                            $userSubscriptions = get_user_meta($user_id, 'listingpro_user_sbscr', true);
                        endif;
                        if (!empty($userSubscriptions) && count($userSubscriptions) > 0) {
                            $subscription_exist = true;
                            $n = 1;
                            foreach ($userSubscriptions as $subscription) {
                                $subscr_id = $subscription['subscr_id'];
                                $taxStatus = '';
                                $unsubscr_btn = true;
                                if (strpos($subscr_id, 'sub_') !== false && !isset($subscription['method'])) {
                                    /* stripe */
                                    try {
                                        $subscrObj = \Stripe\Subscription::retrieve($subscr_id);
                                        if ($subscrObj->status != 'active'){
                                            break;
                                        }
                                        $subscrID = $subscrObj->id;
                                        $planStripe = $subscrObj->plan;
                                        $stripePrice = $planStripe->amount;
                                        $stripePrice = (float) $stripePrice / 100;
                                        $stripePrice = round($stripePrice, 2);
                                        $nextpayment = $subscrObj->current_period_end;
                                    } catch (Exception $e) {
                                        $subscrID = $subscr_id."<br>".'Invalid/Updated Stripe Api Keys';
                                        $stripePrice = 0;
                                        $nextpayment = '';
                                        $unsubscr_btn = false;
                                    }
                                } elseif (isset($subscription['method']) && $subscription['method'] == 'razorpay') {
                                    $subscrID = $subscription['subscr_id'];
                                    $unsub_btn = '<a class="delete-subsc-btn razorpay-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                    $nextpayment = strtotime("+ " . get_post_meta($plan_id, 'plan_time', true) . " days");
                                    
                                    $interval = get_post_meta( $plan_id, 'razorpay_recurring_duration', true );
                                    $interval = ($interval == '') ? 'monthly' : $interval;
                                    if( $interval == 'daily' ){
                                        $nextpayment = strtotime("+ 1 days");
                                    }else if( $interval == 'weekly' ){
                                        $nextpayment = strtotime("+ 7 days");
                                    }else if( $interval == 'monthly' ){
                                        $nextpayment = strtotime("+ 1 month");
                                    }else if( $interval == 'yearly' ){
                                        $nextpayment = strtotime("+ 1 year");
                                    }
                                } elseif (isset($subscription['method']) && $subscription['method'] == 'payfast') {
                                    $subscrID = $subscription['subscr_id'];
                                    $unsub_btn = '<a class="delete-subsc-btn payfast-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                    if (isset($subscription['next_payment'])) {
                                        $nextpayment = $subscription['next_payment'];
                                    } else {
                                        $nextpayment = strtotime("now");
                                    }
                                } elseif (isset($subscription['method']) && $subscription['method'] == 'mollie') {
                                    $subscrID = $subscription['subscr_id'];
                                    $unsub_btn = '<a class="delete-subsc-btn mollie-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                    if (isset($subscription['next_payment'])) {
                                        $nextpayment = $subscription['next_payment'];
                                    } else {
                                        $nextpayment = strtotime("now");
                                    }
                                } elseif (isset($subscription['method']) && $subscription['method'] == 'flutterwave') {
                                    $subscrID = $subscription['subscr_id'];
                                    $unsub_btn = '<a class="delete-subsc-btn flutterwave-unsub" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                    if (isset($subscription['next_payment'])) {
                                        $nextpayment = $subscription['next_payment'];
                                    } else {
                                        $nextpayment = strtotime("now");
                                    }
                                } elseif (strpos($subscr_id, 'SUB_') !== false) {
                                    $subscrID = $subscription['subscr_id'];
                                    if (isset($subscription['next_payment'])) {
                                        $nextpayment = $subscription['next_payment'];
                                    } else {
                                        $nextpayment = strtotime("now");
                                    }
                                    $unsub_btn = '<a class="delete-subsc-btn paystack-unsub" data-mailToekn="' . $subscription['email_tokent'] . '" href="' . $subscrID . '" data-cmsg="' . esc_html__('Are you sure you want to proceed action?', 'listingpro') . '">' . esc_html__('Unsubscribe', 'listingpro') . '</a>';
                                } else {
                                    /* paypal */
                                    $subscrIDOBJ = lp_retreive_recurring_profile($subscr_id);
                                    $subscrID = $subscrIDOBJ['PROFILEID'];
                                    $stripePrice = $subscrIDOBJ['AMT'];
                                    $nextpayment = $subscrIDOBJ['NEXTBILLINGDATE'];
                                    $nextpayment = strtotime($nextpayment);
                                    if( !isset($subscrIDOBJ['LASTPAYMENTDATE']) && isset($subscrIDOBJ['BILLINGFREQUENCY'])){
                                        $nextpayment = strtotime(date(get_option('date_format'), $nextpayment). ' + '. $subscrIDOBJ['BILLINGFREQUENCY'] .' days');
                                    }
                                }

                                $plan_id = $subscription['plan_id'];
                                $listing_id = $subscription['listing_id'];
                                $listing_title = get_the_title($listing_id);
                                $plan_title = get_the_title($plan_id);
                                $plan_price = get_post_meta($plan_id, 'plan_price', true);



                                $dbprefix = $wpdb->prefix;
                                $myPrice = $wpdb->get_row("SELECT * FROM " . $dbprefix . "listing_orders WHERE plan_id = $plan_id AND post_id = $listing_id ORDER BY main_id DESC");
                                if ((isset($subscription['method'])) && ($subscription['method'] == 'razorpay' || $subscription['method'] == 'paystack' || $subscription['method'] == 'payfast' || $subscription['method'] == 'mollie' || $subscription['method'] == 'flutterwave')) {
                                    if(isset($myPrice->price)){
                                        $stripePrice = $myPrice->price;
                                    }
                                }else if( $stripePrice == 0){
                                    if(isset($myPrice->price)){
                                         $stripePrice = $myPrice->price;
                                     } 
                                 }
                                $currency = isset($myPrice->currency) ? $myPrice->currency : $currency;
                                if ($stripePrice == $plan_price) {
                                    $taxStatus = esc_html__('exc. tax', 'listingpro-plugin');
                                } else {
                                    $plan_price = $stripePrice;
                                    $taxStatus = esc_html__('inc. tax', 'listingpro-plugin');
                                }
                                ?>


                                <tr class="<?php echo $listing_id; ?>">
                                    <td><input type="checkbox"></td>
                                    <td><input class="alert alert-success <?php echo $stripePrice; ?>" type="button" name="lp_delte_coupon_submit" value="<?php echo esc_html__('Active', 'listingpro-plugin'); ?>" ></td>
                                    <td><?php echo $user_login; ?></td>
                                    <td><?php echo $listing_title; ?></td>
                                    <td><?php echo $subscrID; ?></td>
                                    <td>
                                        <?php
                                        if ((isset($subscription['method'])) && ($subscription['method'] == 'razorpay' || $subscription['method'] == 'paystack' || $subscription['method'] == 'payfast' || $subscription['method'] == 'mollie' || $subscription['method'] == 'flutterwave')) {
                                            if( $currency_position == 'right' ){
                                                echo $myPrice->price . $currency . " ($taxStatus)";
                                            }else{
                                                echo $currency . $myPrice->price . " ($taxStatus)";
                                            }
                                        } else {
                                            if( $currency_position == 'right' ){
                                                echo $plan_price . $currency . " ($taxStatus)";
                                            }else{
                                                echo $currency . $plan_price . " ($taxStatus)";
                                            }
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <?php
                                        if($nextpayment != '' ){
                                            echo date_i18n(get_option('date_format'), $nextpayment);
                                        }else{
                                            echo '-';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                    <?php if( $unsubscr_btn ){ ?>
                                        <form class="wp-core-ui" class="" id="subscription_cancel" name="subscription_cancel" method="post">
                                            <input type="submit" name="subscription_cancel_submit" class="button action" value="<?php echo esc_html__('Unsubscribe', 'listingpro-plugin'); ?>" onclick="return window.confirm('<?php echo esc_html__('Are you sure you want to proceed action?', 'listingpro-plugin'); ?>');">
                                            <input type="hidden" name="subscr_id" value="<?php echo $subscrID; ?>">
                                            <input type="hidden" name="subscriber_id" value="<?php echo $user_id; ?>">
                                            <?php if (isset($subscription['method']) && $subscription['method'] == 'paystack') { ?>
                                                <input type="hidden" name="payment_method" value="paystack">
                                                <input type="hidden" name="email_token" value="<?php echo $subscription['email_tokent']; ?>">
                                            <?php } elseif(isset($subscription['method']) && $subscription['method'] == 'payfast') { ?>
                                                <input type="hidden" name="payment_method" value="payfast">
                                                <input type="hidden" name="token" value="<?php echo $subscription['token']; ?>">
                                            <?php } elseif (isset($subscription['method']) && $subscription['method'] == 'razorpay') { ?>
                                                <input type="hidden" name="payment_method" value="razorpay">
                                            <?php } elseif (isset($subscription['method']) && $subscription['method'] == 'mollie') { ?>
                                                <input type="hidden" name="payment_method" value="mollie">
                                            <?php } elseif (isset($subscription['method']) && $subscription['method'] == 'flutterwave') { ?>
                                                <input type="hidden" name="payment_method" value="flutterwave">
                                            <?php } ?>
                                        </form>
                                            <?php }else{ echo '-'; } ?>
                                    </td>

                                </tr>

                                <?php
                                $n++;
                            }
                        }
                    }
                    ?>
                    </tbody>
                    <tfoot>
                    <tr>
                        <!-- <th><?php echo esc_html__('No.', 'listingpro-plugin'); ?></th> -->

                        <th id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></th>


                        <th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
                            <a><span><?php echo esc_html__('Status', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a>
                        </th>

                        <th class="manage-column column-tags"><?php echo esc_html__('User', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Listing', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Subscription', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Total', 'listingpro-plugin'); ?></th>
                        <th class="manage-column column-tags"><?php echo esc_html__('Next Payment', 'listingpro-plugin'); ?></th>

                        <th class="manage-column column-tags"><?php echo esc_html__('Action', 'listingpro-plugin'); ?></th>
                    </tr>
                    </tfoot>
                </table>

            </div>

            </form>


            <?php
            if ($subscription_exist == false) {
                echo '<p>' . esc_html('Sorry! There is no subscription yet', 'listingpro-plugin') . '<p>';
            }
            ?>
        </div>

        <!--search-->
        <script>
            function lpSearchDataInInvoice() {
                var input, filter, table, tr, td, i;
                input = document.getElementById("lp_invoiceInput");
                filter = input.value.toUpperCase();
                table = document.getElementsByClassName("wp-list-table");
                for (j = 0; j < table.length; j++) {
                    tr = table[j].getElementsByTagName("tr");
                    for (i = 0; i < tr.length; i++) {
                        td = tr[i].getElementsByTagName("td")[4];
                        if (td) {
                            if (td.innerHTML.toUpperCase().indexOf(filter) > -1) {
                                tr[i].style.display = "";
                            } else {
                                tr[i].style.display = "none";
                            }
                        }
                    }
                }
            }



        </script>
        <?php
    }

}
?>