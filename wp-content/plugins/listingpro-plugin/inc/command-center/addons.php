<?php
lp_open_chat_box();
$installed_plugins  =   get_plugins();
$booking_version    =   '1.6';
if (isset($installed_plugins['listingpro-bookings/listingpro-bookings.php'])) {
	$booking_version    =   $installed_plugins['listingpro-bookings/listingpro-bookings.php']['Version'];
}
$lead_form_version  =   '1.0.7';
if (isset($installed_plugins['listingpro-lead-form/plugin.php'])) {
	$lead_form_version  =   $installed_plugins['listingpro-lead-form/plugin.php']['Version'];
}
$addons_upadates    =   get_option('lp_addons_updates');
$listing_addons_arr =   array(
	'listingpro-plugin' => array(
		'name' => 'listingpro core',
		'necessity' => 'required',
		'paid' => false,
		'file' => 'listingpro-plugin/plugin.php',
		'version' => '2.9.12',
		'destination' => 'own',
		'author'    =>  'CridioStudio',
		'author_url'    =>  'https://crid.io',
	),
	'listingpro-reviews' => array(
		'name' => 'listingpro reviews',
		'necessity' => 'required',
		'paid' => false,
		'file' => 'listingpro-reviews/plugin.php',
		'version' => '1.8',
		'destination' => 'own',
		'author'    =>  'CridioStudio',
		'author_url'    =>  'https://crid.io',
	),
	'listingpro-ads' => array(
		'name' => 'listingpro ads',
		'necessity' => 'required',
		'paid' => false,
		'file' => 'listingpro-ads/plugin.php',
		'version' => '1.4',
		'destination' => 'own',
		'author'    =>  'CridioStudio',
		'author_url'    =>  'https://crid.io',
	),
	'redux-framework' => array(
		'name' => 'Redux Framework',
		'necessity' => 'required',
		'paid' => false,
		'file' => 'redux-framework/redux-framework.php',
		'version' => '3.6.7.7',
		'destination' => 'own',
		'author'    =>  'Redux',
		'author_url'    =>  'https://redux.io/',
	),
	'js_composer' => array(
		'name' => 'WPBakery Page Builder',
		'necessity' => 'recommended',
		'paid' => false,
		'file' => 'js_composer/js_composer.php',
		'version' => '6.8.0',
		'destination' => 'own',
		'author'    =>  'WPBakery',
		'author_url'    =>  'https://wpbakery.com',
	),
	'nextend-facebook-connect' => array(
		'name' => 'Nextend Social Login',
		'necessity' => 'recommended',
		'paid' => false,
		'file' => 'nextend-facebook-connect/nextend-facebook-connect.php',
		'version' => '3.0.20',
		'destination' => 'own',
		'author'    =>  'Nextend',
		'author_url'    =>  'https://nextendweb.com/',
	),
	'lp-bookings' => array(
		'name' => 'Appointments',
		'necessity' => 'free',
		'paid' => false,
		'file' => 'listingpro-bookings/listingpro-bookings.php',
		'version' => $booking_version,
		'destination' => 'external',
		'author'    =>  'CridioStudio',
		'author_url'    =>  'https://crid.io',
	),
	'listingpro_lead_form' => array(
		'name' => 'Listingpro Lead Form',
		'necessity' => 'free',
		'paid' => false,
		'file' => 'listingpro-lead-form/plugin.php',
		'version' => $lead_form_version,
		'destination' => 'external',
		'author'    =>  'CridioStudio',
		'author_url'    =>  'https://crid.io',
	),
);
$content = get_option('lp_overview_api_content');

