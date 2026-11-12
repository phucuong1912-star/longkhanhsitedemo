<?php
/* listingpro aditional functions */

if (! function_exists('lp_generate_invoice_mail')) {
	function lp_generate_invoice_mail($post)
	{
		if ($post->post_type == "listing") {
			$ID = $post->ID;
			global $listingpro_options;
			$author    = $post->post_author;
			$name      = get_the_author_meta('display_name', $author);
			$useremail = get_the_author_meta('user_email', $author);
			$user_name = $name;

			$website_url    = site_url();
			$website_name   = get_option('blogname');
			$listing_title  = $post->post_title;
			$listing_url    = get_permalink($ID);
			$plan_id        = listing_get_metabox_by_ID('Plan_id', $ID);
			$plan_title     = 'N/A';
			$plan_price     = 'N/A';
			$invoice_no     = 'N/A';
			$payment_method = 'N/A';
			if (! empty($plan_id)) {
				$oplan_price = get_post_meta($plan_id, 'plan_price', true);
				if (! empty($oplan_price)) {
					$plan_title = get_the_title($plan_id);

					global $wpdb;
					$dbprefix = $wpdb->prefix;
					$table    = "listing_orders";
					$table    = $dbprefix . $table;

					$results = array();
					if ($wpdb->get_var("SHOW TABLES LIKE '$table'") == $table) {
						$query   = "";
						$query   = "SELECT * from $table WHERE post_id='$ID' ORDER BY main_id DESC";
						$results = $wpdb->get_results($query);
						$results = array_reverse($results);
					}

					foreach ($results as $Index => $Value) {
						$invoice_no     = $Value->order_id;
						$plan_price     = $Value->price;
						$payment_method = $Value->payment_method;
					}
				}
			}
			$headers[] = 'Content-Type: text/html; charset=UTF-8';

			$u_mail_subject_a = '';
			$u_mail_body_a    = '';
			$u_mail_subject   = $listingpro_options['listingpro_subject_listing_approved'];
			$u_mail_body      = $listingpro_options['listingpro_listing_approved'];

			$u_mail_subject_a = lp_sprintf2("$u_mail_subject", array(
				'website_url'    => "$website_url",
				'listing_title'  => "$listing_title",
				'listing_url'    => "$listing_url",
				'website_name'   => "$website_name",
				'user_name'      => "$user_name",
				'plan_title'     => "$plan_title",
				'plan_price'     => "$plan_price",
				'invoice_no'     => "$invoice_no",
				'payment_method' => "$payment_method",
			));

			$u_mail_body_a = lp_sprintf2("$u_mail_body", array(
				'website_url'    => "$website_url",
				'listing_title'  => "$listing_title",
				'listing_url'    => "$listing_url",
				'website_name'   => "$website_name",
				'user_name'      => "$user_name",
				'plan_title'     => "$plan_title",
				'plan_price'     => "$plan_price",
				'invoice_no'     => "$invoice_no",
				'payment_method' => "$payment_method",
			));
			lp_mail_headers_append();
			wp_mail($useremail, $u_mail_subject_a, $u_mail_body_a, $headers);
			lp_mail_headers_remove();
		}
	}
}
add_action('pending_to_publish', 'lp_generate_invoice_mail', 10, 1);
//add_action('new_to_publish', 'lp_generate_invoice_mail', 10, 1);
add_action('draft_to_publish', 'lp_generate_invoice_mail', 10, 1);


/* ================================= force trash delete ============================ */


add_action('publish_to_trash', 'delete_formFIelds_permanently');
if (! function_exists('delete_formFIelds_permanently')) {
	function delete_formFIelds_permanently($post)
	{
		if ($post->post_type != 'form-fields') {
			return false;
		}
		if (!current_user_can('delete_post', $post->ID)) {
			return false;
		}

		return wp_delete_post($post->ID, true);
	}
}

add_action('publish_to_trash', 'delete_ads_permanently');
if (! function_exists('delete_ads_permanently')) {
	function delete_ads_permanently($post)
	{
		if ($post->post_type != 'lp-ads') {
			return false;
		}
		if (!current_user_can('delete_post', $post->ID)) {
			return false;
		}

		return wp_delete_post($post->ID, true);
	}
}
add_action('publish_to_trash', 'delete_reviews_permanently');
if (! function_exists('delete_reviews_permanently')) {
	function delete_reviews_permanently($post)
	{
		if ($post->post_type != 'lp-reviews') {
			return false;
		}
		if (!current_user_can('delete_post', $post->ID)) {
			return false;
		}

		return wp_delete_post($post->ID, true);
	}
}


if (! function_exists('LP_current_location')) {
	function LP_current_location()
	{
		if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		return $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}
}
if (! function_exists('LP_get_if_localhost')) {
	function LP_get_if_localhost()
	{
		if ($_SERVER['SERVER_ADDR'] == '127.0.0.1') {
			return true;
		}

		return false;
	}
}
