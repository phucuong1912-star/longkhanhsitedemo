<?php
global $listingpro_options;
$plan_id = listing_get_metabox_by_ID('Plan_id', $post->ID);
if (!empty($plan_id)) {
	$plan_id = $plan_id;
} else {
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

$facebook = listing_get_metabox('facebook');
$twitter = listing_get_metabox('twitter');
$linkedin = listing_get_metabox('linkedin');

$youtube = listing_get_metabox('youtube');
$instagram = listing_get_metabox('instagram');
$phone = listing_get_metabox('phone');
$website = listing_get_metabox('website');
$gAddress = listing_get_metabox('gAddress');
$latitude = listing_get_metabox('latitude');
$longitude = listing_get_metabox('longitude');
$whatsapp = listing_get_metabox('whatsapp');
$email_switcher = lp_theme_option('listingpro_email_display_switch');
$email = listing_get_metabox('email');
?>

<?php

if ((!empty($email) && $email_switcher == 'yes') || !empty($latitude) || !empty($longitude) || !empty($gAddress) || !empty($phone) || !empty($website) ||  !empty($facebook) || !empty($twitter) || !empty($linkedin) || !empty($youtube) || !empty($instagram)) {
?>
	<div class="sidebar-post">
		<div class="widget-box map-area">
			<?php
			if (!empty($latitude) && !empty($longitude)) {
				if ($map_show == "true") {
			?>
					<div class="widget-bg-color post-author-box lp-border-radius-5">
						<div class="widget-header margin-bottom-25 hideonmobile">
							<ul class="post-stat">
								<li>
									<a class="md-trigger parimary-link singlebigmaptrigger" data-lat="<?php echo esc_attr($latitude); ?>" data-lan="<?php echo esc_attr($longitude); ?>" data-modal="modal-4">
										<!-- <span class="phone-icon">
													Marker icon by Icons8
													<?php echo listingpro_icons('mapMarker'); ?>
												</span>
												<span class="phone-number ">
													<?php echo esc_html__('View Large Map', 'listingpro'); ?>
												</span> -->
									</a>
								</li>
							</ul>
						</div>
						<?php
						$lp_map_pin = $listingpro_options['lp_map_pin']['url'];
						?>
						<div class="widget-content ">
							<div class="widget-map pos-relative">
								<div id="singlepostmap" class="singlemap" data-pinicon="<?php echo esc_attr($lp_map_pin); ?>"></div>
								<div class="get-directions">
									<a href="https://www.google.com/maps/search/?api=1&query=<?php echo esc_attr($latitude); ?>,<?php echo esc_attr($longitude); ?>" target="_blank">
										<span class="phone-icon">
											<i class="fa fa-map-o"></i>
										</span>
										<span class="phone-number ">
											<?php echo esc_html__('Get Directions', 'listingpro'); ?>
										</span>
									</a>
								</div>
							</div>
						</div>
					</div><!-- ../widget-box  -->
				<?php } ?>
			<?php } ?>
			<div class="listing-detail-infos margin-top-20 clearfix">
				<ul class="list-style-none list-st-img clearfix">
					<?php

					$phone = listing_get_metabox('phone');
					$website = listing_get_metabox('website');
					//if(empty($facebook) && empty($twitter) && empty($linkedin)){}else{
					?>
					<?php if (!empty($gAddress)) {
						if ($location_show == "true") { ?>
							<li>
								<a>
									<span class="cat-icon">
										<i class="fa-solid fa-location-dot"></i>
										<!-- <i class="fa fa-map-marker"></i> -->
									</span>
									<span>
										<?php echo esc_attr($gAddress); ?>
									</span>
								</a>
							</li>
						<?php } ?>
					<?php } ?>
					<?php
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
					<?php if ($contact_show == "true") { ?>
						<?php if (!empty($phone)) { ?>
							<li class="lp-listing-phone">
								<a data-lpID="<?php echo esc_attr($post->ID); ?>"
									href="tel:<?php echo esc_attr($phone); ?>">
									<span class="cat-icon">
										<i class="fa-solid fa-phone"></i>
										<!-- <i class="fa fa-mobile"></i> -->
									</span>
									<span>
										<?php echo esc_html($phone); ?>
									</span>
								</a>
							</li>
							<?php
						}
						if (!empty($whatsapp)) {
							$whatsappStatus = $listingpro_options['lp_detail_page_whatsapp_button'];
							$whatsappMsg = esc_html__('Hi, Contacting for you listing', 'listingpro');
							if ($whatsappStatus == "on" && !empty($whatsapp)) {

								$whatsappobj = "https://api.whatsapp.com/send?";
								$whatsappobj .= "phone=$whatsapp";
								$whatsappobj .= "&";
								$whatsappobj .= "text=$whatsappMsg";
							?>
								<li class="lp-listing-phone-whatsapp">
									<a href="<?php echo esc_attr($whatsappobj); ?>" target="_blank">
										<span class="cat-icon">
											<i class="fa fa-whatsapp" aria-hidden="true"></i>
										</span>
										<span>
											<?php echo esc_html__('Call on Whatsapp', 'listingpro'); ?>
										</span>
									</a>
								</li>
							<?php
							}
							?>

						<?php } ?>
					<?php } ?>
					<?php if (!empty($website)) {
						if ($website_show == "true") { ?>
							<li class="lp-user-web">
								<a data-lpID="<?php echo esc_attr($post->ID); ?>" href="<?php echo esc_url($website); ?>" target="_blank" rel="nofollow">
									<span class="cat-icon">
										<i class="fa-solid fa-globe"></i>
										<!-- <i class="fa fa-globe"></i> -->
									</span>
									<span><?php echo esc_url($website); ?></span>
								</a>
							</li>
						<?php } ?>
					<?php } ?>
					<?php //} 
					?>
				</ul>
				<?php
				$facebook = listing_get_metabox('facebook');
				$twitter = listing_get_metabox('twitter');
				$linkedin = listing_get_metabox('linkedin');
				$youtube = listing_get_metabox('youtube');
				$instagram = listing_get_metabox('instagram');
				if ($social_show == "true") {
					if (empty($facebook) && empty($twitter) && empty($linkedin) && empty($youtube) && empty($instagram)) {
					} else {
				?>
						<div class="widget-box widget-social">
							<div class="widget-content clearfix">
								<ul class="list-style-none list-st-img">
									<?php if (!empty($facebook)) { ?>
										<li class="lp-fb">
											<a href="<?php echo esc_url($facebook); ?>" class="padding-left-0" target="_blank">
												<i class="fa-brands fa-square-facebook"></i>
											</a>
										</li>
									<?php } ?>
									<?php if (!empty($twitter)) { ?>
										<li class="lp-tw">
											<a href="<?php echo esc_url($twitter); ?>" class="padding-left-0" target="_blank">
												<i class="fa-brands fa-square-x-twitter"></i>
											</a>
										</li>
									<?php } ?>
									<?php if (!empty($linkedin)) { ?>
										<li class="lp-li">
											<a href="<?php echo esc_url($linkedin); ?>" class="padding-left-0" target="_blank">
												<i class="fa-brands fa-linkedin"></i>
											</a>
										</li>
									<?php } ?>
									<?php if (!empty($youtube)) { ?>
										<li class="lp-li">
											<a href="<?php echo esc_url($youtube); ?>" class="padding-left-0" target="_blank">
												<i class="fa-brands fa-youtube"></i>
											</a>
										</li>
									<?php } ?>
									<?php if (!empty($instagram)) { ?>
										<li class="lp-li">
											<a href="<?php echo esc_url($instagram); ?>" class="padding-left-0" target="_blank">
												<i class="fa-brands fa-square-instagram"></i>
											</a>
										</li>
									<?php } ?>
								</ul>
							</div>
						</div><!-- ../widget-box  -->
					<?php } ?>
				<?php } ?>
			</div>
		</div>
	</div>
<?php } ?>