?>
<input id="active-pruchase-key" value="<?php echo get_option('active_license'); ?>" type="hidden">
<input id="cridio-api-url" value="<?php echo CRIDIO_API_URL; ?>" type="hidden">
<div class="lp-cc-dashboard-content-container">
	<div class="lp-cc-dashboard-navbar">
		<div class="lp-cc-dashboard-navbar-brand">
			<img alt="image" src="<?php echo get_site_url(); ?>/wp-content/plugins/listingpro-plugin/images/logo.png" />
		</div>
		<div class="lp-cc-dashboard-navbar-nav">
			<span>Need Customization?<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-need-customization">Request Free Quote</a></span>
			<span class="or"></span>
			<span>Need Help?<a href="https://docs.listingprowp.com/" target="_blank">Go to Help Center</a></span>
		</div>
	</div>
	<div class="lp-cc-dashboard-content-header">
		<p class="lp-cc-dashboard-content-header-title">Welcome to ListingPro Command Center!</p>
		<?php
		$verification_check =   get_option('theme_activation');
		if ($verification_check != 'activated') {
		?>
			<p class="lp-cc-dashboard-content-header-title-des">ListingPro is now installed and ready to use! Get ready to
				built your dream directory. Please <a href="<?php echo admin_url('admin.php?page=lp-cc-license'); ?>">activate your license</a> to get automatic updates, enable
				Visualizer modules, install free Add-ons and more. We hope you enjoy it
			</p>
		<?php
		}
		?>
	</div>

	<div class="lp-cc-dashboard-content-btn-group">
		<a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=listingpro" class="float-l">Dashboard</a>
		<a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-addons" class="active float-l">Add-ons</a>
		<a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-visualizer" class="float-l">Visualizer</a>
		<a href="<?php echo get_site_url() ?>/wp-admin/admin.php?page=lp-cc-license" class="float-l">License</a>
		<?php if (isset($content['listingpro_options']['services_link']) &&  !empty($content['listingpro_options']['services_link'])) { ?>
			<a href="<?php echo esc_url($content['listingpro_options']['services_link']); ?>" target="_blank" class="lp-services-link float-l">Services</a>
		<?php } ?>
	</div>
	<div class="lp-cc-dashboard-content-plugin-card-group-main clearfix">
		<div class="lp-cc-dashboard-content-plugin-card-group">
			<?php
			foreach ($listing_addons_arr as $addon_dir => $v) {
			?>
				<div class="lp-cc-dashboard-content-plugin-card">
					<div class="card-container">
						<div class="lp-cc-dashboard-content-plugin-card-head">
							<div class="head-row">
								<span class="head-title"><?php echo $v['name']; ?></span>
								<div class="head-title-info tooltip">
									i
									<span class="tooltiptext">On this page all the listings will appear post search or by going directly to a location or category.</span>
								</div>
							</div>
							<div class="clearfix"></div>
							<div class="head-row">
								<span class="card-head-version">v<?php echo $v['version']; ?></span>
								<span class="or"></span>
								<span class="card-head-publisher"><a href="<?php echo $v['author_url']; ?>" target="_blank"><?php echo $v['author']; ?></a></span>
								<?php
								if ($v['necessity'] == 'required' || $v['necessity'] == 'recommended') {
								?>
									<button class="card-head-importance require"><?php echo $v['necessity']; ?></button>
								<?php
								} else {
								?>
									<button class="card-head-importance free">free</button>
								<?php
								}
								?>
							</div>
						</div>
						<div class="clearfix"></div>
						<div class="for-click-event">
							<div class="preloader">
								<img alt="image" src="<?php echo get_template_directory_uri() . '/assets/images/ajax-load.gif'; ?>">
							</div>
							<div class="lp-cc-dashboard-content-plugin-card-content">
								<div class="card-content-container" align="center">
									<img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/lp-logo1.png">
									<p class="plugin-card-content-img-des"><?php echo $v['name']; ?></p>
									<?php
									if (!file_exists(WP_PLUGIN_DIR . '/' . $v['file'])) {
									?>
										<div class="card-img-overlay">
											<button class="card-img-overlay-action lp-cc-addons-actions" data-destination="<?php echo $v['destination']; ?>" data-file="<?php echo $v['file']; ?>" data-action="install">Install</button>
										</div>
										<?php
									} else {
										if (is_plugin_active($v['file'])) {
										?>
											<div class="card-img-overlay">
												<button class="card-img-overlay-action lp-cc-addons-actions" data-destination="<?php echo $v['destination']; ?>" data-file="<?php echo $v['file']; ?>" data-action="deactivate">Deactivate</button>
												<?php
												if (is_array($v) && isset($addons_upadates['available_updates']) && in_array($v['file'], $addons_upadates['available_updates'])) {
													$updated_file   =   'appointments-updates';
													if ($v['file'] == 'listingpro-lead-form/plugin.php') {
														$updated_file   =   'lead-form-updates';
													}
												?>
													<button class="card-img-overlay-action lp-cc-addons-actions lp-cc-addons-action-update" data-destination="<?php echo $v['destination']; ?>" data-file="<?php echo $updated_file; ?>" data-action="update">Update</button>
												<?php
												}
												?>
											</div>
										<?php
										} elseif (!is_plugin_active($v['file']) && array_key_exists($v['file'], $installed_plugins)) {
										?>
											<div class="card-img-overlay">
												<button class="card-img-overlay-action lp-cc-addons-actions" data-destination="<?php echo $v['destination']; ?>" data-file="<?php echo $v['file']; ?>" data-action="activate">Activate</button>
											</div>
										<?php
										} else {
										?>
											<div class="card-img-overlay">
												<button class="card-img-overlay-action lp-cc-addons-actions" data-destination="<?php echo $v['destination']; ?>" data-file="<?php echo $v['file']; ?>" data-action="install">Install</button>
											</div>
									<?php
										}
									}
									?>
								</div>
							</div>
							<div class="clearfix"></div>
							<?php
							if (!file_exists(WP_PLUGIN_DIR . '/' . $v['file'])) {
							?>
								<div class="lp-cc-dashboard-content-plugin-card-footer">
									<button class="footer-status inactive">Inactive</button>
								</div>
								<?php
							} else {
								if (is_plugin_active($v['file'])) {
									if (is_array($addons_upadates) && in_array($v['file'], $addons_upadates['available_updates'])) {
								?>
										<div class="lp-cc-dashboard-content-plugin-card-footer">
											<button class="footer-status active">Update Available</button>
										</div>
									<?php
									} else {
									?>
										<div class="lp-cc-dashboard-content-plugin-card-footer">
											<button class="footer-status active">Active</button>
										</div>
									<?php
									}
									?>
								<?php
								} elseif (!is_plugin_active($v['file']) && array_key_exists($v['file'], $installed_plugins)) {
								?>
									<div class="lp-cc-dashboard-content-plugin-card-footer">
										<button class="footer-status installed">Installed</button>
									</div>
								<?php
								} else {
								?>
									<div class="lp-cc-dashboard-content-plugin-card-footer">
										<button class="footer-status inactive">Inactive</button>
									</div>
							<?php
								}
							}
							?>
						</div>
					</div>
				</div>
			<?php
			}
			if (isset($content['listingpro_options']['zone6_content']) && !empty($content['listingpro_options']['zone6_content'])) { ?>
				<div class="lp-listingpro-zone zone-6">
					<?php echo wp_kses_post($content['listingpro_options']['zone6_content']); ?>
				</div>
			<?php } ?>
		</div>
		<div class="lp-cc-dashboard-content-paid-plugin-card-group">

			<div class="clearfix lp-cc-dashboard-content-paid-plugin-card-group-header">
				<p>Premium Add-ons by ListingProWP</p>
				<a href="https://listingprowp.com/plugins/" target="_blank">See All</a>
			</div>
			<?php if (isset($content['listingpro_options']['zone5_content']) && !empty($content['listingpro_options']['zone5_content'])) { ?>
				<div class="lp-cc-dashboard-content-plugin-card-content lp-listingpro-zone zone-5">
					<?php echo wp_kses_post($content['listingpro_options']['zone5_content']); ?>
				</div>
			<?php } ?>
			<div class="clearfix"></div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/MOLLIE_Thumb.jpg">

					<a class="card-imgg-overlay-action" href="https://listingprowp.com/downloads/mollie/" target="_blank">Download</a>
					<p class="lp-typology-tag lp-typology-tag-premium">paid</p>
				</div>
			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/razorpay-thumb2.jpg">

					<a class="card-imgg-overlay-action" href="https://listingprowp.com/downloads/razorpay/" target="_blank">Download</a>
					<p class="lp-typology-tag lp-typology-tag-premium">paid</p>
				</div>
			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/payfast.jpg">

					<a class="card-imgg-overlay-action" href="https://listingprowp.com/downloads/payfast/" target="_blank">Download</a>
					<p class="lp-typology-tag lp-typology-tag-premium">paid</p>
				</div>
			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/MEDPRO_Thumb.jpg">

					<a class="card-imgg-overlay-action" href="https://listingprowp.com/downloads/medicalpro/" target="_blank">Download</a>
					<p class="lp-typology-tag lp-typology-tag-premium">paid</p>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="suggested-partner">
				<p>Suggested Add-ons from Partners </p>

			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/hub.png">
					<a class="card-imgg-overlay-action" href="<?php echo esc_url(home_url('/')); ?>wp-admin/plugin-install.php?s=hubspot&tab=search&type=term" target="_blank">Download</a>
				</div>
			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/form.png">

					<a class="card-imgg-overlay-action" href="<?php echo esc_url(home_url('/')); ?>wp-admin/plugin-install.php?s=wp-forms&tab=search&type=term" target="_blank">Download</a>

				</div>
			</div>
			<div class="lp-cc-dashboard-content-plugin-card-content">
				<div class="card-content-container" align="center">
					<img alt="image" src="<?php echo LISTINGPRO_PLUGIN_URI; ?>/images/paidplugin/repair-kit.png">
					<?php
					if (class_exists('Seo_Repair_Kit')) { ?>
						<a class="card-imgg-overlay-action" href="https://seorepairkit.com/" target="_blank">Visit</a>
					<?php } else { ?>
						<a class="card-imgg-overlay-action" href="<?php echo esc_url(home_url('/')); ?>wp-admin/plugin-install.php?s=seo repair kit&tab=search&type=term" target="_blank">Download</a>
					<?php } ?>

				</div>
			</div>
		</div>
	</div>

</div>