<?php
require_once dirname(__FILE__) . '/class-tgm-plugin-activation.php';

add_action('tgmpa_register', 'listingpro_required_plugins');
if (!function_exists('listingpro_required_plugins')) {
	function listingpro_required_plugins()
	{

		/**
		 * Array of plugin arrays. Required keys are name and slug.
		 * If the source is NOT from the .org repo, then source is also required.
		 */
		$plugins = array(

			array(
				'name' => 'Redux Framework',
				'slug' => 'redux-framework',
				'source' => get_template_directory() . '/include/plugins/redux-framework.zip',
				'required' => true,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			),
			array(
				'name' => 'Listingpro Plugin',
				'slug' => 'listingpro-plugin',
				'source' => get_template_directory() . '/include/plugins/listingpro-plugin.zip',
				'required' => true,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			),

			array(
				'name' => 'Listingpro Reviews',
				'slug' => 'listingpro-reviews',
				'source' => get_template_directory() . '/include/plugins/listingpro-reviews.zip',
				'required' => true,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			),
			array(
				'name' => 'Listingpro ADs',
				'slug' => 'listingpro-ads',
				'source' => get_template_directory() . '/include/plugins/listingpro-ads.zip',
				'required' => true,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			),
			array(
				'name' => 'CubeWP Forms',
				'slug' => 'cubewp-forms',
				'required' => true,
				'force_activation' => false,
				'force_deactivation' => false
			),
			array(
				'name' => 'CubeWP Framework',
				'slug' => 'cubewp-framework',
				'source' => get_template_directory() . '/include/plugins/cubewp-framework.zip',
				'required' => true,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			),
			// Facebook Connect
			array(
				'name' => 'Nextend Social Login and Register',
				'slug' => 'nextend-facebook-connect',
				'required' => false,
				'force_activation' => false,
				'force_deactivation' => false
			),
			array(
				'name' => 'SEO Repair Kit',
				'slug' => 'seo-repair-kit',
				'required' => true,
				'force_activation' => false,
				'force_deactivation' => false
			),

			// Twitter Connect

		);
		$lp_editor_platform =   'WP Bakery';
		if (isset($_COOKIE['lp_editor_platform'])) {
			$lp_editor_platform =   $_COOKIE['lp_editor_platform'];
		}
		if ($lp_editor_platform == 'Elementor') {
			$plugins[]  =   array(
				'name' => 'Elementor',
				'slug' => 'elementor',
				'required' => false,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false

			);
		} else {
			$plugins[]  =   array(
				'name' => 'JS Composer',
				'slug' => 'js_composer',
				'source' => get_template_directory() . '/include/plugins/js_composer.zip',
				'required' => false,
				'version' => '',
				'force_activation' => false,
				'force_deactivation' => false
			);
		}



		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'id'           => 'listingpro',                 // Unique ID for hashing notices for multiple instances of TGMPA.
			'default_path' => '',                      // Default absolute path to bundled plugins.
			'menu'         => 'tgmpa-install-plugins', // Menu slug.
			'has_notices'  => true,                    // Show admin notices or not.
			'dismissable'  => true,                    // If false, a user cannot dismiss the nag message.
			'dismiss_msg'  => '',                      // If 'dismissable' is false, this message will be output at top of nag.
			'is_automatic' => false,                   // Automatically activate plugins after installation or not.
			'message'      => '',                      // Message to output right before the plugins table.

			/*
		'strings'      => array(
			'page_title'                      => __( 'Install Required Plugins', 'listingpro' ),
			'menu_title'                      => __( 'Install Plugins', 'listingpro' ),
			/* translators: %s: plugin name. * /
			'installing'                      => __( 'Installing Plugin: %s', 'listingpro' ),
			/* translators: %s: plugin name. * /
			'updating'                        => __( 'Updating Plugin: %s', 'listingpro' ),
			'oops'                            => __( 'Something went wrong with the plugin API.', 'listingpro' ),
			'notice_can_install_required'     => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme requires the following plugin: %1$s.',
				'This theme requires the following plugins: %1$s.',
				'listingpro'
			),
			'notice_can_install_recommended'  => _n_noop(
				/* translators: 1: plugin name(s). * /
				'This theme recommends the following plugin: %1$s.',
				'This theme recommends the following plugins: %1$s.',
				'listingpro'
			),
			'notice_ask_to_update'            => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following plugin needs to be updated to its latest version to ensure maximum compatibility with this theme: %1$s.',
				'The following plugins need to be updated to their latest version to ensure maximum compatibility with this theme: %1$s.',
				'listingpro'
			),
			'notice_ask_to_update_maybe'      => _n_noop(
				/* translators: 1: plugin name(s). * /
				'There is an update available for: %1$s.',
				'There are updates available for the following plugins: %1$s.',
				'listingpro'
			),
			'notice_can_activate_required'    => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following required plugin is currently inactive: %1$s.',
				'The following required plugins are currently inactive: %1$s.',
				'listingpro'
			),
			'notice_can_activate_recommended' => _n_noop(
				/* translators: 1: plugin name(s). * /
				'The following recommended plugin is currently inactive: %1$s.',
				'The following recommended plugins are currently inactive: %1$s.',
				'listingpro'
			),
			'install_link'                    => _n_noop(
				'Begin installing plugin',
				'Begin installing plugins',
				'listingpro'
			),
			'update_link' 					  => _n_noop(
				'Begin updating plugin',
				'Begin updating plugins',
				'listingpro'
			),
			'activate_link'                   => _n_noop(
				'Begin activating plugin',
				'Begin activating plugins',
				'listingpro'
			),
			'return'                          => __( 'Return to Required Plugins Installer', 'listingpro' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'listingpro' ),
			'activated_successfully'          => __( 'The following plugin was activated successfully:', 'listingpro' ),
			/* translators: 1: plugin name. * /
			'plugin_already_active'           => __( 'No action taken. Plugin %1$s was already active.', 'listingpro' ),
			/* translators: 1: plugin name. * /
			'plugin_needs_higher_version'     => __( 'Plugin not activated. A higher version of %s is needed for this theme. Please update the plugin.', 'listingpro' ),
			/* translators: 1: dashboard link. * /
			'complete'                        => __( 'All plugins installed and activated successfully. %1$s', 'listingpro' ),
			'dismiss'                         => __( 'Dismiss this notice', 'listingpro' ),
			'notice_cannot_install_activate'  => __( 'There are one or more required or recommended plugins to install, update or activate.', 'listingpro' ),
			'contact_admin'                   => __( 'Please contact the administrator of this site for help.', 'listingpro' ),

			'nag_type'                        => '', // Determines admin notice type - can only be one of the typical WP notice classes, such as 'updated', 'update-nag', 'notice-warning', 'notice-info' or 'error'. Some of which may not work as expected in older WP versions.
		),
		*/
		);

		tgmpa($plugins, $config);
	}
}
