<?php
/* ============== ListingPro create pages ============ */
add_action('admin_init', 'check_pages_live');
if (!function_exists('check_pages_live')) {

    function check_pages_live()
    {

        if (class_exists('Redux')) {
            global $listingpro_options;
            global $opt_name;
            $opt_name = 'listingpro_options';
            $pricing_plan = isset($listingpro_options['pricing-plan']) ? $listingpro_options['pricing-plan'] : '';
			$submit_listing = isset($listingpro_options['submit-listing']) ? $listingpro_options['submit-listing'] : '';
			$listing_author = isset($listingpro_options['listing-author']) ? $listingpro_options['listing-author'] : '';
			$recreate_pages = isset($listingpro_options['listingpro_recreate_pages_switch']) ? $listingpro_options['listingpro_recreate_pages_switch'] : '';

            $PP_pageID = url_to_postid($pricing_plan);
            if ($PP_pageID) {
                $pricing_plan = get_permalink($PP_pageID);
            }
            $D_pageID = url_to_postid($listing_author);
            if ($D_pageID) {
                $listing_author = get_permalink($D_pageID);
            }
            $S_pageID = url_to_postid($submit_listing);
            if ($S_pageID) {
                $submit_listing = get_permalink($S_pageID);
            }

            if (!empty($recreate_pages) && ($recreate_pages == 'yes')) {
                if (empty($pricing_plan) && empty($submit_listing) && empty($listing_author) && empty($payment_checkout) && empty($payment_fail)) {
                    create_page_if_null('Dashboard', '', 'listing-author');
                    create_page_if_null('Submit Your Listing', '[vc_row row_type="row_full_center_content" bg_color="#eff3f6" bg_repeat="no-repeat" bg_attatch="scroll"][vc_column][listingpro_submit title="Add your Listing" subtitle="Add details about your listing"][/vc_column][/vc_row]', 'submit-listing');
                    create_page_if_null('Edit Your Listing', '[vc_row row_type="row_full_center_content" bg_color="#eff3f6" bg_repeat="no-repeat" bg_attatch="scroll"][vc_column][listingpro_edit title="Edit your Listing" subtitle="Edit details about your listing"][/vc_column][/vc_row]', 'edit-listing');
                    create_page_if_null('Select Your Plan', '[vc_row row_type="row_full_center_content" bg_color="#eff3f6"][vc_column][listingpro_pricing title="Add your Listing" subtitle="Add details about your listing" pricing_views="vertical_view"][/vc_column][/vc_row]', 'pricing-plan');

                    create_page_if_null('Payment Success', '', 'payment-success');
                    create_page_if_null('Payment Fail', '', 'payment-fail');
                    create_page_if_null('Payment Checkout', '[vc_row row_type="row_full_center_content" bg_color="#eff3f6" bg_repeat="no-repeat" bg_attatch="scroll"][vc_column][listingpro_checkout title="See listings and pay" subtitle="Select your listings and payment method to proceed"][/vc_column][/vc_row]', 'payment-checkout');
                }
            }
        }
    }
}

