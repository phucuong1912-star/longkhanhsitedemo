<?php
$plan_id = listing_get_metabox_by_ID('Plan_id', $post->ID);
if (empty($plan_id)) {
	$plan_id = 'none';
}
$map_show = get_post_meta($plan_id, 'map_show', true);
$social_show = get_post_meta($plan_id, 'listingproc_social', true);
$location_show = get_post_meta($plan_id, 'listingproc_location', true);
$contact_show = get_post_meta($plan_id, 'contact_show', true);
$website_show = get_post_meta($plan_id, 'listingproc_website', true);

if ($plan_id == "none") {
	$map_show = 'true';
	$social_show = 'true';
	$location_show = 'true';
	$contact_show = 'true';
	$website_show = 'true';
}
$whatsapp = listing_get_metabox('whatsapp');
$email_switcher = lp_theme_option('listingpro_email_display_switch');
$email = listing_get_metabox('email');
if (($email == '' && $email_switcher == '') && ($latitude == '') && ($longitude == '') && ($gAddress == '') && ($phone == '') && ($website == '') && ($whatsapp == '') && ($facebook == '') && ($twitter == '') && ($youtube == '') && ($instagram == '') && ($linkedin == '')) {
	return;
}


if (!empty($latitude) && !empty($longitude) && $map_show == 'true'):
	$lp_map_pin = $listingpro_options['lp_map_pin']['url'];

?>
	<span class="singlebigmaptrigger" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>"></span>
	<div id="singlepostmap" class="singlemap lp-widget-inner-wrap" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>
<?php endif; ?>
<ul class="widget-social-icons">
	<?php
	if ($gAddress && $location_show == 'true'):
	?>
		<li>
			<p>
				<span class="social-icon">
					<i class="fa-solid fa-location-dot"></i>
				</span>
				<?php echo esc_attr($gAddress); ?>
			</p>
			<a class="addr-margin" href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>" target="_blank"><?php echo esc_html__('Get Directions', 'listingpro'); ?></a>
		</li>
	<?php
	endif;
	$email_switcher = lp_theme_option('listingpro_email_display_switch');
	$email = listing_get_metabox('email');
	if ($email_switcher == 'yes') { ?>
		<li class="lp-listing-email">
			<a data-lpID="<?php echo esc_attr($post->ID); ?>" href="mailto:<?php echo esc_attr($email); ?>">
				<span class="cat-icon">
					<i class="fa fa-envelope"></i>
				</span>
				<span>
					<?php echo esc_html($email); ?>
				</span>
			</a>
		</li>
	<?php } ?>
	<?php

	if ($phone && $contact_show == 'true'):
	?>
		<li class="lp-listing-phone">
			<span class="social-icon">
				<i class="fa-solid fa-phone"></i>
			</span>
			<a data-lpid="<?php echo esc_attr($post->ID); ?>" class="phone-link" href="tel:<?php echo esc_attr($phone); ?>"><?php echo esc_attr($phone); ?></a>
		</li>
	<?php
	endif;
	$whatsappStatus = $listingpro_options['lp_detail_page_whatsapp_button'];
	$whatsappMsg = esc_html__('Hi, Contacting for you listing', 'listingpro');
	if ($whatsapp && $contact_show == 'true' && $whatsappStatus == "on") {

		$whatsappobj = "https://api.whatsapp.com/send?";
		$whatsappobj .= "phone=$whatsapp";
		$whatsappobj .= "&";
		$whatsappobj .= "text=$whatsappMsg";
	?>
		<li class="lp-listing-phone-whatsapp">
			<a href="<?php echo esc_attr($whatsappobj); ?>" target="_blank">
				<span class="social-icon">
					<img alt="image" src="data:image/png;base64,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">
				</span>
				<span>
					<?php echo esc_html__('Whatsapp', 'listingpro'); ?>
				</span>
			</a>
		</li>
	<?php
	}
	if ($website && $website_show == 'true'):
	?>
		<li class="lp-user-web">
			<span class="social-icon">
				<i class="fa-solid fa-globe"></i>
			</span>
			<a data-lpid="<?php echo esc_attr($post->ID); ?>" href="<?php echo esc_url($website); ?>" target="_blank"><?php echo esc_url($website); ?></a>
		</li>
	<?php
	endif;
	if ($social_show == 'true' && (!empty($facebook) || !empty($twitter) || !empty($youtube) || !empty($instagram) || !empty($linkedin))):
	?>
		<li class="lp-widget-social-links">
			<?php if (!empty($facebook)): ?><a href="<?php echo esc_attr($facebook); ?>" target="_blank"><i class="fa-brands fa-facebook"></i></a><?php endif;; ?>
			<?php if (!empty($twitter)): ?> <a href="<?php echo esc_attr($twitter); ?>" target="_blank"><i class="fa-brands fa-square-x-twitter" aria-hidden="true"></i></a><?php endif; ?>
			<?php if (!empty($youtube)): ?><a href="<?php echo esc_attr($youtube); ?>" target="_blank"><i class="fa fa-youtube-square" aria-hidden="true"></i></a><?php endif; ?>
			<?php if (!empty($instagram)): ?><a href="<?php echo esc_attr($instagram); ?>" target="_blank"><i class="fa-brands fa-instagram-square" aria-hidden="true"></i></a><?php endif; ?>
			<?php if (!empty($linkedin)) { ?>

				<a href="<?php echo esc_attr($linkedin); ?>" target="_blank"><i class="fa-brands fa-linkedin" aria-hidden="true"></i></a>
				</a>

			<?php } ?>
			<div class="clearfix"></div>
		</li>
	<?php endif; ?>
</ul>