add_action('admin_init', 'check_set_options');
add_action("redux/options/listingpro_options/settings/change", "check_set_options");
if (!function_exists('check_set_options')) {

    function check_set_options()
    {
        $counter = 0;

        if (class_exists('Redux')) {
            global $listingpro_options;
            global $opt_name;
            $opt_name = 'listingpro_options';
            $pricing_plan = $listingpro_options['pricing-plan'];
            $submit_listing = $listingpro_options['submit-listing'];
            $edit_listing = $listingpro_options['edit-listing'];
            $listing_author = $listingpro_options['listing-author'];
            $payment_checkout = $listingpro_options['payment-checkout'];
            $payment_fail = $listingpro_options['payment_fail'];
            $payment_success = $listingpro_options['payment_success'];

            $PP_pageID = url_to_postid($pricing_plan);
            if ($PP_pageID) {
                $pricing_plan = get_permalink($PP_pageID);
            }
            $D_pageID = url_to_postid($listing_author);
            if ($D_pageID) {
                $listing_author = get_permalink($D_pageID);
            }
            $S_pageID = url_to_postid($submit_listing);
            if ($S_pageID) {
                $submit_listing = get_permalink($S_pageID);
            }
            $E_pageID = url_to_postid($edit_listing);
            if ($E_pageID) {
                $edit_listing = get_permalink($E_pageID);
            }

            if (empty($listing_author)) {
				$dashboard = get_posts([
					'title' => 'Dashboard',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
                if( isset($dashboard[0]) && !empty($dashboard) ){
                    $page = $dashboard[0];
                    $permalink = get_permalink($page);
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'listing-author', $permalink);
                    }
                }
            }

            if (empty($submit_listing)) {
				$submit = get_posts([
					'title' => 'Submit Your Listing',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($submit[0]) && !empty($submit) ){
                    $page = $submit[0];
                    $permalink = get_permalink( $page );
                    $status = get_post_status( $page );
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'submit-listing', $permalink);
                    }
                }
            }

            if (empty($edit_listing)) {
                $edit = get_posts([
					'title' => 'Edit Your Listing',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($edit[0]) && !empty($edit) ){
					$page = $edit[0];
                    $permalink = get_permalink($page);
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'edit-listing', $permalink);
                    }
                }
            }

            if (empty($pricing_plan)) {
                $plan = get_posts([
					'title' => 'Select Your Plan',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($plan[0]) && !empty($plan) ){
					$page = $plan[0];
                    $permalink = get_permalink($page);
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'pricing-plan', $permalink);
                    }
                }
            }

            if (empty($payment_checkout) || (get_post_status($payment_checkout) == '')) {
                $payment = get_posts([
					'title' => 'Payment Checkout',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($payment[0]) && !empty($payment) ){
					$page = $payment[0];
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'payment-checkout', $page);
                    }
                }
            }

            if (empty($payment_fail) || (get_post_status($payment_fail) == '')) {
                $fail = get_posts([
					'title' => 'Payment Fail',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($fail[0]) && !empty($fail) ){
					$page = $fail[0];
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'payment_fail', $page);
                    }
                }
            }

            if (empty($payment_success) || (get_post_status($payment_success) == '')) {
                $success = get_posts([
					'title' => 'Payment Success',
					'post_type' => 'page',
					'fields' => 'ids',
				]);
				if( isset($success[0]) && !empty($success) ){
					$page = $success[0];
                    $status = get_post_status($page);
                    if ($status == 'publish') {
                        Redux::set_option($opt_name, 'payment_success', $page);
                    }
                }
            }
        }
    }
}



if (!function_exists('create_page_if_null')) {

    function create_page_if_null($target, $content, $slug)
    {
        if (get_page_by_title($target) == NULL) {
            listingpro_create_pages($target, $content, $slug);
        }
    }
}



if (!function_exists('listingpro_create_pages')) {

    function listingpro_create_pages($pageName, $content, $slug)
    {
        $createPage = array(
            'post_title' => $pageName,
            'post_content' => $content,
            'post_status' => 'publish',
            'post_author' => 1,
            'post_type' => 'page',
            'post_name' => $slug
        );
        if ($slug == 'listing-author') {
            $createPage = array(
                'post_title' => $pageName,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_name' => $slug,
                'page_template' => 'template-dashboard.php'
            );
        }

        if ($slug == 'payment-success') {
            $createPage = array(
                'post_title' => $pageName,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_name' => $slug,
                'page_template' => 'templates/template-payment-thankyou.php'
            );
        }

        if ($slug == 'payment-fail') {
            $createPage = array(
                'post_title' => $pageName,
                'post_content' => $content,
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'page',
                'post_name' => $slug,
                'page_template' => 'templates/template-payment-cancel.php'
            );
        }

        // Insert the post into the database
        wp_insert_post($createPage);
    }
}

// set permalink
if (!function_exists('lp_set_permalink')) {

    function lp_set_permalink()
    {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
    }
}

add_action('after_switch_theme', 'lp_set_permalink');

if (!function_exists('lp_import_existing_post_callback')) {

    function lp_import_existing_post_callback($post_exists, $post)
    {

        $post_exists = post_exists($post['post_title'], '', '');

        if ($post_exists && $post['post_title'] == 'Dashboard' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        if ($post_exists && $post['post_title'] == 'Submit Your Listing' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        if ($post_exists && $post['post_title'] == 'Edit Your Listing' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        if ($post_exists && $post['post_title'] == 'Select Your Plan' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }
        if ($post_exists && $post['post_title'] == 'Payment Success' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        if ($post_exists && $post['post_title'] == 'Payment Fail' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        if ($post_exists && $post['post_title'] == 'Payment Checkout' && get_post_type($post_exists) == $post['post_type']) {
            wp_delete_post($post_exists, true);
            $post_exists = 0;
        }

        return $post_exists;
    }

    add_filter('wp_import_existing_post', 'lp_import_existing_post_callback', 99, 2);
}
