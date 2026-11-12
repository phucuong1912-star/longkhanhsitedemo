<?php

/**
 * ReduxFramework Sample Config File
 * For full documentation, please visit: http://docs.reduxframework.com/
 */
if (!class_exists('Redux')) {
    return;
}
$allowed_html_array = array(
    'i' => array(
        'class' => array()
    ),
    'span' => array(

        'class' => array()
    ),
    'a' => array(
        'href' => array(),
        'title' => array(),
        'target' => array()
    )
);
// This is your option name where all the Redux data is stored.
$opt_name = "listingpro_options";
// This line is only for altering the demo. Can be easily removed.
$opt_name = apply_filters('redux_demo/opt_name', $opt_name);
/*
     *
     * --> Used within different fields. Simply examples. Search for ACTUAL DECLARATION for field examples
     *
     */
$sampleHTML = '';
// Background Patterns Reader
$sample_patterns_path = ReduxFramework::$_dir . '../sample/patterns/';
$sample_patterns_url = ReduxFramework::$_url . '../sample/patterns/';
$sample_patterns = array();
/**
 * ---> SET ARGUMENTS
 * All the possible arguments for Redux.
 * For full documentation on arguments, please refer to: https://github.com/ReduxFramework/ReduxFramework/wiki/Arguments
 * */
$theme = wp_get_theme(); // For use with some settings. Not necessary.
$args = array(
    // TYPICAL -> Change these values as you need/desire
    'opt_name' => $opt_name,
    // This is where your data is stored in the database and also becomes your global variable name.
    'display_name' => $theme->get('Name'),
    // Name that appears at the top of your panel
    'display_version' => $theme->get('Version'),
    // Version that appears at the top of your panel
    'menu_type' => 'menu',
    //Specify if the admin menu should appear or not. Options: menu or submenu (Under appearance only)
    'allow_sub_menu' => true,
    // Show the sections below the admin menu item or not
    'menu_title' => __('Theme Options', 'listingpro'),
    'page_title' => __('Theme Options', 'listingpro'),
    // You will need to generate a Google API key to use this feature.
    // Please visit: https://developers.google.com/fonts/docs/developer_api#Auth
    'google_api_key' => '',
    // Set it you want google fonts to update weekly. A google_api_key value is required.
    'google_update_weekly' => false,
    // Must be defined to add google fonts to the typography module
    'async_typography' => true,
    // Use a asynchronous font on the front end or font string
    //'disable_google_fonts_link' => true,                    // Disable this in case you want to create your own google fonts loader
    'admin_bar' => false,
    // Show the panel pages on the admin bar
    'admin_bar_icon' => 'dashicons-portfolio',
    // Choose an icon for the admin bar menu
    'admin_bar_priority' => 50,
    // Choose an priority for the admin bar menu
    'global_variable' => '',
    // Set a different name for your global variable other than the opt_name
    'dev_mode' => false,
    // Show the time the page took to load, etc
    'update_notice' => false,
    // If dev_mode is enabled, will notify developer of updated versions available in the GitHub Repo
    'customizer' => false,
    // Enable basic customizer support
    //'open_expanded'     => true,                    // Allow you to start the panel in an expanded way initially.
    //'disable_save_warn' => true,                    // Disable the save warning when a user changes a field
    // OPTIONAL -> Give you extra features
    'page_priority' => 32,
    // Order where the menu appears in the admin area. If there is any conflict, something will not show. Warning.
    'page_parent' => 'themes.php',
    // For a full list of options, visit: http://codex.wordpress.org/Function_Reference/add_submenu_page#Parameters
    'page_permissions' => 'manage_options',
    // Permissions needed to access the options panel.
    'menu_icon' => '',
    // Specify a custom URL to an icon
    'last_tab' => '',
    // Force your panel to always open to a specific tab (by id)
    'page_icon' => 'icon-themes',
    // Icon displayed in the admin panel next to your menu_title
    'page_slug' => '',
    // Page slug used to denote the panel, will be based off page title then menu title then opt_name if not provided
    'save_defaults' => true,
    // On load save the defaults to DB before user clicks save or not
    'default_show' => true,
    // If true, shows the default value next to each field that is not the default value.
    'default_mark' => '',
    // What to print by the field's title if the value shown is default. Suggested: *
    'show_import_export' => true,
    // Shows the Import/Export panel when not used as a field.
    // CAREFUL -> These options are for advanced use only
    'transient_time' => 60 * MINUTE_IN_SECONDS,
    'output' => true,
    // Global shut-off for dynamic CSS output by the framework. Will also disable google fonts output
    'output_tag' => true,
    // Allows dynamic CSS to be generated for customizer and google fonts, but stops the dynamic CSS from going to the head
    // 'footer_credit'     => '',                   // Disable the footer credit of Redux. Please leave if you can help it.
    // FUTURE -> Not in use yet, but reserved or partially implemented. Use at your own risk.
    'database' => '',
    // possible: options, theme_mods, theme_mods_expanded, transient. Not fully functional, warning!
    'use_cdn' => true,
    // If you prefer not to use the CDN for Select2, Ace Editor, and others, you may download the Redux Vendor Support plugin yourself and run locally or embed it in your code.
);
Redux::setArgs($opt_name, $args);
/*
     * ---> END ARGUMENTS
     */
/*
     * ---> START HELP TABS
     */
/*
     *
     * ---> START SECTIONS
     *
     */
/*
        As of Redux 3.5+, there is an extensive API. This API can be used in a mix/match mode allowing for
     */
// -> START General Fields
Redux::setSection($opt_name, array(
    'title' => __('General', 'listingpro'),
    'id' => 'general-settings',
    'customizer_width' => '400px',
    'icon' => 'el el-cogs',
    'fields' => array(
        array(
            'id' => 'theme_color',
            'type' => 'color',
            'title' => __('Primary Color', 'listingpro'),
            'subtitle' => __('(default: #41a6df).', 'listingpro'),
            'desc' => __('Controls the main highlight color throughout the theme. Color applied to elements such as buttons, links, categories, etc.', 'listingpro'),
            'default' => '#41a6df',
            'validate' => 'color',
        ),
        array(
            'id' => 'sec_theme_color',
            'type' => 'color',
            'title' => __('Secondary Color', 'listingpro'),
            'subtitle' => __('(default: #363F48).', 'listingpro'),
            'desc' => __('Controls the minor highlight color throughout the theme. Color applied to elements such as links mouse-over, buttons, breadcrumbs, etc.', 'listingpro'),
            'default' => '#363F48',
            'validate' => 'color',
        ),
        array(
            'id' => 'lp_register_password',
            'type' => 'switch',
            'title' => __('User Signup Form Password Field', 'listingpro'),
            'subtitle' => __('On for enable and Off for disable password field', 'listingpro'),
            'desc' => __('Enable to show password field within the signup form. Disable to have the password emailed to the user after signup. It`s recommended to leave enabled.', 'listingpro'),
            'default' => 0,
        ),
        array(
            'id' => 'lp_register_username',
            'type' => 'switch',
            'title' => __('Instant-Signup Username Field', 'listingpro'),
            'desc' => __('Enable to allow users to set their own username within the instant signup form. If disabled a username is auto-generated during signup.Instant signup shows during listing and review submission if the user isn`t signed in', 'listingpro'),
            'subtitle' => __('On for enable and Off for disable username field', 'listingpro'),
            'default' => false,
        ),
        array(
            'id' => 'lp_showhide_pagetitle',
            'type' => 'switch',
            'title' => __('Page Title (H1)', 'listingpro'),
            'subtitle' => __('On for enable and Off for disable page title', 'listingpro'),
            'desc' => __('Enable to display a page title for non-directory pages such as about, contact etc. This will not affect directory pages such as listing search results archive and listing details page.', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'listing_pricerange_symbol',
            'type' => 'text',
            'title' => __('Currency Symbol For Price Group/Range', 'listingpro'),
            'desc' => __('Enter a currency symbol (ex. $ | ¥ | £) to display in the search filter as price-group (ex. $ | $$ | $$$ | $$$$ ) and also on listing details page as price-range ($25 - $50).', 'listingpro'),
            'default' => '$',
        ),
        array(
            'id' => 'css_editor',
            'type' => 'ace_editor',
            'title' => __('Custom CSS', 'listingpro'),
            'subtitle' => __('Paste your CSS code here.', 'listingpro'),
            'mode' => 'css',
            'theme' => 'monokai',
            'desc' => 'Enter your Custom CSS code in the field. Do not include any tags or HTML in the field. Custom CSS entered here will override the theme CSS. In some cases, the !important tag may be needed. All CSS here will be kept during a theme update. We highly recommend adding your Custom CSS to a Child Theme.',
            'default' => "#header{\nmargin: 0 auto;\n}"
        ),
        array(
            'id' => 'script_editor',
            'type' => 'ace_editor',
            'title' => __('Custom JS', 'listingpro'),
            'subtitle' => __('Paste your JS code here.', 'listingpro'),
            'mode' => 'css',
            'theme' => 'monokai',
            'desc' => 'Enter your Custom JS Code in the field. Custom JS entered here will be kept during a theme update. We highly recommend entering Custom JS in a Child Theme.',
            'default' => "jQuery(document).ready(function(){\n\n});"
        ),
        array(
            'id' => 'lp_auto_current_locations_switch',
            'type' => 'switch',
            'title' => __('Auto-Location detection for Banner (Hero) and Search Field (Where)', 'listingpro'),
            'subtitle' => __('On for enable and Off for disable current location', 'listingpro'),
            'desc' => __('Enable to auto-detect users location and display their location within the location (where) field and within the Home Page Banner. <br>Example: Browse Anything! Explore <b>New York</b>', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'lp_current_ip_type',
            'type' => 'select',
            'required' => array('lp_auto_current_locations_switch', 'equals', '1'),
            'title' => __('Auto-Location Detection Method', 'listingpro'),
            'subtitle' => '',
            'desc' => __("There are two options available to identify visitors current location. <br /><br /><strong>GPS </strong>  based location auto-detection provides extremely accurate location information based on satellites.  (User consent required in the browser. A pop is displayed to the visitor to enable location detection.). <br /><br /><strong>IP API</strong> based location auto-detection provides less accurate location information based on your IP address and uses an external database to map the IP address to a physical location. (User consent not required in the browser. No pop is displayed to the user to enable location detection.).", 'listingpro'),
            'options' => array(
                'ip_api' => 'IP API',
                'gpsloc' => 'GPS',
            ),
            'default' => 'ip_api',
        ),
        array(
            'id' => 'lp_theme_loader_switch',
            'type' => 'switch',
            'title' => __('Enable Loader into your Website', 'listingpro'),
            'subtitle' => __('On for enable and Off for disable loader', 'listingpro'),
            'desc' => __('Enable to set loader for your website.', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'lp_theme_loader',
            'type' => 'media',
            'required' => array('lp_theme_loader_switch', 'equals', '1'),
            'url' => true,
            'title' => __('Theme Loader', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your loader for website untill content loaded', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/loader2.gif'),
        ),
    )
));
// -> START User Dashboard Fields
Redux::setSection($opt_name, array(
    'title' => __('User Dashboard', 'listingpro'),
    'id' => 'dashboard-settings',
    'customizer_width' => '400px',
    'icon' => 'el el-user',
    'fields' => array(
        array(
            'id' => 'dashboard_usr',
            'type' => 'switch',
            'title' => __('Dashboard', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'resurva_bookings_enable',
            'type' => 'switch',
            'title' => __('Resurva Bookings', 'listingpro'),
            'desc' => __('Enable to allow Resurva Bookings. (Available only for business listing owners) ', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'timekit_bookings_enable',
            'type' => 'switch',
            'title' => __('Timekit Bookings', 'listingpro'),
            'desc' => __('Enable to allow Timekit Bookings. (Available only for business listing owners) ', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'my_listings',
            'type' => 'switch',
            'title' => __('Listings', 'listingpro'),
            'desc' => __('Enable to show authors listings within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'saved_listing',
            'type' => 'switch',
            'title' => __('Saved', 'listingpro'),
            'desc' => __('Enable to show saved listings within the dashboard.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'invoices_dashboard',
            'type' => 'switch',
            'title' => __('Invoices', 'listingpro'),
            'desc' => __('Enable to show invoices within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'dashboard_packages',
            'type' => 'switch',
            'title' => __('Packages', 'listingpro'),
            'desc' => __('Enable to show packages within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'ad_compaigns',
            'type' => 'switch',
            'title' => __('Ad Campaign', 'listingpro'),
            'desc' => __('Enable to show and allow the creation of Ad Campaigns within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'review_dashoard',
            'type' => 'switch',
            'title' => __('Reviews', 'listingpro'),
            'desc' => __('Enable to show and reply to reviews within the dashboard.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'booking_dashoard',
            'type' => 'switch',
            'title' => __('Appointments', 'listingpro'),
            'desc' => __('Enable to show Appointments within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => false,
        ),
        array(
            'id' => 'menu_dashoard',
            'type' => 'switch',
            'title' => __('Menus', 'listingpro'),
            'desc' => __('Enable to show and allow the creation of Menus within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'menu_gallery_dashoard',
            'type' => 'switch',
            'title' => __('Menus Image Gallery', 'listingpro'),
            'desc' => __('(Available only for business listing owners) ', 'listingpro'),
            'default' => false,
            'required' => array('menu_dashoard', 'equals', '1'),
        ),
        array(
            'id' => 'img_menu_dashoard',
            'type' => 'switch',
            'title' => __('Image Menu', 'listingpro'),
            'desc' => __('(Available only for business listing owners)', 'listingpro'),
            'default' => false,
            'required' => array('menu_dashoard', 'equals', '1'),
        ),
        array(
            'id' => 'announcements_dashoard',
            'type' => 'switch',
            'title' => __('Announcements', 'listingpro'),
            'desc' => __('Enable to show and allow the creation of Announcements within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'events_dashoard',
            'type' => 'switch',
            'title' => __('Events', 'listingpro'),
            'desc' => __('Enable to show and allow the creation of Events within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'discounts_dashoard',
            'type' => 'switch',
            'title' => __('Discount/Offers/Deals', 'listingpro'),
            'desc' => __('Enable to show and allow the creation of Discounts/Offers/Deals within the dashboard. (Available only for business listing owners)', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'my_profile',
            'type' => 'switch',
            'title' => __('My Profile', 'listingpro'),
            'desc' => '',
            'default' => true,
        ),
        array(
            'id' => 'log_outt',
            'type' => 'switch',
            'title' => __('Logout Button', 'listingpro'),
            'desc' => __('Enable to show the logout button within the dashboard.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'pub_pend_notice',
            'type' => 'switch',
            'title' => __('Notice to Listing', 'listingpro'),
            'desc' => '',
            'default' => true,
        ),
        array(
            'id' => 'campgn_notice',
            'type' => 'switch',
            'title' => __('Notice to Create Campaigns', 'listingpro'),
            'desc' => '',
            'default' => true,
        ),
        array(
            'id' => 'inbox_msg_del',
            'type' => 'switch',
            'title' => __('Delete Conversation Button', 'listingpro'),
            'desc' => __('Enable to show delete conversation button within the dashboard inbox.', 'listingpro'),
            'default' => false,
        ),
    )
));
// START Typo Section
Redux::setSection($opt_name, array(
    'title' => esc_html__('Typography', 'listingpro'),
    'id' => 'typography-settings',
    'customizer_width' => '400px',
    'icon' => 'el el-file-edit',
    'fields' => array(
        array(
            'id' => 'typography-body',
            'type' => 'typography',
            'title' => esc_html__('Body Font', 'listingpro'),
            'subtitle' => esc_html__('Specify the body font properties.', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'default' => array(
                'color' => '#7f7f7f',
                'font-size' => '',
                'font-family' => 'Quicksand',
                'font-weight' => '400',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'nav_typo',
            'type' => 'typography',
            'title' => esc_html__('Navigation Style and Anchor', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'line-height' => false,
            'all_styles' => true,
            'color' => false,
            'output' => array('.menu-item a'),
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
            ),
        ),
        array(
            'id' => 'h1_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h1 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'h2_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h2 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'h3_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h3 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'h4_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h4 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'h5_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h5 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        ),
        array(
            'id' => 'h6_typo',
            'type' => 'typography',
            'title' => esc_html__('Heading h6 Style', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#333',
                'font-style' => '',
                'font-family' => 'Quicksand',
                'google' => true,
                'font-size' => '16px',
                'line-height' => '27px'
            ),
        ),
        array(
            'id' => 'paragraph_typo',
            'type' => 'typography',
            'title' => esc_html__('Paragraph and small elements', 'listingpro'),
            'google' => true,
            'font-backup' => false,
            'text-align' => false,
            'all_styles' => true,
            'units' => 'px',
            'subtitle' => esc_html__('Typography option with each property can be called individually.', 'listingpro'),
            'default' => array(
                'color' => '#7f7f7f',
                'font-style' => '',
                'font-family' => 'Open Sans',
                'google' => true,
                'font-size' => '',
                'line-height' => ''
            ),
        )
    )
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Header', 'listingpro'),
    'id' => 'Header',
    'customizer_width' => '400px',
    'icon' => 'el el-home',
    'fields' => array(
        array(
            'id' => 'header_views',
            'type' => 'image_select',
            'title' => esc_html__('Header layout', 'listingpro'),
            'desc' => esc_html__('Select from 1 of 9 Header Styles', 'listingpro'),
            'options' => array(
                'header_with_topbar' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-with-topbar.jpg'
                ),
                'header_menu_bar' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-menu-dropdown.jpg'
                ),
                'header_without_topbar' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-without-topbar.jpg'
                ),
                'header_with_topbar_menu' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-with-topbar-menu.jpg'
                ),
                /* 'header_with_bigmenu'      => array(
                        'alt'   => 'Listing detail layout',
                        'img'   => get_template_directory_uri().'/assets/images/admin/header-with-bigmenu.jpg'
                    ), */
                // New header layouts
                'header_style5' => array(
                    'alt' => 'Header Style 5',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-5.jpg'
                ),
                'header_style6' => array(
                    'alt' => 'Header Style 6',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-6.jpg'
                ),
                'header_style7' => array(
                    'alt' => 'Header Style 7',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-7.jpg'
                ),
                'header_style8' => array(
                    'alt' => 'Header Style 8',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-8.jpg'
                ),
                'header_style9' => array(
                    'alt' => 'Header Style 9',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-9.jpg'
                ),
				'header_style10' => array( //classic new style
                    'alt' => 'Header Classic New',
                    'img' => get_template_directory_uri() . '/assets/images/admin/header-classic-new.png'
                ),

            ),
            'default' => 'header_without_topbar'
        ),
        array(
            'id' => 'top_bar_enable',
            'type' => 'switch',
            'title' => __('Top Bar', 'listingpro'),
            'desc' => __('Enable to show Top Bar within Header ', 'listingpro'),
            'required' => array('header_views', 'equals', 'header_with_topbar'),
            'default' => 1,
        ),
        array(
            'id' => 'top_bar_bgcolor',
            'type' => 'color',
            'title' => __('Top Bar Background Color', 'listingpro'),
            'subtitle' => __('(default: #363F48).', 'listingpro'),
            'required' => array('top_bar_enable', 'equals', '1'),
            'default' => '#363F48',
            'validate' => 'color',
        ),
        array(
            'id' => 'top_bar_enable_new',
            'type' => 'switch',
            'title' => __('Enable/Disable Top bar', 'listingpro'),
            'desc' => __('Enable to show Top Bar within Header ', 'listingpro'),
            'required' => array('header_views', 'equals', 'header_with_topbar_menu'),
            'default' => 1,
        ),
        array(
            'id' => 'top_bar_bg_inner',
            'type' => 'color',
            'title' => __('Top bar Background Color', 'listingpro'),
            'subtitle' => __('(default: #42a7df).', 'listingpro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'top_bar_color_inner',
            'type' => 'color',
            'title' => __('Top bar Color', 'listingpro'),
            'subtitle' => __('(default: #797979).', 'listingpro'),
            'default' => '#797979',
            'validate' => 'color',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'top_bar_color_inner2',
            'type' => 'color',
            'title' => __('Top bar Color', 'listingpro'),
            'subtitle' => __('(default: #fff).', 'listingpro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'required' => array('header_views', 'equals', 'header_with_topbar'),
        ),
        array(
            'id' => 'top_bar_opacity',
            'type' => 'select',
            'title' => __('Set Opacity for Top Bar', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array('top_bar_enable_new', 'equals', '1'),
            // Must provide key => value pairs for select options
            'options' => array(
                '0.1' => '0.1',
                '0.2' => '0.2',
                '0.3' => '0.3',
                '0.4' => '0.4',
                '0.5' => '0.5',
                '0.6' => '0.6',
                '0.7' => '0.7',
                '0.8' => '0.8',
                '0.9' => '0.9',
            ),
            'default' => '0.5',
        ),
        array(
            'id' => 'header_fixed_front',
            'type' => 'switch',
            'title' => __('Fixed Header', 'listingpro'),
            'subtitle' => __('Position Top Fixed Header.', 'listingpro'),
            'default' => 'No',
        ),
        array(
            'id' => 'header_fixed_inner_page',
            'type' => 'switch',
            'title' => __('Fixed Header In Inner Pages', 'listingpro'),
            'subtitle' => __('Position Top Fixed Header In Inner Pages.', 'listingpro'),
            'default' => 'No',
        ),
        array(
            'id' => 'header_bgcolor',
            'type' => 'color',
            'title' => __('Header Background Color', 'listingpro'),
            'subtitle' => __('(default: #42a7df).', 'listingpro'),
            'default' => 'transparent',
            'validate' => 'color',
        ),
        array(
            'id' => 'header_bgcolor_inner_pages',
            'type' => 'color',
            'title' => __('Header Background Color Inner Pages', 'listingpro'),
            'subtitle' => __('(default: #42a7df).', 'listingpro'),
            'default' => '#42a7df',
            'validate' => 'color',
        ),
		array(
            'id' => 'app_view_header_bgcolor',
            'type' => 'color',
            'title' => __('App View Header Background Color', 'listingpro'),
            'subtitle' => __('(default: #42a7df).', 'listingpro'),
            'default' => 'transparent',
            'validate' => 'color',
        ),
        array(
            'id' => 'header_textcolor',
            'type' => 'color',
            'title' => __('Header Text and Border Color', 'listingpro'),
            'subtitle' => __('(default: #ffffff).', 'listingpro'),
            'default' => '#FFFFFF',
            'validate' => 'color',
        ),
        array(
            'id' => 'app_view_header_textcolor',
            'type' => 'color',
            'title' => __('App View Header Text/Icon Color', 'listingpro'),
            'subtitle' => __('(default: #ffffff).', 'listingpro'),
            'default' => '#FFFFFF',
            'validate' => 'color',
        ),
        array(
            'id' => 'header_cats_partypro',
            'type' => 'switch',
            'title' => __(' Categories in Header', 'listingpro'),
            'subtitle' => '',
            'required' => array('header_views', 'equals', 'header_with_bigmenu'),
            'default' => true,
        ),
        array(
            'id' => 'fb_h',
            'type' => 'text',
            'title' => __("Facebook URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'tw_h',
            'type' => 'text',
            'title' => __("Twitter URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'insta_h',
            'type' => 'text',
            'title' => __("Instagram URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'tumb_h',
            'type' => 'text',
            'title' => __("Tumbler URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'f-yout_h',
            'type' => 'text',
            'title' => __("Youtube URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'f-linked_h',
            'type' => 'text',
            'title' => __("LinkedIn URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'f-pintereset_h',
            'type' => 'text',
            'title' => __("Pinterest URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'f-vk_h',
            'type' => 'text',
            'title' => __("VK URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('top_bar_enable_new', 'equals', '1'),
        ),
        array(
            'id' => 'header_fullwidth',
            'type' => 'switch',
            'title' => __(' Header Layout', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'search_switcher',
            'type' => 'switch',
            'title' => __('Header Search', 'listingpro'),
            'desc' => __('Enable to show Search Fields within the Header.', 'listingpro'),
            'default' => false,
        ),
        array(
            'id' => 'primary_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('Home Page Logo', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your logo to show within the Header on the Homepage. ', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/logo.png'),
        ),
        array(
            'id' => 'seconday_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('Logo for Inner Pages', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your logo to show within the Header on Inner Pages.', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/logo2.png'),
        ),
		array(
            'id' => 'app_view_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('App View Logo', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your logo for App view only. ', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/logo.png'),
        ),
        array(
            'id' => 'theme_favicon',
            'type' => 'media',
            'url' => true,
            'title' => __('Favicon ', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your Favicon to show within the Browser Tab.', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/favicon.png'),
        ),
        array(
            'id' => 'page_header',
            'type' => 'media',
            'url' => true,
            'title' => __('Page header background image', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Upload your Image here', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/header-banner.jpg'),
        ),
        array(
            'id' => 'login_popup_style',
            'type' => 'image_select',
            'title' => esc_html__('Choose Login Popup style', 'listingpro'),
            'options' => array(
                'style1' => array(
                    'alt' => 'style 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/style1.jpg'
                ),
                'style2' => array(
                    'alt' => 'Style 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/style2.jpg'
                ),
            ),
            'default' => 'style1'
        ),
    )
));
// -> START Banner Fields
Redux::setSection($opt_name, array(
    'title' => __('Banner', 'listingpro'),
    'id' => 'search_settings',
    'customizer_width' => '400px',
    'icon' => 'el el-map-marker',
    'fields' => array(
        array(
            'id' => 'top_banner_styles',
            'type' => 'image_select',
            'title' => __('Homepage Banner Style', 'listingpro'),
            'subtitle' => '',
            'desc' => __('Select from 1 of 7 styles', 'listingpro'),
            // Must provide key => value pairs for select options
            'options' => array(
                'banner_view2' => array(
                    'alt' => 'Banner with Search',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style1.jpg'
                ),
                'banner_view3' => array(
                    'alt' => 'Banner with side-search',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style7.jpg'
                ),
                'banner_view' => array(
                    'alt' => 'Banner with Search Style2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style2.jpg'
                ),
                'map_view' => array(
                    'alt' => 'Map View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style3.jpg'
                ),
                // start banner different views added
                'banner_view_search_bottom' => array(
                    'alt' => 'Banner with Search Bottom',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style4.jpg'
                ),
                'banner_view_category_upper' => array(
                    'alt' => 'Banner with Category inside',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style5.jpg'
                ),
                'banner_view_search_inside' => array(
                    'alt' => 'Banner with search inside',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style6.jpg'
                ),
				 'banner_view_search_classic' => array(
                    'alt' => 'Banner with search clasic new',
                    'img' => get_template_directory_uri() . '/assets/images/admin/banner-style8.png'
                ),

                // End banner different views added
            ),
            'default' => 'banner_view',
        ),
        // <---------------------start new options--------------------->
        // Different search views for(banner with serach style 2)
        array(
            'id' => 'search_different_styles',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'search_view' => array(
                    'alt' => 'Search View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-default.png'
                ),
                'search_view1' => array(
                    'alt' => 'Search View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-1.png'
                ),
                'search_view2' => array(
                    'alt' => 'Search View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-2.png'
                ),
                'search_view3' => array(
                    'alt' => 'Search View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-3.png'
                )

            ),
            'default' => 'search_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view'))
            )
        ),
        // Different search views for (Banner with search bottom)
        array(
            'id' => 'search_different_styles_banner_search_overlap',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'search_view' => array(
                    'alt' => 'Search View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-overlap-view-1.jpg'
                ),
                'search_view1' => array(
                    'alt' => 'Search View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-overlap-view-2.jpg'
                ),
                'search_view2' => array(
                    'alt' => 'Search View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-overlap-view-3.jpg'
                ),
                'search_view3' => array(
                    'alt' => 'Search View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-overlap-view-4.jpg'
                )
            ),
            'default' => 'search_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_bottom'))
            )
        ),
        // Different search views for (Banner with search inside)
        array(
            'id' => 'search_different_styles_banner_search_inside',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'search_view' => array(
                    'alt' => 'Search View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-default.png'
                ),
                'search_view1' => array(
                    'alt' => 'Search View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-1.png'
                ),
                'search_view2' => array(
                    'alt' => 'Search View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-2.png'
                ),
                'search_view3' => array(
                    'alt' => 'Search View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-3.png'
                )
            ),
            'default' => 'search_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_inside'))
            )
        ),
        // Different search views for (Banner with category inside)
        array(
            'id' => 'search_different_styles_banner_category_inside',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'search_view' => array(
                    'alt' => 'Search View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-default.png'
                ),
                'search_view1' => array(
                    'alt' => 'Search View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-1.png'
                ),
                'search_view2' => array(
                    'alt' => 'Search View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-2.png'
                ),
                'search_view3' => array(
                    'alt' => 'Search View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-3.png'
                )
            ),//classic new style
            'default' => 'search_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_category_upper'))
            )
        ),  //classic new style
		array(
            'id' => 'search_different_styles_banner_classic',
            'type' => 'image_select',
            'title' => __('Select Search Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'search_view' => array(
                    'alt' => 'Search View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-default.png'
                ),
                'search_view1' => array(
                    'alt' => 'Search View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-1.png'
                ),
                'search_view2' => array(
                    'alt' => 'Search View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-2.png'
                ),
                'search_view3' => array(
                    'alt' => 'Search View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-3.png'
                ),
                'search_view4' => array(
                    'alt' => 'Search View 4',
                    'img' => get_template_directory_uri() . '/assets/images/admin/search-view-4.png'
                ),
            ),
            'default' => 'search_view4',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_classic'))
            )
        ),
        // Different Category Views
        array(
            'id' => 'categories_different_styles',
            'type' => 'image_select',
            'title' => __('Select Category Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'category_view' => array(
                    'alt' => 'Category View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style.png'
                ),
                'category_view1' => array(
                    'alt' => 'Category View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style1.png'
                ),
                'category_view2' => array(
                    'alt' => 'Category View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style2.png'
                ),
                'category_view3' => array(
                    'alt' => 'Category View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style-banner-view.png'
                ),
            ),
            'default' => 'category_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view',))
            )
        ),
        // Different Category Views for(search overlap)
        array(
            'id' => 'categories_different_styles_search_overlap',
            'type' => 'image_select',
            'title' => __('Select Category Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'category_view' => array(
                    'alt' => 'Category View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style.png'
                ),
                'category_view1' => array(
                    'alt' => 'Category View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style1.png'
                ),
                'category_view2' => array(
                    'alt' => 'Category View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style2.png'
                ),
                'category_view3' => array(
                    'alt' => 'Category View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style-banner-view.png'
                )
            ),
            'default' => 'category_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_bottom'))
            )
        ),
        // Different Category Views for(category inside)
        array(
            'id' => 'categories_different_styles_category_inside',
            'type' => 'image_select',
            'title' => __('Select Category Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'category_view' => array(
                    'alt' => 'Category View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style.png'
                ),
                'category_view1' => array(
                    'alt' => 'Category View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style1.png'
                ),
                'category_view2' => array(
                    'alt' => 'Category View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style2.png'
                ),
                'category_view3' => array(
                    'alt' => 'Category View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style-banner-view.png'
                ),
            ),
            'default' => 'category_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_category_upper'))
            )
        ),
        // Different Category Views for(search inside)
        array(
            'id' => 'categories_different_styles_search_inside',
            'type' => 'image_select',
            'title' => __('Select Category Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'category_view' => array(
                    'alt' => 'Category View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style.png'
                ),
                'category_view1' => array(
                    'alt' => 'Category View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style1.png'
                ),
                'category_view2' => array(
                    'alt' => 'Category View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style2.png'
                ),
                'category_view3' => array(
                    'alt' => 'Category View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style-banner-view.png'
                )
            ),
            'default' => 'category_view',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_inside'))
            )
        ),
		//classic new style
		// Different Category Views for(search inside new classic)
         array(
            'id' => 'categories_different_styles_search_inside_classic',
            'type' => 'image_select',
            'title' => __('Select Category Style', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'category_view' => array(
                    'alt' => 'Category View',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style.png'
                ),
                'category_view1' => array(
                    'alt' => 'Category View 1',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style1.png'
                ),
                'category_view2' => array(
                    'alt' => 'Category View 2',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style2.png'
                ),
                'category_view3' => array(
                    'alt' => 'Category View 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style-banner-view.png'
                ),
				'category_view4' => array(
                    'alt' => 'Category View 4',
                    'img' => get_template_directory_uri() . '/assets/images/admin/category-style4.png'
                ),

            ),
            'default' => 'category_view4',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_classic'))
            )
        ),

        // Category colors options
        array(
            'id' => 'categories_color',
            'type' => 'color',
            'title' => __('Category Background Color', 'listingpro'),
            'subtitle' => __('(default: #41a6df).', 'listingpro'),
            'default' => '#41a6df',
            'validate' => 'color',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_bottom', 'banner_view', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic'))
            )
        ),
        array(
            'id' => 'categories_hover_color',
            'type' => 'color',
            'title' => __('Category Background Hover Color', 'listingpro'),
            'subtitle' => __('(default: #363F48).', 'listingpro'),
            'default' => '#363F48',
            'validate' => 'color',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_bottom', 'banner_view', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic'))
            )
        ),
        array(
            'id' => 'categories_text_color',
            'type' => 'color',
            'title' => __('Category Text Color', 'listingpro'),
            'subtitle' => __('(default: #ffffff).', 'listingpro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view_search_bottom', 'banner_view', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic' , 'banner_view_search_classic'))
            )
        ),
        // new banner height option for some views
        array(
            'id' => 'banner_height_search_bottom',
            'type' => 'text',
            'title' => __('Banner height', 'listingpro'),
            'subtitle' => __('Add Banner Height (without unitPixel/Percent)', 'listingpro'),
            'default' => 370,
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view_search_bottom', 'banner_view_search_inside')),
            ),
        ),
        // <----------------End new options------------->
        array(
            'id' => 'banner_height',
            'type' => 'text',
            'title' => __('Banner height', 'listingpro'),
            'subtitle' => __('Add Banner Height (without unitPixel/Percent)', 'listingpro'),
            'default' => 610,
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view', 'banner_view2', 'banner_view_category_upper' , 'banner_view_search_classic')),
            ),
        ),
        array(
            'id' => 'banner_height2',
            'type' => 'text',
            'title' => __('Banner height', 'listingpro'),
            'subtitle' => __('Add Banner Height (without unitPixel/Percent)', 'listingpro'),
            'default' => 720,
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view3')),
            ),
        ),
        array(
            'id' => 'banner_opacity',
            'type' => 'select',
            'title' => __('Set opacity for banner', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            // Must provide key => value pairs for select options
            'options' => array(
                '0.0' => '0.0',
                '0.1' => '0.1',
                '0.2' => '0.2',
                '0.3' => '0.3',
                '0.4' => '0.4',
                '0.5' => '0.5',
                '0.6' => '0.6',
                '0.7' => '0.7',
                '0.8' => '0.8',
                '0.9' => '0.9',
            ),
            'default' => '0.6',
        ),
        array(
            'id' => 'lp_video_banner_on',
            'type' => 'select',
            'title' => esc_html__('Banner background type', 'listingpro'),
            'desc' => esc_html__('Select if banner shows static image or video.', 'listingpro'),
            'options' => array(
                'static_image' => 'Static Image',
                'video_banner' => 'Video Banner'
            ),
            'default' => 'static_image',
            'required' => array(
                array('top_banner_styles', '!=', 'map_view')
            )
        ),
        array(
            'id' => 'home_banner',
            'type' => 'media',
            'url' => true,
            'title' => __('Home Banner Image', 'listingpro'),
            'compiler' => 'true',
            'subtitle' => __('Upload image for homepage banner', 'listingpro'),
            'required' => array(
                array('top_banner_styles', '!=', 'map_view')
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/home-banner.jpg'),
        ),
        array(
            'id' => 'video_banner_img',
            'type' => 'media',
            'url' => true,
            'title' => __('Home Video Banner Image', 'listingpro'),
            'compiler' => 'true',
            'subtitle' => __('Upload video poster for homepage banner', 'listingpro'),
            'required' => array(
                array('lp_video_banner_on', '=', 'video_banner'),
                array('top_banner_styles', '!=', 'map_view')
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/dashboard-img.jpg'),
        ),
        array(
            'id' => 'vedio_type',
            'type' => 'select',
            'title' => __('Video Source', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array(
                array('lp_video_banner_on', '=', 'video_banner'),
            ),
            'options' => array(
                'video_youtube' => 'Youtube',
                'video_mp4' => 'MP4',
            ),
            'default' => 'video_mp4',
        ),
        array(
            'id' => 'vedio_url',
            'type' => 'text',
            'title' => __('MP4 Video Url', 'listingpro'),
            'desc' => __('Please use proper video URL with extension like .mp4. Avoid using any video page URL like youtube or vimeo', 'listingpro'),
            'subtitle' => __('Upload video here to show in banner', 'listingpro'),
            'required' => array(
                array('lp_video_banner_on', '=', 'video_banner'),
                array('vedio_type', '=', 'video_mp4'),
            ),
            'default' => '',
        ),
        array(
            'id' => 'vedio_url_yt',
            'type' => 'text',
            'title' => __('Youtube Video Url', 'listingpro'),
            'desc' => __('Please use proper video URL from youtube', 'listingpro'),
            'subtitle' => __('Upload video here to show in banner', 'listingpro'),
            'required' => array(
                array('lp_video_banner_on', '=', 'video_banner'),
                array('vedio_type', '=', 'video_youtube'),
            ),
            'default' => '',
        ),
        array(
            'id' => 'video_search_layout',
            'type' => 'select',
            'title' => __('Select Search Mode For Video', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array(
                array('lp_video_banner_on', '=', 'video_banner'),
                array('top_banner_styles', '!=', 'banner_view2'),
            ),
            'options' => array(
                'align_center' => 'Align Center',
                'align_bottom_video' => 'Align bottom under Video',
                //'align_bottom_on_banner' => 'Align bottom On Banner',
            ),
            'default' => 'align_center',
        ),
        array(
            'id' => 'courtesy_switcher',
            'type' => 'switch',
            'title' => __('Courtesy Listing On/Off', 'listingpro'),
            //                'subtitle' => __('Is this banner courtesy with any listing', 'listingpro'),
            'desc' => __('Link image to listing if images have been provided by a listing', 'listingpro'),
            'required' => array(
                array('top_banner_styles', 'equals', 'banner_view', 'banner_view_search_bottom', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic')
            ),
            'default' => false,
        ),
        array(
            'id' => 'courtesy_listing',
            'type' => 'text',
            'title' => __('Listing ID', 'listingpro'),
            //                'subtitle' => __( 'Add listing ID here', 'listingpro' ),
            'desc' => __('Listing ID for a link to the listing ', 'listingpro'),
            'required' => array(
                array('courtesy_switcher', 'equals', 1),
            ),
            'default' => '',
        ),
        /* array(
                'id'        =>'courtesy_listing',
                'type'      => 'select',
                'multi'     => false,
                'required' => array(
                    array('courtesy_switcher','equals', 1),
                ),
                'data'      => 'posts',
                'args'      => array('post_type' => array('listing'), 'posts_per_page' => -1),
                'title'     => esc_html__('Select listing', 'listingpro'),
                'desc'      => '',
            ), */
        array(
            'id' => 'home_banner_taxonomy',
            'type' => 'select',
            'title' => esc_html__('Banner Taxonomy', 'listingpro'),
            'desc' => esc_html__('Choose if banner catgory boxes show Category, Location or features', 'listingpro'),
            'subtitle' => '',
            'options' => array(
                'tax_cats' => 'Categories',
                'tax_locs' => 'Locations',
                'tax_feats' => 'Features'
            ),
            'default' => 'tax_cats',
            'required' => array(
                array('top_banner_styles', '=', array('banner_view', 'banner_view3', 'banner_view2', 'banner_view_search_bottom', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic'))
            )
        ),
        array(
            'id' => 'home_banner_cats',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => 'listing-category', 'hide_empty' => false),
            'multi' => true,
            'title' => __('Listing Categories', 'listingpro'),
            //                'subtitle' => __('These categories will be appeared on the homepage Banner', 'listingpro'),
            'desc' => __('Select Categories to show for banner taxonomy', 'listingpro'),
            'default' => '',
            'required' => array(
                array('home_banner_taxonomy', '=', 'tax_cats')
            )
        ),
        array(
            'id' => 'default_search_cats',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => 'listing-category', 'hide_empty' => false),
            'multi' => true,
            'title' => __('Select listing categories for dropdown in search (WHAT field)', 'listingpro'),
            //                'subtitle' => __('These categories will be appeared on search dropdown', 'listingpro'),
            'desc' => __('Select Categories to show in the search dropdown', 'listingpro'),
            'default' => '',
        ),
        array(
            'id' => 'home_banner_locs',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => 'location', 'hide_empty' => false),
            'multi' => true,
            'title' => __('Locations', 'listingpro'),
            //                'subtitle' => __('These categories will be appeared on the homepage Banner', 'listingpro'),
            'desc' => __('Select Location to show for banner taxonomy', 'listingpro'),
            'default' => '',
            'required' => array(
                array('home_banner_taxonomy', '=', 'tax_locs')
            )
        ),
        array(
            'id' => 'home_banner_feats',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => 'features', 'hide_empty' => false),
            'multi' => true,
            'title' => __('listing Features', 'listingpro'),
            //                'subtitle' => __('These categories will be appeared on the homepage Banner', 'listingpro'),
            'desc' => __('Select Listing Features to show under the home banner. To add new Listing Features <a href="' . admin_url('edit-tags.php?taxonomy=features&post_type=listing') . '" target="_blank">click here</a>.', 'listingpro'),
            'default' => '',
            'required' => array(
                array('home_banner_taxonomy', '=', 'tax_feats')
            )
        ),
        array(
            'id' => 'search_views',
            'type' => 'select',
            'title' => __('Search View', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'map_view')
            ),
            // Must provide key => value pairs for select options
            'options' => array(
                'light' => 'Light',
                'dark' => 'Dark',
                'grey' => 'Grey',
            ),
            'default' => 'light',
        ),
        array(
            'id' => 'search_alignment',
            'type' => 'select',
            'title' => __('Search Alignment', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'map_view')
            ),
            // Must provide key => value pairs for select options
            'options' => array(
                'align_top' => 'Align with Top Navbar',
                'align_middle' => 'Align bottom under banner',
                'align_bottom' => 'Align bottom after banner',
            ),
            'default' => 'align_middle',
        ),
        array(
            'id' => 'search_layout',
            'type' => 'select',
            'title' => __('Search Mode', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'map_view')
            ),
            // Must provide key => value pairs for select options
            'options' => array(
                'fullwidth' => 'Fullwidth',
                'boxed' => 'Boxed',
            ),
            'default' => 'boxed',
        ),
        array(
            'id' => 'cat_txt',
            'type' => 'text',
            'title' => __('Categories Text', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'map_view'),
                array('search_alignment', '!=', 'align_top')
            ),
            'default' => 'Just looking around? Let us suggest you something hot & happening! ',
        ),
        array(
            'id' => 'map_height',
            'type' => 'text',
            'title' => __('Map Height', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'map_view'),
            ),
            'desc' => 'Only use numbers do not use Px. i.e 500',
            'default' => '550',
        ),
        array(
            'id' => 'search_placeholder',
            'type' => 'text',
            'title' => __('Banner Search Placeholder', 'listingpro'),
            'subtitle' => '',
            'default' => 'Ex: food, service, barber, hotel',
        ),
        array(
            'id' => 'location_default_text',
            'type' => 'text',
            'title' => __('Location Default Text', 'listingpro'),
            'subtitle' => '',
            'default' => 'Your City...',
        ),
        array(
            'id' => 'top_title',
            'type' => 'text',
            'title' => __('Search Top Title', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'banner_view', 'banner_view_search_bottom', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic')
            ),
            'default' => "Let's uncover the best places to eat, drink, and shop nearest to you.",
        ),
        array(
            'id' => 'lp-sidebar-search-location_text',
            'type' => 'text',
            'title' => __('Sidebar Search Location Text', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view3')),
            ),
            'default' => '# 1 Best Rated Restaurant Directory in San Jose, California',
        ),
        array(
            'id' => 'top_main_title',
            'type' => 'text',
            'title' => __('Search Main Text', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'banner_view', 'banner_view_search_bottom', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic')
            ),
            'default' => 'Explore <span class="lp-dyn-city">Your city</span>',
        ),
        array(
            'id' => 'main_text',
            'type' => 'text',
            'title' => __('Search Main Text', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', 'banner_view', 'banner_view_search_bottom', 'banner_view_category_upper', 'banner_view_search_inside' , 'banner_view_search_classic')
            ),
            'default' => 'Just looking around? Let us suggest you something hot & happening! ',
        ),
        array(
            'id' => 'search_small_title',
            'type' => 'text',
            'title' => __('Search Upper Small Text', 'listingpro'),
            'subtitle' => '',
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view3'))
            ),
            'default' => ' Over 10,0000 listed businesses and experiences ',
        ),
        array(
            'id' => 'arrow_image',
            'type' => 'switch',
            'title' => __('Banner Arrow Image', 'listingpro'),
            'desc' => __('Enable to show an arrow in banner pointing to taxonomy boxes', 'listingpro'),
            'default' => true,
            'required' => array(
                array('top_banner_styles', 'equals', array('map_view', 'banner_view', 'banner_view_category_upper', 'banner_view_search_bottom', 'banner_view_search_inside'))
            )
        ),
        array(
            'id' => 'banner_logo_search2',
            'type' => 'media',
            'url' => true,
            'title' => __('Banner Logo', 'listingpro'),
            'compiler' => 'true',
            'subtitle' => __('Upload image for logo on banner search form', 'listingpro'),
            'required' => array(
                array('top_banner_styles', 'equals', array('banner_view2')),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/lp-logo1.png'),
        ),
        /* for v2 */
        array(
            'id' => 'map_listing_by',
            'type' => 'select',
            'title' => __('Show Listing Pin By', 'listingpro'),
            'subtitle' => '',
            'desc' => __('On home page map, show pin based on the selection', 'listingpro'),
            'required' => array(
                array('top_banner_styles', '=', 'map_view'),
            ),
            'options' => array(
                'all' => 'All Listings',
                'by_category' => 'By Category',
                'geolocaion' => 'User city (Based on Wordpress Location taxonomy)',
            ),
            'default' => 'geolocaion',
        ),
        array(
            'id' => 'lp_listing_cat_on_map',
            'type' => 'select',
            'data' => 'terms',
            'args' => array('taxonomies' => 'listing-category', 'args' => array()),
            'title' => __('Category', 'listingpro'),
            'subtitle' => __('Please choose a category.', 'listingpro'),
            'required' => array(
                array('top_banner_styles', '=', 'map_view'),
                array('map_listing_by', '=', 'by_category'),
            ),
            'desc' => __('Select a listing category', 'listingpro'),
        ),
        /* end for v2 */
    )
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Map', 'listingpro'),
    'id' => 'map_settings',
    'customizer_width' => '400px',
    'icon' => 'el el-home',
    'fields' => array(
        array(
            'id' => 'google_map_api',
            'type' => 'text',
            'title' => __('Google Map & Places API Key', 'listingpro'),
            'subtitle' => __('Please set your own google map API key for your site( default key is for only demo)', 'listingpro'),
            'desc' => __('Get your Google Maps API Key here. <br> https://developers.google.com/maps/documentation/javascript/get-api-key', 'listingpro'),
            'default' => 'AIzaSyDQIbsz2wFeL42Dp9KaL4o4cJKJu4r8Tvg',
        ),
        array(
            'id' => 'map_option',
            'type' => 'select',
            'title' => __('Map Type', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'options' => array(
                'openstreet' => 'OpenStreet Map',
                'google' => 'Google Map',
                'mapbox' => 'MapBox API',
            ),
            'default' => 'openstreet',
        ),
        array(
            'id' => 'mapbox_token',
            'type' => 'text',
            'title' => __('Mapbox Token', 'listingpro'),
            'subtitle' => __('Put here MapBox token, If you leave it empty then Google map will work', 'listingpro'),
            'desc' => __('Get your Mapbox Key here.<br>https://account.mapbox.com/access-tokens/create', 'listingpro'),
            'required' => array(
                array('map_option', 'equals', 'mapbox')
            ),
            'default' => '',
        ),
        array(
            'id' => 'map_style',
            'type' => 'text',
            'title' => esc_html__('Mapbox Map Style Id', 'listingpro'),
            'subtitle' => esc_html__('Type Your Custom Style ID', 'listingpro'),
            'desc' => esc_html__('Type how you want the Mapbox map to show.', 'listingpro'),
            'required' => array(
                array('map_option', 'equals', 'mapbox')
            ),
            'default' => 'mapbox/streets-v11'
        ),
        //new code 2.6.15
        array(
            'id' => 'map_zoom',
            'type' => 'text',
            'title' => esc_html__('Default Map Zoom On Listing Details Page', 'listingpro'),
            'subtitle' => esc_html__('Enter Your Map Zoom', 'listingpro'),
            'desc' => __('Enter only digits from 0 to 17. e-g 11', 'listingpro'),
            'default' => 17,
        ),
        //end new code 2.6.15
    )
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Blog', 'listingpro'),
    'id' => 'lp_blog_settings',
    'customizer_width' => '400px',
    'icon' => 'el el-comment',
    'fields' => array(
        array(
            'id' => 'blog_view',
            'type' => 'select',
            'title' => __('Blog View', 'listingpro'),
            'desc' => '',
            'options' => array(
                'list_view' => 'List View',
                'list_view2' => 'List View 2',
                'grid_view' => 'Grid View',
            ),
            'default' => 'grid_view',
        ),
        array(
            'id' => 'blog_grid_view',
            'type' => 'select',
            'title' => __('Blog Grid View Style', 'listingpro'),
            'desc' => '',
            'options' => array(
                'grid_view_style1' => 'Grid View Style1',
                'grid_view_style2' => 'Grid View Style2',
                'grid_view_style3' => 'Grid View Style3',
                'grid_view_style4' => 'Grid View Style4',
            ),
            'default' => 'grid_view_style1',
        ),
        array(
            'id' => 'blog_template',
            'type' => 'select',
            'title' => __('Blog Template', 'listingpro'),
            'desc' => '',
            'options' => array(
                'fullwidth' => 'Full Width',
                'left_sidebar' => 'Left Sidebar',
                'right_sidebar' => 'Right Sidebar',
            ),
            'default' => 'fullwidth',
        ),
        array(
            'id' => 'blog_single_template',
            'type' => 'select',
            'title' => __('Blog Detail Template', 'listingpro'),
            'desc' => '',
            'options' => array(
                'fullwidth' => 'Full Width',
                'left_sidebar' => 'Left Sidebar',
                'right_sidebar' => 'Right Sidebar',
                'fullwidth2' => 'Full Width Style2',
                'left_sidebar2' => 'Left Sidebar Style2',
                'right_sidebar2' => 'Right Sidebar Style2',
            ),
            'default' => 'fullwidth',
        ),
        array(
            'id' => 'blog_sidebar_style',
            'type' => 'select',
            'title' => __('Blog Sidebar Style', 'listingpro'),
            'desc' => '',
            'options' => array(
                'sidebar_style1' => 'SideBar Style1',
                'sidebar_style2' => 'SideBar Style2',
            ),
            'default' => 'sidebar_style1',
        ),
    ),
));
include_once(ABSPATH . 'wp-admin/includes/plugin.php');

// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Listing', 'listingpro'),
    'id' => 'general_settings',
    'customizer_width' => '400px',
    'icon' => 'el el-list-alt',
    'fields' => array()
));
Redux::setSection($opt_name, array(
    'title' => __('General', 'listingpro'),
    'id' => 'listing_setting_general',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'single_listing_mobile_view',
            'type' => 'select',
            'title' => __('Mobile View', 'listingpro'),
            'desc' => __('Select between Responsive & App View. Find out more here ', 'listingpro'),
            'options' => array(
                'responsive_view' => 'Responsive View',
                'app_view' => 'App View',
                'app_view2' => 'App View 2',
            ),
            'default' => 'responsive_view',
        ),
        array(
            'id' => 'app_view_menu_header_img',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload App View Menu Header Image', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload image to be used as the app view menu header', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/admin/adminbg.jpg'),
            'required' => array(
                array('single_listing_mobile_view', 'equals', array('app_view', 'app_view2'))
            )
        ),
        array(
            'id' => 'app_view_home',
            'type' => 'select',
            'data' => 'pages',
            'title' => __('Home Page for App View', 'listingpro'),
            'subtitle' => __('Enter url for home page on app view', 'listingpro'),
            'desc' => __('Select page for App View to use as Homepage. This allows you to create a separate page for Desktop and Mobile which allows you to layout the homepage depending on screen size.', 'listingpro'),
            'default' => '',
            'required' => array(
                array('single_listing_mobile_view', 'equals', array('app_view', 'app_view2'))
            ),
        ),
		array(
            'id' => 'listingpro_responsive_footer_app_switch',
            'type' => 'button_set',
            'title' => __('Enable if you want to show Responsive Footer in App View', 'listingpro'),
            'subtitle' => __('This option is to show terms Responsive View Footer in the App View or App view2', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
			'required' => array(
                array('single_listing_mobile_view', 'equals', array('app_view', 'app_view2'))
            )
        ),
        /* array(
                   'id'       => 'lp_select_view_archive',
                   'type'     => 'select',
                    'title'    => __('Listing Archive App View', 'listingpro'),
                    'desc'     => __('Listing View Activate On App View Archive Page', 'listingpro'),
                    'options'  => array(
                        'list_archive_view' => 'List View',
                        'grig_archive_view' => 'Grid View',
                    ),
                    'default'  => 'list_archive_view',
				), */
        array(
            'id' => 'lp_default_map_location_lat',
            'type' => 'text',
            'title' => __('Default Map Location Latitude', 'listingpro'),
            'subtitle' => __('Enter latitude for default map location', 'listingpro'),
            'desc' => __('This option will work on map only and only if there is no listing result found', 'listingpro'),
            'default' => '0',
        ),
        array(
            'id' => 'lp_default_map_location_long',
            'type' => 'text',
            'title' => __('Default Map Location Longitude', 'listingpro'),
            'subtitle' => __('Enter longitude for default map location', 'listingpro'),
            'desc' => __('This option will work on map only and only if there is no listing result found', 'listingpro'),
            'default' => '-0',
        ),
        array(
            'id' => 'lp_archivepage_listingorderby',
            'type' => 'select',
            'title' => __('Listings Order By', 'listingpro'),
            'subtitle' => '',
            'desc' => __('Order By Listings on archive/search result page', 'listingpro'),
            'options' => array(
                'title' => 'Title',
                'date' => 'Date',
                'rand' => 'Random',
                'post_views_count' => 'Most Viewed',
                'listing_reviewed' => 'Most Reviewed',
                'listing_rate' => 'Highest Rated',
                'claimed' => 'Claimed',
            ),
            'default' => 'date',
        ),
        array(
            'id' => 'lp_archivepage_listingorder',
            'type' => 'select',
            'title' => __('Listings Order', 'listingpro'),
            'subtitle' => '',
            'desc' => __('Listings Order on archive/search result page', 'listingpro'),
            'required' => array(
                array('lp_archivepage_listingorderby', 'equals', array('title', 'date', 'post_views_count', 'reviewed', 'listing_reviewed', 'listing_rate', 'claimed'))
            ),
            'options' => array(
                'ASC' => 'ASC',
                'DESC' => 'DESC',
            ),
            'default' => 'ASC',
        ),
        array(
            'id' => 'lp_allow_vistor_submit',
            'type' => 'switch',
            'title' => esc_html__('Add listing only by logged in users', 'listingpro'),
            'desc' => esc_html__('Only logged in users can submit listings', 'listingpro'),
            'default' => false,
        ),
        array(
            'id' => 'timing_option',
            'type' => 'select',
            'title' => __('Select Timing Format', 'listingpro'),
            'desc' => '',
            'options' => array(
                '24' => '24H Format',
                '12' => '12H Format',
            ),
            'default' => '12',
        ),
        array(
            'id' => 'contact_support',
            'type' => 'text',
            'title' => __('Contact Support Page', 'listingpro'),
            'desc' => __('The select page that Contact Support button links to.', 'listingpro'),
            'default' => '/Contact',
        ),
        array(
            'id' => 'lp_listing_change_plan_option',
            'type' => 'button_set',
            'title' => esc_html__('Change Plan Button', 'listingpro'),
            'desc' => esc_html__('Enable to allow users to change plan via the dashboard', 'listingpro'),
            'options' => array(
                'enable' => 'Enable',
                'disable' => 'Disable',
            ),
            'default' => 'enable',
        ),
        array(
            'id' => 'listing_per_page',
            'type' => 'text',
            'title' => __('Listings Per Page', 'listingpro'),
            'subtitle' => __('It will effect on taxonomy and search page.', 'listingpro'),
            'desc' => __('Enter only digits. e-g 11', 'listingpro'),
            'default' => '10',
        ),
        array(
            'id' => 'lp_def_featured_image',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload default featured image for listing', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload a default image to be used as the featured image, only used if the user doesn’t upload an image. ', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/default/placeholder.png'),
        ),
        array(
            'id' => 'lp_def_featured_image_from_gallery',
            'type' => 'button_set',
            'title' => esc_html__('Featured image from gallery', 'listingpro'),
            'desc' => esc_html__('Enable to use an image from listing gallery as a featured image if a featured image isn’t uploaded.', 'listingpro'),
            'options' => array(
                'enable' => 'Enable',
                'disable' => 'Disable',
            ),
            'default' => 'disable',
        ),
        array(
            'id' => 'lp_map_pin',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Map Pin Image for Contact Us Page and Listing Details Page', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload your image', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/pins/pin.png'),
        ),
        array(
            'id' => 'lp_detail_slider_styles',
            'type' => 'select',
            'title' => __('Select Slider Type', 'listingpro'),
            'desc' => '',
            'options' => array(
                'style1' => 'Style 1',
                'style2' => 'Style 2',
                'style3' => 'Style 3',
				'style4' => 'Style 4' //classic new style
            ),
            'default' => 'style1',
        ),
        array(
            'id' => 'slider_height',
            'type' => 'text',
            'title' => __('Gallery Height', 'listingpro'),
            'subtitle' => __('Add Slider Height (without unitPixel/Percent)', 'listingpro'),
            'required' => array(
                array('lp_detail_slider_styles', 'equals', 'style2')
            ),
            'default' => 354,
        ),
		array(
            'id' => 'listingpro_check_live_pages_switch',
            'type' => 'button_set',
            'title' => __('Disable auto page creation if deleted', 'listingpro'),
            'subtitle' => __('Set Yes if you want to disable auto re-creation of core pages', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'edit_listing_pending_switch',
            'type' => 'switch',
            'title' => esc_html__('Change Listing Status On Listing Moderation', 'listingpro'),
            'subtitle' => esc_html__('ON=Pending, OFF=Publish', 'listingpro'),
            'desc' => esc_html__('Enable to change the listing to pending after listing is edited. The listing will not show until published by admin.', 'listingpro'),
            'default' => false,
        ),
        /* for v2 */
        array(
            'id' => 'lp_icon_for_archive_pages_switch',
            'type' => 'button_set',
            'title' => esc_html__('Custom Icon for Archive & Search Pages', 'listingpro'),
            'subtitle' => esc_html__('If Enable => It means that custom icon will work on listing archive and search result pages', 'listingpro'),
            'options' => array(
                'enable' => 'Enable',
                'disable' => 'Disable',
            ),
            'default' => 'disable',
        ),
        array(
            'id' => 'lp_icon_for_archive_search_pages',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Uploade custom icon for Archive & Search Pages', 'listingpro'),
            'compiler' => 'true',
            'required' => array(
                array('lp_icon_for_archive_pages_switch', 'equals', 'enable')
            ),
            'desc' => esc_html__('Please upload a PNG icon', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/pins/default.png'),
        ),
        /* end for v2 */
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Listing Detail', 'listingpro'),
    'id' => 'lp-detail-page-manager',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'lp_detail_page_styles',
            'type' => 'select',
            'title' => __('Select listing detail page Style', 'listingpro'),
            'desc' => __('Choose from 1 of 4 styles.', 'listingpro'),
            'options' => array(
                'lp_detail_page_styles1' => 'Listing Detail Page Style 1',
                'lp_detail_page_styles2' => 'Listing Detail Page Style 2',
                'lp_detail_page_styles3' => 'Listing Detail Page Style 3',
                'lp_detail_page_styles4' => 'Listing Detail Page Style 4',
				'lp_detail_page_styles6' => 'Listing Detail Page Style 5' //classic new style
                /* 'lp_detail_page_styles5' => 'Listing Detail Page Style 5', */
            ),
            'default' => 'lp_detail_page_styles1',
        ),
        array(
            'id' => 'lp-detail-page-layout5-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles5'),
            'desc' => 'Shuffle elements within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_content_section' => 'Details',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_faqs_section' => 'FAQ\'s',
                    'lp_services_section' => 'Services',
                    'lp_reviews_section' => 'Reviews',
                    'lp_quote_section' => 'Quote Form',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        /* for listing layout3 & 4 */
        array(
            'id' => 'lp-detail-page-layout4-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles4'),
            'desc' => 'Shuffle elements within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_content_section' => 'Details',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_additional_section' => 'Additional Details',
                    'lp_features_section' => 'Listing Features',
                    'lp_faqs_section' => 'FAQ\'s',
                    'lp_event_section' => 'Event',
                    'lp_reviews_section' => 'Reviews',
                    'lp_reviewform_section' => 'Review Form',
                    'lp_menu_section' => 'Menu',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout4-rsidebar',
            'type' => 'sorter',
            'title' => 'Sidebar Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles4'),
            'desc' => 'Shuffle elements within Listing SideBar',
            'compiler' => 'true',
            'options' => array(
                'sidebar' => array(
                    'lp_sidebar_video' => 'Video',
                    'lp_mapsocial_section' => 'Map/Contacts',
                    'lp_event_section' => 'Event',
                    'lp_booking_section' => 'Appointments',
                    'lp_timing_section' => 'Timings',
                    'lp_quicks_section' => 'Quick Actions',
                    'lp_additional_section' => 'Additional Details',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_leadform_section' => 'Leadform',
                    'lp_sidebarelemnts_section' => 'Detail Page Sidebar Widgets',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout3-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles3'),
            'desc' => 'Shuffle elements within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_content_section' => 'Details',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_additional_section' => 'Additional Details',
                    'lp_features_section' => 'Listing Features',
                    'lp_faqs_section' => 'FAQ\'s',
                    'lp_event_section' => 'Event',
                    'lp_reviews_section' => 'Reviews',
                    'lp_reviewform_section' => 'Review Form',
                    'lp_menu_section' => 'Menu',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout3-rsidebar',
            'type' => 'sorter',
            'title' => 'Sidebar Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles3'),
            'desc' => 'Shuffle elements within Listing SideBar',
            'compiler' => 'true',
            'options' => array(
                'sidebar' => array(
                    'lp_mapsocial_section' => 'Map/Contacts',
                    'lp_event_section' => 'Event',
                    'lp_booking_section' => 'Appointments',
                    'lp_timing_section' => 'Timings',
                    'lp_quicks_section' => 'Quick Actions',
                    'lp_additional_section' => 'Additional Details',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_leadform_section' => 'Leadform',
                    'lp_sidebarelemnts_section' => 'Detail Page Sidebar Widgets',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp_detail_page_styles4_bg',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Header Background Image', 'listingpro'),
            'subtitle' => esc_html__('Top background image for this style', 'listingpro'),
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles4'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/home-banner.jpg'),
        ),
        array(
            'id'   => 'lp_detail_page_styles4_bg_color',
            'type' => 'color',
            'title' => __('Header Background Color', 'listingpro'),
            'subtitle' => __('(default: #1d2327).', 'listingpro'),
            'desc' => __('This color will use as header background image overlay, and if there is no image selected, then this color will operate as a header solid color background.', 'listingpro'),
            'default' => '#1d2327',
            'validate' => 'color',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles4'),
        ),
        /* for listing layout1 */
        array(
            'id' => 'lp-detail-page-layout-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles1'),
            'desc' => 'Shuffle elements within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_video_section' => 'Youtube Video',
                    'lp_content_section' => 'Details',
                    // New update 2.6.10
                    // 'lp_openFields_section' => 'Listing Global Form Fields',
                    // End New update 2.6.10
                    'lp_features_section' => 'Listing Features',
                    'lp_additional_section' => 'Additional Details',
                    'lp_faqs_section' => 'FAQ\'s',
                    'lp_event_section' => 'Event',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_menu_section' => 'Menu',
                    'lp_reviews_section' => 'Reviews',
                    'lp_reviewform_section' => 'Review Form',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout-rsidebar',
            'type' => 'sorter',
            'title' => 'Sidebar Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles1'),
            'desc' => 'Shuffle elements within Listing SideBar',
            'compiler' => 'true',
            'options' => array(
                'sidebar' => array(
                    'lp_timing_section' => 'Timings',
                    'lp_mapsocial_section' => 'Map/Contacts',
                    'lp_booking_section' => 'Appointments',
                    'lp_event_section' => 'Event',
                    'lp_leadform_section' => 'Leadform',
                    'lp_quicks_section' => 'Quick Actions',
                    'lp_additional_section' => 'Additional Details',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_sidebarelemnts_section' => 'Detail Page Sidebar Widgets',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        /* for listing layout2 */
        array(
            'id' => 'lp-detail-page-layout2-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles2'),
            'desc' => 'Shuffle tabs order within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_content_section' => 'Details',
                    'lp_additional_section' => 'Additional Details',
                    'lp_video_section' => 'Youtube Video',
                    'lp_faqs_section' => 'FAQ\'s',
                    'lp_reviews_section' => 'Reviews',
                    'lp_contacts_section' => 'Contact info',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_event_section' => 'Event',
                    'lp_menu_section' => 'Menu',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout2-rsidebar',
            'type' => 'sorter',
            'title' => 'Sidebar Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles2'),
            'desc' => 'Shuffle elements within Listing SideBar',
            'compiler' => 'true',
            'options' => array(
                'sidebar' => array(
                    'lp_timing_section' => 'Timings',
                    'lp_mapsocial_section' => 'Map/Contacts',
                    'lp_booking_section' => 'Appointments',
                    'lp_event_section' => 'Event',
                    'lp_quicks_section' => 'Quick Actions',
                    'lp_additional_section' => 'Additional Details',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_sidebarelemnts_section' => 'Detail Page Sidebar Widgets',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
		//classic new style
		array(
            'id' => 'lp-detail-page-layout6-content',
            'type' => 'sorter',
            'title' => 'Content Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles6'),
            'desc' => 'Shuffle elements within Listing Detail Content',
            'compiler' => 'true',
            'options' => array(
                'general' => array(
                    'lp_video_section' => 'Youtube Video',
                    'lp_content_section' => 'Details',
                    // New update 2.8                    // 'lp_openFields_section' => 'Listing Global Form Fields',
                    // End New update 2.8
                    'lp_features_section' => 'Listing Features',
                    'lp_additional_section' => 'Additional Details',
                    'lp_faqs_section' => 'FAQs',
                    'lp_event_section' => 'Event',
                    'lp_announcements_section' => 'Announcements',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_menu_section' => 'Menu',
                    'lp_reviews_section' => 'Reviews',
                    'lp_reviewform_section' => 'Review Form',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp-detail-page-layout6-rsidebar',
            'type' => 'sorter',
            'title' => 'Sidebar Layout',
            'required' => array('lp_detail_page_styles', 'equals', 'lp_detail_page_styles6'),
            'desc' => 'Shuffle elements within Listing SideBar',
            'compiler' => 'true',
            'options' => array(
                'sidebar' => array(
                    'lp_mapsocial_section' => 'Map/Contacts',
                    'lp_event_section' => 'Event',
                    'lp_booking_section' => 'Appointments',
                    'lp_timing_section' => 'Timings',
                    'lp_quicks_section' => 'Quick Actions',
                    'lp_additional_section' => 'Additional Details',
                    'lp_offers_section' => 'Offers/Discounts/Deals',
                    'lp_leadform_section' => 'Leadform',
                    'lp_sidebarelemnts_section' => 'Detail Page Sidebar Widgets',
                ),
                'disabled' => array(
                    '' => '',
                ),
            ),
        ),
        array(
            'id' => 'lp_detail_page_additional_styles',
            'type' => 'button_set',
            'title' => esc_html__('Additional Details Position', 'listingpro'),
            'subtitle' => esc_html__('Set additional details position to sidebar or below content area', 'listingpro'),
            //Must provide key => value pairs for options
            'options' => array(
                'left' => 'Left',
                'right' => 'Right',
            ),
            'default' => 'right',
        ),
        array(
            'id' => 'lp_detail_page_video_display',
            'type' => 'button_set',
            'title' => esc_html__('Video Display Option', 'listingpro'),
            'required' => array('lp_detail_page_styles', 'equals', array('lp_detail_page_styles6', 'lp_detail_page_styles1')), //classic new style
            'subtitle' => esc_html__('On=Show youtube video in popup, off=embed', 'listingpro'),
            'options' => array(
                'on' => 'On',
                'off' => 'Off',
            ),
            'default' => 'on',
        ),
        array(
            'id' => 'lp_detail_page_report_button',
            'type' => 'button_set',
            'title' => esc_html__('Detail Listing Page Report Button', 'listingpro'),
            'subtitle' => esc_html__('on/off', 'listingpro'),
            'desc' => esc_html__('Enable to show report listing button within listing', 'listingpro'),
            'options' => array(
                'on' => 'On',
                'off' => 'Off',
            ),
            'default' => 'on',
        ),
        array(
            'id' => 'discount_deals_offers_design',
            'type' => 'image_select',
            'title' => esc_html__('Deals/Offer Design (content)', 'listingpro'),
            'subtitle' => esc_html__('design for content area', 'listingpro'),
            'desc' => esc_html__('The design used when deals show within the content area.', 'listingpro'),
            'options' => array(
                '1' => array(
                    'alt' => 'Deals Design',
                    'img' => get_template_directory_uri() . '/assets/images/admin/deal-design.png'
                ),
                '2' => array(
                    'alt' => 'Offers Design',
                    'img' => get_template_directory_uri() . '/assets/images/admin/offer-design.png'
                ),
            ),
            'default' => '1'
        ),
        array(
            'id' => 'discount_deals_offers_design_sidebar',
            'type' => 'image_select',
            'title' => esc_html__('Discount/Deals (sidebar)', 'listingpro'),
            'subtitle' => esc_html__('design for sidebar area', 'listingpro'),
            'desc' => esc_html__('The design used when deals show within the sidebar.', 'listingpro'),
            'required' => array('lp_detail_page_styles', 'equals', array('lp_detail_page_styles4', 'lp_detail_page_styles3', 'lp_detail_page_styles2', 'lp_detail_page_styles1' , 'lp_detail_page_styles6')), //classic new style
            'options' => array(
                '1' => array(
                    'alt' => 'Deals Design',
                    'img' => get_template_directory_uri() . '/assets/images/admin/deal-design.png'
                ),
                '3' => array(
                    'alt' => 'Discounts Design',
                    'img' => get_template_directory_uri() . '/assets/images/admin/discount-desing.png'
                ),
            ),
            'default' => '3'
        ),
		array(
            'id' => 'listingpro_allow_logo_styles_switch',
            'type' => 'button_set',
            'title' => __('Business Logo in Different Styles ', 'listingpro'),
            'subtitle' => __('Enable if you want to show business logo in other than style 3', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
		array(
            'id' => 'listingpro_email_display_switch',
            'type' => 'button_set',
            'title' => __('Enable to display Email on Listing Details Page. ', 'listingpro'),
            'subtitle' => __('Enable if you want to show Email on Listing Details page sidebar in social section', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        /* array(
                    'id'       => 'lp_allow_user_customize_detail_page',
                    'type'     => 'button_set',
                    'title'    => esc_html__('Allow customization on front-end?', 'listingpro'),
                    'subtitle' => esc_html__('User will able to customize page from their dashboard', 'listingpro'),
					//Must provide key => value pairs for options
					'options' => array(
						'on' => 'On',
						'off' => 'Off',
					 ),
                    'default'  => 'on',
                ), */
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Listing Archive View', 'listingpro'),
    'id' => 'listing_view',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'listing_style',
            'type' => 'image_select',
            'title' => esc_html__('Listing page layout', 'listingpro'),
            'subtitle' => '',
            'options' => array(
                '1' => array(
                    'alt' => 'Fullwidth - top search',
                    'img' => get_template_directory_uri() . '/assets/images/admin/listing-search-top.jpg'
                ),
                //                       '2'      => array(
                //                            'alt'   => 'with sidebar search',
                //                            'img'   => get_template_directory_uri().'/assets/images/admin/listing-search.jpg'
                //                        ),
                '3' => array(
                    'alt' => 'Half map - half listing - top search',
                    'img' => get_template_directory_uri() . '/assets/images/admin/listing-map.jpg'
                ),
                '4' => array(
                    'alt' => 'Listing with header filters',
                    'img' => get_template_directory_uri() . '/assets/images/admin/lsiting_layout_4.jpg'
                ),
                '5' => array(
                    'alt' => 'Listing with sidebar filters',
                    'img' => get_template_directory_uri() . '/assets/images/admin/archive-style4.png'
                ),
				'6' => array(  
					'alt' => 'Classic New Style',
                    'img' => get_template_directory_uri() . '/assets/images/admin/archive-classic-style.png'
                ),

            ),
            'default' => '1'
        ),
        array(
            'id' => 'lp_archive_bg',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Background Image', 'listingpro'),
            'compiler' => 'true',
            'required' => array('listing_style', 'equals', '4'),
            'desc' => '',
            'default' => '',
        ),
        array(
            'id'   => 'lp_archive_bg_color',
            'type' => 'color',
            'title' => __('Header Background Color', 'listingpro'),
            'subtitle' => __('(default: #1d2327).', 'listingpro'),
            'desc' => __('This color will use as header background image overlay, and if there is no image selected, then this color will operate as a header solid color background.', 'listingpro'),
            'default' => '#1d2327',
            'validate' => 'color',
            'required' => array('listing_style', 'equals', '4'),
        ),
        array(
            'id' => 'lp_listing_sub_cats',
            'type' => 'select',
            'title' => __('Child Categories', 'listingpro'),
            'desc' => __('Enable to show child category boxes within the parent category ', 'listingpro'),
            'required' => array('listing_style', 'equals', '4'),
            'options' => array(
                'show' => 'Show',
                'hide' => 'Hide',
            ),
            'default' => 'show',
        ),
        array(
            'id' => 'lp_listing_sub_cats_lcation',
            'type' => 'select',
            'title' => __('Display Style for child category boxes', 'listingpro'),
            'subtitle' => __('Choose display style for child categories ', 'listingpro'),
            'required' => array('lp_listing_sub_cats', 'equals', 'show'),
            'options' => array(
                'fullwidth' => 'Fullwidth',
                'content' => 'Content Area',
            ),
            'default' => 'content',
        ),
        array(
            'id' => 'c_ad',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Ad Image', 'listingpro'),
            'compiler' => 'true',
            'required' => array('listing_style', 'equals', '2'),
            'desc' => esc_html__('Put ad here', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/boximage2.jpg'),
        ),
        array(
            'id' => 'listing_views',
            'type' => 'image_select',
            'title' => esc_html__('Listing grid/list ', 'listingpro'),
            'subtitle' => '',
            'options' => array(
                'grid_view' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid-view.png'
                ),
                'grid_view2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid3.png'
                ),
                'grid_view3' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid4.png'
                ),
                'grid_view_v2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/gird_view_v2.jpg'
                ),
                'grid_view_v3' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/gird_view_v3.jpg'
                ),
                'list_view' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/listing-view.png'
                ),
                'list_view_v2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/list_view_v2.png'
                ),
                'lp-list-view-compact' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/list_view_v3.png'
                ),
				'grid_view_classic' => array(
                    'alt' => 'Listing detail layout Classic',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid-classic.png'
                ),
            ),
            'default' => 'grid_view'
        ),
        array(
            'id' => 'lp_children_in_tax',
            'type' => 'button_set',
            'title' => __('Include child Terms listings in Archive Results', 'listingpro'),
            'desc' => __('Enable to show listings within child category when searching the main category.', 'listingpro'),
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'default' => 'yes',
        ),
        array(
            'id' => 'hide_grid_switcher',
            'type' => 'button_set',
            'title' => __('Hide Grid/List switcher', 'listingpro'),
            'desc' => __('Disable to show button to switch between grid and list view.', 'listingpro'),
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'default' => 'no',
            'required' => array('listing_style', '!=', '5'),
        ),
		array(
            'id' => 'listingpro_show_terms_description_switch',
            'type' => 'button_set',
            'title' => __('Enable if you want to show terms description on Archive Pages', 'listingpro'),
            'subtitle' => __('This option is to show terms (listing categories, location etc) description on their Archive Pages.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Listing Submit & Edit', 'listingpro'),
    'id' => 'listing_submit_settings',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        // Harry Code Starts from here
        array(
            'id' => 'listing_submit_page_style',
            'type' => 'select',
            'title' => __('Page Style', 'listingpro'),
            'subtitle' => __('Choose an option', 'listingpro'),
            'desc' => __('Choose submit listing form style.', 'listingpro'),
            'options' => array(
                'style1' => 'Page Style 1',
                'style2' => 'Page Style 2',
            ),
            'default' => 'style2',
        ),
        /* ----------------- */
        array(
            'id' => 'lp_listing_listing_by_google',
            'type' => 'button_set',
            'title' => esc_html__('Auto-Pilot Front-End submission via google (Fill-O-Bot) ', 'listingpro'),
            'desc' => __('Enable to import details from Google Maps.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'lp_listing_images_count_switch',
            'type' => 'button_set',
            'title' => esc_html__('Gallery Images Upload Limit', 'listingpro'),
            'desc' => esc_html__('Enable to restrict total number of images allowed to upload for a single listing. Individual pricing plan settings will be override this option.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'lp_listing_images_counter',
            'type' => 'text',
            'title' => esc_html__('Max. allowed images in gallery', 'listingpro'),
            'subtitle' => esc_html__('Just use integer value. e.g 5', 'listingpro'),
            'description' => esc_html__('Maximum no. of images allowed in gallery while submitting a listing', 'listingpro'),
            'default' => '5',
            'required' => array('lp_listing_images_count_switch', 'equals', 'yes'),
        ),
        array(
            'id' => 'lp_listing_images_size_switch',
            'type' => 'button_set',
            'title' => esc_html__('Gallery Images File Size (MB)', 'listingpro'),
            'desc' => esc_html__('Enable to restrict total images file size (mb) allowed to upload for a single listing. Individual pricing plan settings will be override this option.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'lp_listing_images_sizes',
            'type' => 'text',
            'title' => esc_html__('Total allowed images size in gallery', 'listingpro'),
            'subtitle' => esc_html__('Size is in Mbs, Just use integer value. e.g 5', 'listingpro'),
            'description' => esc_html__('Enter total image size in MB. This is the total for all images within the listing.', 'listingpro'),
            'default' => '5',
            'required' => array('lp_listing_images_size_switch', 'equals', 'yes'),
        ),
        /* ------------- */
        /* style 1 imgs */
        array(
            'id' => 'submit_ad_img_switch',
            'type' => 'switch',
            'title' => esc_html__('First section Image', 'listingpro'),
            'subtitle' => '',
            'default' => true,
            'required' => array('listing_submit_page_style', 'equals', 'style1'),
        ),
        array(
            'id' => 'submit_ad_img',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload Image for Submit Listing', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Submit Listing', 'listingpro'),
            'required' => array(
                array('submit_ad_img_switch', 'equals', '1'),
                array('listing_submit_page_style', 'equals', 'style1'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/submt.png'),
        ),
        array(
            'id' => 'submit_ad_img1_switch',
            'type' => 'switch',
            'title' => esc_html__('Second section Image ', 'listingpro'),
            'subtitle' => '',
            'required' => array('listing_submit_page_style', 'equals', 'style1'),
            'default' => true,
        ),
        array(
            'id' => 'submit_ad_img1',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload Image for second section', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for second section', 'listingpro'),
            'required' => array('submit_ad_img1_switch', 'equals', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/sbmt.png'),
        ),
        array(
            'id' => 'submit_ad_img2_switch',
            'type' => 'switch',
            'title' => esc_html__('Third section Image', 'listingpro'),
            'subtitle' => '',
            'required' => array('listing_submit_page_style', 'equals', 'style1'),
            'default' => true,
        ),
        array(
            'id' => 'submit_ad_img2',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload Image for third section', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for third section', 'listingpro'),
            'required' => array('submit_ad_img2_switch', 'equals', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/sbmt1.png'),
        ),
        array(
            'id' => 'submit_ad_img3_switch',
            'type' => 'switch',
            'title' => esc_html__('Fourth section Image', 'listingpro'),
            'subtitle' => '',
            'required' => array('listing_submit_page_style', 'equals', 'style1'),
            'default' => true,
        ),
        array(
            'id' => 'submit_ad_img3',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Upload Image for Fourth section', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Fourth section', 'listingpro'),
            'required' => array('submit_ad_img3_switch', 'equals', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/sbmt2.png'),
        ),
        array(
            'id' => 'business_logo_switch',
            'type' => 'switch',
            'title' => esc_html__('Business Logo', 'listingpro'),
            'desc' => esc_html__('Enable to allow upload of listing logo', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'business_logo_default',
            'type' => 'media',
            'title' => esc_html__('Default Logo', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Default logo if not uploaded by user', 'listingpro'),
            'required' => array('business_logo_switch', 'equals', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/claim1.png'),
        ),
        /* style 2 imgs */
        array(
            'id' => 'submit_ad_img_title',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Title Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Title Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/title.png'),
        ),
        array(
            'id' => 'submit_ad_img_faddress',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Full Address Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Full Address Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/contact.png'),
        ),
        array(
            'id' => 'submit_ad_img_city',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for City Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for City Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/contact.png'),
        ),
        array(
            'id' => 'submit_ad_img_phone',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Phone Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Phone Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/contact.png'),
        ),
        array(
            'id' => 'submit_ad_img_website',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Website Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Website Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/contact.png'),
        ),
        array(
            'id' => 'submit_ad_img_categories',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Category Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Category Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/cat-sub.png'),
        ),
        array(
            'id' => 'submit_ad_img_pricerange',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Price Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Price Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/price.png'),
        ),
        array(
            'id' => 'submit_ad_img_busincesshours',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Business Hours Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Business Hours Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/biz.png'),
        ),
        array(
            'id' => 'submit_ad_img_socialmedia',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Social Media Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Social Media Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/biz.png'),
        ),
        array(
            'id' => 'submit_ad_img_faq',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Faq\'s Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Faq\'s Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/faq.png'),
        ),
        array(
            'id' => 'submit_ad_img_desc',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Description Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Description Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/desc.png'),
        ),
        array(
            'id' => 'submit_ad_img_video',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Video Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Video Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/video.png'),
        ),
        array(
            'id' => 'submit_ad_img_gallery',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Gallery Tip', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Gallery Tip', 'listingpro'),
            'required' => array(
                array('listing_submit_page_style', 'equals', 'style2'),
            ),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/gallery.png'),
        ),
        array(
            'id' => 'submit_ad_img_b_logo',
            'type' => 'media',
            'url' => true,
            'title' => esc_html__('Image for Business Logo', 'listingpro'),
            'compiler' => 'true',
            'desc' => esc_html__('Upload Image for Business Logo', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/quick-tip/gallery.png'),
        ),

        /* end for tip style 2 */
        array(
            'id' => 'quick_tip_switch',
            'type' => 'switch',
            'title' => esc_html__('Quick Tips', 'listingpro'),
            'desc' => esc_html__('Enable To Show Quick Tips.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'quick_tip_title',
            'type' => 'text',
            'title' => esc_html__('Quick Tip Title', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('quick_tip_switch', 'equals', '1'),
            'default' => 'Quick Tip',
        ),
        array(
            'id' => 'quick_tip_text',
            'type' => 'textarea',
            'title' => esc_html__('Quick Text', 'listingpro'),
            'subtitle' => '',
            'required' => array('quick_tip_switch', 'equals', '1'),
            'default' => 'Add information about your business below. Your business page will not appear in search results until this information has been verified and approved by our moderators. Once it is approved, you will receive an email with instructions on how to claim your business page.'
        ),
        array(
            'id' => 'listing_title_text',
            'type' => 'text',
            'title' => esc_html__('Title Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'Listing Title',
        ),
        array(
            'id' => 'listing_city_text',
            'type' => 'text',
            'title' => esc_html__('Location Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'City',
        ),
        array(
            'id' => 'lp_listing_location_mode',
            'type' => 'select',
            'title' => __('Location Mode', 'listingpro'),
            'subtitle' => __('Choose an option', 'listingpro'),
            'desc' => __('Choose if multiple locations can be selected', 'listingpro'),
            'options' => array(
                'single' => 'Single',
                'multi' => 'Multi',
            ),
            'default' => 'single',
        ),
        array(
            'id' => 'lp_showhide_address',
            'type' => 'switch',
            'title' => esc_html__('Address', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_gadd_text',
            'type' => 'text',
            'title' => esc_html__('Google Address Button Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('lp_showhide_address', 'equals', '1'),
            'default' => 'Full Address',
        ),
        array(
            'id' => 'listing_custom_cordn',
            'type' => 'text',
            'title' => esc_html__('Custom Address Button Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('lp_showhide_address', 'equals', '1'),
            'default' => 'Add Custom Address',
        ),
        array(
            'id' => 'phone_switch',
            'type' => 'switch',
            'title' => esc_html__('Phone', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'lp_detail_page_whatsapp_button',
            'type' => 'button_set',
            'title' => esc_html__('Whatsapp', 'listingpro'),
            'subtitle' => esc_html__('on/off', 'listingpro'),
            'options' => array(
                'on' => 'On',
                'off' => 'Off',
            ),
            'default' => 'off',
        ),
        array(
            'id' => 'lp_whatsapp_label',
            'type' => 'text',
            'title' => esc_html__('Whatsapp Field Label', 'listingpro'),
            'subtitle' => esc_html__('Label for whatsapp field', 'listingpro'),
            'description' => esc_html__('It will show on listing submit and edit page', 'listingpro'),
            'default' => 'Whatsapp',
            'required' => array('lp_detail_page_whatsapp_button', 'equals', 'on'),
        ),
        array(
            'id' => 'listing_ph_text',
            'type' => 'text',
            'title' => esc_html__('Phone Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('phone_switch', 'equals', '1'),
            'default' => 'Phone',
        ),
        array(
            'id' => 'web_switch',
            'type' => 'switch',
            'title' => esc_html__('Website URL', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_web_text',
            'type' => 'text',
            'title' => esc_html__('Website Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('web_switch', 'equals', '1'),
            'default' => 'Website',
        ),
        array(
            'id' => 'oph_switch',
            'type' => 'switch',
            'title' => esc_html__('Hours', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        //            array(
        //                'id' => 'listing_oph_text',
        //                'type' => 'text',
        //                'title' => esc_html__('Operational Hours Text', 'listingpro'),
        //                'subtitle' => '',
        //                'description' => '',
        //                'required' => array('oph_switch', 'equals', '1'),
        //                'default' => 'Add Business Hours',
        //            ),
        array(
            'id' => 'lp_hours_slot2',
            'type' => 'button_set',
            'title' => esc_html__('2nd Time Slot', 'listingpro'),
            'required' => array('oph_switch', 'equals', '1'),
            'desc' => esc_html__('Enable to allow two Times slots per day.', 'listingpro'),
            'options' => array(
                'enable' => 'Enable',
                'disable' => 'Disable',
            ),
            'default' => 'disable',
        ),
        array(
            'id' => 'listing_cat_text',
            'type' => 'text',
            'title' => esc_html__('Category Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'Category',
        ),
        array(
            'id' => 'lp_listing_category_mode',
            'type' => 'select',
            'title' => __('Select Category Mode', 'listingpro'),
            'subtitle' => __('Choose an option', 'listingpro'),
            'desc' => __('Choose if multiple category can be selected.', 'listingpro'),
            'options' => array(
                'single' => 'Single',
                'multi' => 'Multi',
            ),
            'default' => 'single',
        ),
        array(
            'id' => 'listing_features_text',
            'type' => 'text',
            'title' => esc_html__('Features area Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => esc_html__('Select your listing features', 'listingpro'),
        ),
        array(
            'id' => 'currency_switch',
            'type' => 'switch',
            'title' => esc_html__('Price Range', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_curr_text',
            'type' => 'text',
            'title' => esc_html__('Price Range Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('currency_switch', 'equals', '1'),
            'default' => 'Price Range',
        ),
        array(
            'id' => 'digit_price_switch',
            'type' => 'switch',
            'title' => esc_html__('Price From', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_digit_text',
            'type' => 'text',
            'title' => esc_html__('Price From Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('digit_price_switch', 'equals', '1'),
            'default' => 'Price From',
        ),
        array(
            'id' => 'price_switch',
            'type' => 'switch',
            'title' => esc_html__('Price To', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_price_text',
            'type' => 'text',
            'title' => esc_html__('Price To Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('price_switch', 'equals', '1'),
            'default' => 'Price To',
        ),
        array(
            'id' => 'listing_desc_text',
            'type' => 'text',
            'title' => esc_html__('Description Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'Description',
        ),
        array(
            'id' => 'faq_switch',
            'type' => 'switch',
            'title' => esc_html__('FAQ', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_faq_text',
            'type' => 'text',
            'title' => esc_html__('FAQ Title', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('faq_switch', 'equals', '1'),
            'default' => 'FAQ',
        ),
        array(
            'id' => 'listing_faq_tabs_text',
            'type' => 'text',
            'title' => esc_html__('FAQ Tabs Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('faq_switch', 'equals', '1'),
            'default' => 'FAQ',
        ),
        array(
            'id' => 'listin_social_switch',
            'type' => 'switch',
            'title' => esc_html__('Social', 'listingpro'),
            'subtitle' => esc_html__('Hide or show all socials', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'tw_switch',
            'type' => 'switch',
            'title' => esc_html__('Twitter', 'listingpro'),
            'subtitle' => '',
            'required' => array('listin_social_switch', 'equals', '1'),
            'default' => true,
        ),
        array(
            'id' => 'fb_switch',
            'type' => 'switch',
            'title' => esc_html__('Facebook', 'listingpro'),
            'subtitle' => '',
            'required' => array('listin_social_switch', 'equals', '1'),
            'default' => true,
        ),
        array(
            'id' => 'lnk_switch',
            'type' => 'switch',
            'title' => esc_html__('LinkedIn', 'listingpro'),
            'subtitle' => '',
            'required' => array('listin_social_switch', 'equals', '1'),
            'default' => true,
        ),
        array(
            'id' => 'yt_switch',
            'type' => 'switch',
            'title' => esc_html__('Youtube', 'listingpro'),
            'subtitle' => '',
            'required' => array('listin_social_switch', 'equals', '1'),
            'default' => true,
        ),
        array(
            'id' => 'insta_switch',
            'type' => 'switch',
            'title' => esc_html__('Instagram', 'listingpro'),
            'subtitle' => '',
            'required' => array('listin_social_switch', 'equals', '1'),
            'default' => true,
        ),
        array(
            'id' => 'tags_switch',
            'type' => 'switch',
            'title' => esc_html__('Tags field', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_tags_text',
            'type' => 'text',
            'title' => esc_html__('Tags Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('tags_switch', 'equals', '1'),
            'default' => 'Tags or keywords (Comma separated)',
        ),
        array(
            'id' => 'vdo_switch',
            'type' => 'switch',
            'title' => esc_html__('Business video', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_vdo_text',
            'type' => 'text',
            'title' => esc_html__('Business video Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'required' => array('vdo_switch', 'equals', '1'),
            'default' => 'Your Business video',
        ),
        array(
            'id' => 'file_switch',
            'type' => 'switch',
            'title' => esc_html__('Image Uploading', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'lp_featured_file_switch',
            'type' => 'switch',
            'title' => esc_html__('Featured Image', 'listingpro'),
            'subtitle' => '',
            'default' => false,
        ),
        array(
            'id' => 'location_switch',
            'type' => 'switch',
            'title' => esc_html__('Show Location', 'listingpro'),
            'subtitle' => '',
            'default' => true,
        ),
        array(
            'id' => 'listing_username_text',
            'type' => 'text',
            'title' => esc_html__('User Name Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => esc_html__('Enter User Name ', 'listingpro'),
        ),
        array(
            'id' => 'listing_email_text',
            'type' => 'text',
            'title' => esc_html__('Email Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => esc_html__('Enter Your email', 'listingpro'),
        ),
        array(
            'id' => 'listing_btn_text',
            'type' => 'text',
            'title' => esc_html__('Submit Listing Button Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'Save & Preview',
        ),
        array(
            'id' => 'listing_edit_btn_text',
            'type' => 'text',
            'title' => esc_html__('Edit Listing Button Text', 'listingpro'),
            'subtitle' => '',
            'description' => '',
            'default' => 'Update & Preview',
        ),
        // Harry Code Ends from here
    )
));
/* **********************************************************************
		 * Locations
		 * **********************************************************************/
Redux::setSection(
    $opt_name,
    array(
        'title' => __('Location', 'listingpro'),
        'id' => 'listing_submit_edit_locations',
        'customizer_width' => '400px',
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'info_listing_location',
                'type' => 'info',
                'desc' => __('This section is for locations field in listing submission and edit. you can select either "Auto" or "Manual" locations option', 'listingpro')
            ),
            array(
                'id' => 'lp_listing_locations_options',
                'type' => 'select',
                'title' => __('Select Location Type', 'listingpro'),
                'subtitle' => __('Select option about locations', 'listingpro'),
                'desc' => __('Select if locations are added by admin or via Google Maps. If Google is selected all locations in the world can be selected unless restricted below.', 'listingpro'),
                'options' => array(
                    'manual_loc' => 'Add Locations by Admin Only',
                    'auto_loc' => 'Auto Locations by Google',
                ),
                'default' => 'manual_loc',
            ),
            array(
                'id' => 'lp_listing_locations_range',
                'type' => 'text',
                'title' => esc_html__('Add Location Code', 'listingpro'),
                'required' => array('lp_listing_locations_options', 'equals', 'auto_loc'),
                'subtitle' => esc_html__('Shortcode to restrict locations for specific country ', 'listingpro'),
                'description' => esc_html__('Edited location code to restrict locations to the select country. Codes can be viewed here', 'listingpro') . '<a href="http://www.fao.org/countryprofiles/iso3list/en/" target="_blank">' . ' ' . esc_html__('here', 'listingpro') . '</a>.',
                'default' => '',
            ),
            array(
                'id' => 'lp_listing_locations_field_options',
                'type' => 'select',
                'title' => __('Location Pattern', 'listingpro'),
                'required' => array('lp_listing_locations_options', 'equals', 'auto_loc'),
                'subtitle' => __('Select option about locations pattern', 'listingpro'),
                'desc' => __('locations option for pattern on search and listing submit and edit', 'listingpro'),
                'options' => array(
                    'with_region' => 'With Region',
                    'no_region' => 'Without Region',
                ),
                'default' => 'no_region',
            ),
        )
    )
);
/* **********************************************************************
		 * Reviews
		 * **********************************************************************/
Redux::setSection(
    $opt_name,
    array(
        'title' => __('Reviews', 'listingpro'),
        'id' => 'listing_review_submit_option',
        'customizer_width' => '400px',
        'subsection' => true,
        'fields' => array(
            array(
                'id' => 'info_post_review',
                'type' => 'info',
                'desc' => __('This section is for submit review option. You can on/off reviews submission. You can also allow user to submit their reviews on listing by either option', 'listingpro')
            ),
            array(
                'id' => 'lp_review_switch',
                'type' => 'switch',
                'title' => esc_html__('Review', 'listingpro'),
                'desc' => esc_html__('Enable to allow reviews within listings', 'listingpro'),
                'default' => true,
            ),
            array(
                'id' => 'lp_review_submit_options',
                'type' => 'select',
                'title' => __('Post Review', 'listingpro'),
                'required' => array('lp_review_switch', 'equals', '1'),
                'subtitle' => __('Select option about review submit', 'listingpro'),
                'desc' => __('Choose if the user must be logged in to leave reviews.', 'listingpro'),
                'options' => array(
                    'sign_in' => 'Only by logged in User',
                    'instant_sign_in' => 'Instant signup',
                ),
                'default' => 'instant_sign_in',
            ),
            array(
                'id' => 'lp_review_img_from_listing',
                'type' => 'button_set',
                'title' => esc_html__('Use Listing Image if No Image Uploaded with Review For Diplay in Activities Widget', 'listingpro'),
                'subtitle' => esc_html__('on/off', 'listingpro'),
                'options' => array(
                    'on' => 'on',
                    'off' => 'off',
                ),
                'default' => 'on',
            ),
            array(
                'id' => 'lp_review_placeholder',
                'type' => 'media',
                'url' => true,
                'title' => esc_html__('Upload Placeholder Image for Reviews', 'listingpro'),
                'compiler' => 'true',
                'desc' => esc_html__('In case if there are no images uploaded with reviews.', 'listingpro'),
                'default' => array('url' => get_template_directory_uri() . '/assets/images/default/placeholder.png'),
                'required' => array('lp_review_img_from_listing', 'equals', 'off'),
            ),
            array(
                'id' => 'lp_detail_page_review_report_button',
                'type' => 'button_set',
                'required' => array('lp_review_switch', 'equals', '1'),
                'title' => esc_html__('Detail Listing Page Review Report Button', 'listingpro'),
                'subtitle' => esc_html__('on/off', 'listingpro'),
                'options' => array(
                    'on' => 'on',
                    'off' => 'off',
                ),
                'default' => 'on',
            ),
            array(
                'id' => 'lp_multirating_switch',
                'type' => 'switch',
                'required' => array('lp_review_switch', 'equals', '1'),
                'title' => esc_html__('Multi Rating', 'listingpro'),
                'desc' => esc_html__('Enable to choose multipoint reviews', 'listingpro'),
                'default' => false,
            ),
            array(
                'id' => 'lp_multi_ratiing1_switch',
                'type' => 'switch',
                'required' => array('lp_multirating_switch', 'equals', '1'),
                'title' => esc_html__('Field 1', 'listingpro'),
                'subtitle' => '',
                'default' => true,
            ),
            array(
                'id' => 'lp_multi_ratiing1_label_switch',
                'type' => 'text',
                'required' => array('lp_multi_ratiing1_switch', 'equals', '1'),
                'title' => esc_html__('Label Field 1', 'listingpro'),
                'subtitle' => '',
                'default' => '',
            ),
            array(
                'id' => 'lp_multi_ratiing2_switch',
                'type' => 'switch',
                'required' => array('lp_multirating_switch', 'equals', '1'),
                'title' => esc_html__('Field 2', 'listingpro'),
                'subtitle' => '',
                'default' => true,
            ),
            array(
                'id' => 'lp_multi_ratiing2_label_switch',
                'type' => 'text',
                'required' => array('lp_multi_ratiing2_switch', 'equals', '1'),
                'title' => esc_html__('Label Field 2', 'listingpro'),
                'subtitle' => '',
                'default' => '',
            ),
            array(
                'id' => 'lp_multi_ratiing3_switch',
                'type' => 'switch',
                'required' => array('lp_multirating_switch', 'equals', '1'),
                'title' => esc_html__('Field 3', 'listingpro'),
                'subtitle' => '',
                'default' => true,
            ),
            array(
                'id' => 'lp_multi_ratiing3_label_switch',
                'type' => 'text',
                'required' => array('lp_multi_ratiing3_switch', 'equals', '1'),
                'title' => esc_html__('Label Field 3', 'listingpro'),
                'subtitle' => '',
                'default' => '',
            ),
            array(
                'id' => 'lp_multi_ratiing4_switch',
                'type' => 'switch',
                'required' => array('lp_multirating_switch', 'equals', '1'),
                'title' => esc_html__('Field 4', 'listingpro'),
                'subtitle' => '',
                'default' => true,
            ),
            array(
                'id' => 'lp_multi_ratiing4_label_switch',
                'type' => 'text',
                'required' => array('lp_multi_ratiing4_switch', 'equals', '1'),
                'title' => esc_html__('Label Field 4', 'listingpro'),
                'subtitle' => '',
                'default' => '',
            ),
            /* ------------- */
            array(
                'id' => 'lp_listing_reviews_images_count_switch',
                'type' => 'button_set',
                'title' => esc_html__('Gallery Images Counter', 'listingpro'),
                'options' => array(
                    'yes' => __('Yes', 'listingpro'),
                    'no' => __('No', 'listingpro'),
                ),
                'default' => 'no',
                'required' => array('lp_review_switch', 'equals', '1'),
            ),
            array(
                'id' => 'lp_listing_reviews_images_counter',
                'type' => 'text',
                'title' => esc_html__('Max. allowed images in gallery', 'listingpro'),
                'subtitle' => esc_html__('Just use integer value. e.g 5', 'listingpro'),
                'description' => esc_html__('Maximum no. of images allowed in gallery while submitting a review', 'listingpro'),
                'default' => '5',
                'required' => array(
                    array('lp_review_switch', 'equals', '1'),
                    array('lp_listing_reviews_images_count_switch', 'equals', 'yes'),
                ),
            ),
            array(
                'id' => 'lp_listing_reviews_images_size_switch',
                'type' => 'button_set',
                'title' => esc_html__('Gallery Images Size', 'listingpro'),
                'options' => array(
                    'yes' => __('Yes', 'listingpro'),
                    'no' => __('No', 'listingpro'),
                ),
                'default' => 'no',
                'required' => array('lp_review_switch', 'equals', '1'),
            ),
            array(
                'id' => 'lp_listing_reviews_images_sizes',
                'type' => 'text',
                'title' => esc_html__('Total allowed images size in gallery', 'listingpro'),
                'subtitle' => esc_html__('Size is in Mbs, Just use integer value. e.g 5', 'listingpro'),
                'description' => esc_html__('Allowed size of all images in gallery', 'listingpro'),
                'default' => '5',
                'required' => array(
                    array('lp_review_switch', 'equals', '1'),
                    array('lp_listing_reviews_images_size_switch', 'equals', 'yes'),
                ),
            ),
            /* ------------- */
            //review sorting
            array(
                'id' => 'lp_listing_reviews_orderby',
                'type' => 'button_set',
                'title' => esc_html__('Reviews Order Filter', 'listingpro'),
                'options' => array(
                    'on' => __('Enable', 'listingpro'),
                    'off' => __('Disable', 'listingpro'),
                ),
                'default' => 'off',
            ),
        )
    )
);
/* **********************************************************************
		 * Lead form
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Leads Form', 'listingpro'),
    'id' => 'listing_lead_form_option',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'info_lead form',
            'type' => 'info',
            'desc' => __('Show / Hide leads form from listing detail page', 'listingpro')
        ),
        array(
            'id' => 'lp_lead_form_switch',
            'type' => 'switch',
            'title' => esc_html__('Form', 'listingpro'),
            'desc' => esc_html__('Enable to show Lead From within listings', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'lp_lead_form_switch_claim',
            'type' => 'switch',
            'title' => esc_html__('Show lead form only on claimed listing', 'listingpro'),
            'subtitle' => esc_html__('on=show only on claimed, off= show on all listing', 'listingpro'),
            'desc' => esc_html__('Enable to only show lead form within claimed listings', 'listingpro'),
            'required' => array('lp_lead_form_switch', 'equals', '1'),
            'default' => false,
        ),
    )
));
/* **********************************************************************
		 * Claim ON/OFF
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Listing Claim', 'listingpro'),
    'id' => 'listing_listing_claim_option',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'info_listing_claim',
            'type' => 'info',
            'desc' => __('Show / Hide claim option for listing ', 'listingpro')
        ),
        array(
            'id' => 'lp_listing_claim_switch',
            'type' => 'switch',
            'title' => esc_html__('Claim', 'listingpro'),
            'desc' => esc_html__('Enable to allow claiming listings.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'lp_listing_form_top_image',
            'url' => true,
            'type' => 'media',
            'title' => esc_html__('Claim Form Top Image', 'listingpro'),
            'read-only' => false,
            'required' => array('lp_listing_claim_switch', '=', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/claimtop.png'),
            'subtitle' => esc_html__('Upload Claim Popup Top Image.', 'listingpro'),
        ),
        array(
            'id' => 'lp_listing_claim_image',
            'url' => true,
            'type' => 'media',
            'title' => esc_html__('Claim Popup Image', 'listingpro'),
            'read-only' => false,
            'required' => array('lp_listing_claim_switch', '=', '1'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/claim1.png'),
            'subtitle' => esc_html__('Upload Claim Popup Image.', 'listingpro'),
        ),
        array(
            'id' => 'lp_listing_claim_text_slider',
            'type' => 'slides',
            'title' => esc_html__('Claim Details', 'listingpro'),
            'subtitle' => esc_html__('put text here about claim', 'listingpro'),
            'desc' => esc_html__('You can add more than one text information', 'listingpro'),
            'required' => array('lp_listing_claim_switch', '=', '1'),
            'show' => array(
                'title' => true,
                'description' => true,
                'url' => false,
            ),
            'placeholder' => array(
                'title' => __('This is a title', 'listingpro'),
                'description' => __('Description Here', 'listingpro'),
                'url' => '',
            ),
        ),
    )
));
/* **********************************************************************
		 * Listing nearby
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Listing Nearby', 'listingpro'),
    'id' => 'listing_nearby_loc',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'info_listing_nearby',
            'type' => 'info',
            'desc' => __('Show nearby listings in listing detail page sidebar', 'listingpro')
        ),
        array(
            'id' => 'listingpro_nearby_dest',
            'type' => 'text',
            'title' => esc_html__('Add Distance for Nearby Location', 'listingpro'),
            'subtitle' => esc_html__('Enter only numeric values. Do not add distance units here', 'listingpro'),
            'description' => esc_html__('No units required.', 'listingpro') . '</a>',
            'default' => '100',
        ),
        array(
            'id' => 'listingpro_nearby_dest_in',
            'type' => 'select',
            'title' => __('Destination in ', 'listingpro'),
            'subtitle' => __('Nearby Destination Calculate in', 'listingpro'),
            'desc' => __('Select between Miles & Kilometers', 'listingpro'),
            'options' => array(
                'km' => 'Kilometers',
                'mil' => 'Miles',
            ),
            'default' => 'km',
        ),
        array(
            'id' => 'listingpro_nearby_filter',
            'type' => 'select',
            'title' => __('Filter By Listing Category ', 'listingpro'),
            'subtitle' => __('Show Nearby listing only from current category', 'listingpro'),
            'desc' => '',
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_nearby_noposts',
            'type' => 'text',
            'title' => esc_html__('No. of Nearby Listings', 'listingpro'),
            'subtitle' => esc_html__('Enter only numeric values', 'listingpro'),
            'description' => '' . '</a>',
            'default' => '5',
        ),
    )
));
/* **********************************************************************
		 * Listing nearby
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Google AdSense', 'listingpro'),
    'id' => 'listing_google_adnense_section',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'info_listing_googleads',
            'type' => 'info',
            'desc' => __('Paste Your Google AdSense code. Ads will show on listings detail page', 'listingpro')
        ),
        array(
            'id' => 'lp-gads-editor',
            'type' => 'editor',
            'title' => __('Google AdSense', 'listingpro'),
            'subtitle' => __('Google Ads on listings details page', 'listingpro'),
            'desc' => __('Adsense code for listings page. If not entered AdSense will not show', 'listingpro'),
            'default' => '',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10
            )
        ),
        /* for v2 */
        array(
            'id' => 'info_listing_archive_googleads',
            'type' => 'info',
            'desc' => __('Paste Your Google AdSense code. Ads will show on listings archive page', 'listingpro')
        ),
        array(
            'id' => 'lp-archive-gads-editor',
            'type' => 'editor',
            'title' => __('Google AdSense', 'listingpro'),
            'subtitle' => __('Google Ads on listings archive page', 'listingpro'),
            'desc' => __('Adsense code for archive pages. If not entered AdSense will not show', 'listingpro'),
            'default' => '',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10
            )
        ),
        /* end for v2 */
    )
));
/* **********************************************************************
         * Author archive settings
         * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Author Archive', 'listingpro'),
    'id' => 'author_general',
    'customizer_width' => '400px',
    'icon' => 'el el-list-alt',
    'fields' => array(
        array(
            'id' => 'author_banner',
            'url' => true,
            'type' => 'media',
            'title' => esc_html__('Banner Image', 'listingpro'),
            'read-only' => false,
            'default' => array('url' => get_template_directory_uri() . '/assets/images/home-banner.jpg'),
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('My Listings', 'listingpro'),
    'id' => 'author_listings',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'my_listings_author',
            'type' => 'switch',
            'title' => esc_html__('Listings', 'listingpro'),
            'desc' => 'Enable to show which listings are connected to the user.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'my_listing_per_page',
            'type' => 'text',
            'title' => __('Listings Per Page', 'listingpro'),
            'required' => array('my_listings_author', 'equals', '1'),
            'subtitle' => __('It will effect on taxonomy and search page.', 'listingpro'),
            'desc' => __('Set how many listings will show per page within the user’s profile.', 'listingpro'),
            'default' => '10',
        ),
        array(
            'id' => 'my_listing_views',
            'type' => 'image_select',
            'title' => esc_html__('Listing page layout', 'listingpro'),
            'desc' => esc_html__('Select how the listings will show within the user’s profile.', 'listingpro'),
            'required' => array('my_listings_author', 'equals', '1'),
            'options' => array(
                'grid_view' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid-view.png'
                ),
                'grid_view2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid3.png'
                ),
                'grid_view3' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid4.png'
                ),
                'grid_view_v2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/gird_view_v2.png'
                ),
                'list_view' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/listing-view.png'
                ),
                'list_view3' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/listview3.png'
                ),
                'list_view_v2' => array(
                    'alt' => 'Listing detail layout',
                    'img' => get_template_directory_uri() . '/assets/images/admin/list_view_v2.png'
                ),
				'grid_view_classic' => array(
                    'alt' => 'Listing detail layout Classic',
                    'img' => get_template_directory_uri() . '/assets/images/admin/grid-classic.png'
                ),
            ),
            'default' => 'grid_view'
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('About Me', 'listingpro'),
    'id' => 'author_about',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'about_me',
            'type' => 'switch',
            'title' => esc_html__('About Me', 'listingpro'),
            'desc' => 'Enable to show users information within their profile.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons',
            'type' => 'switch',
            'title' => esc_html__('Social Icons', 'listingpro'),
            'desc' => 'Enable to show users Social Media links within their profile.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons_fb',
            'type' => 'switch',
            'title' => esc_html__('Facebook', 'listingpro'),
            'default' => 1,
            'required' => array('about_me_social_icons', 'equals', '1'),
            'on' => esc_html__('Show', 'listingpro'),
            'off' => esc_html__('Hide', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons_tw',
            'type' => 'switch',
            'title' => esc_html__('Twitter', 'listingpro'),
            'default' => 1,
            'required' => array('about_me_social_icons', 'equals', '1'),
            'on' => esc_html__('Show', 'listingpro'),
            'off' => esc_html__('Hide', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons_linkedin',
            'type' => 'switch',
            'title' => esc_html__('LinkedIn', 'listingpro'),
            'default' => 1,
            'required' => array('about_me_social_icons', 'equals', '1'),
            'on' => esc_html__('Show', 'listingpro'),
            'off' => esc_html__('Hide', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons_inst',
            'type' => 'switch',
            'title' => esc_html__('Instagram', 'listingpro'),
            'default' => 1,
            'required' => array('about_me_social_icons', 'equals', '1'),
            'on' => esc_html__('Show', 'listingpro'),
            'off' => esc_html__('Hide', 'listingpro'),
        ),
        array(
            'id' => 'about_me_social_icons_pin',
            'type' => 'switch',
            'title' => esc_html__('Pinterest', 'listingpro'),
            'default' => 1,
            'required' => array('about_me_social_icons', 'equals', '1'),
            'on' => esc_html__('Show', 'listingpro'),
            'off' => esc_html__('Hide', 'listingpro'),
        ),
        array(
            'id' => 'about_activities',
            'type' => 'switch',
            'title' => esc_html__('Activities', 'listingpro'),
            'desc' => 'Enable to show users activities within their profile.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'about_activities_title',
            'type' => 'text',
            'title' => esc_html__('Heading', 'listingpro'),
            'default' => esc_html__('Activities', 'listingpro'),
            'required' => array('about_activities', 'equals', '1'),
        ),
    )
));
//        Redux::setSection( $opt_name, array(
//            'title'            => __( 'Reviews', 'listingpro' ),
//            'id'               => 'author_reviews',
//            'customizer_width' => '400px',
//            'subsection' => true,
//            'fields'     => array(
//                array(
//                    'id'       => 'reviews',
//                    'type'     => 'switch',
//                    'title'    => esc_html__( 'Enable / Disable Reviews', 'listingpro' ),
//                    'desc'     => '',
//                    'subtitle' => '',
//                    'default'  => 1,
//                    'on'       => esc_html__( 'Enabled', 'listingpro' ),
//                    'off'      => esc_html__( 'Disabled', 'listingpro' ),
//                ),
//                array(
//                    'id'       => 'my_reviews_style',
//                    'type'     => 'image_select',
//                    'title'    => esc_html__('Listing page layout', 'listingpro'),
//                    'subtitle' => '',
//                    'required' => array('reviews','equals','1'),
//                    'options'  => array(
//                        'style1'      => array(
//                            'alt'   => 'Reviews Style',
//                            'img'   => get_template_directory_uri().'/assets/images/admin/reviews1.png'
//                        ),
//                        'style2'      => array(
//                            'alt'   => 'Listing detail layout',
//                            'img'   => get_template_directory_uri().'/assets/images/admin/reviews2.png'
//                        ),
//                    ),
//                    'default' => 'style1'
//                ),
//            )
//        ) );
Redux::setSection($opt_name, array(
    'title' => __('Photos', 'listingpro'),
    'id' => 'author_photos',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'photos',
            'type' => 'switch',
            'title' => esc_html__('Photos', 'listingpro'),
            'desc' => 'Enable to show the users uploaded photos within their profile.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'my_photos_per_page',
            'type' => 'text',
            'title' => __('Images Per Page', 'listingpro'),
            'required' => array('photos', 'equals', '1'),
            'subtitle' => __('It will effect on taxonomy and search page.', 'listingpro'),
            'desc' => __('Set how many images will show per page within the user’s profile.', 'listingpro'),
            'default' => '10',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Contact', 'listingpro'),
    'id' => 'author_contact',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'contact',
            'type' => 'switch',
            'title' => esc_html__('Contact', 'listingpro'),
            'desc' => 'Enable to show users contact information.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Counters', 'listingpro'),
    'id' => 'author_countersss',
    'customizer_width' => '400px',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'author_counters',
            'type' => 'switch',
            'title' => esc_html__('Counters', 'listingpro'),
            'desc' => 'Enable to show a counter within the user’s profile.',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'counter_listing',
            'type' => 'switch',
            'title' => esc_html__('Listing Counter', 'listingpro'),
            'default' => 1,
            'desc' => 'Enable to show the total number of listings the user has connected to their account.',
            'required' => array('author_counters', 'equals', '1'),
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'counter_photos',
            'type' => 'switch',
            'title' => esc_html__('Photos Counter', 'listingpro'),
            'desc' => 'Enable to show the total number of images the user has uploaded.',
            'default' => 1,
            'required' => array('author_counters', 'equals', '1'),
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'counter_reviews',
            'type' => 'switch',
            'title' => esc_html__('Reviews Counter', 'listingpro'),
            'default' => 1,
            'desc' => 'Enable to show a total number of reviews left by the user.',
            'required' => array('author_counters', 'equals', '1'),
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'report_btn',
            'type' => 'switch',
            'title' => esc_html__('Report Button', 'listingpro'),
            'desc' => 'Enable to allow other users to report the user.',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
    )
));
/* **********************************************************************
		 * Pricing Plan Options
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => __('Pricing Plan', 'listingpro'),
    'id' => 'lp_pricing_plans',
    'customizer_width' => '400px',
    'icon' => 'el-icon-delicious',
    'fields' => array(
        /* plan for expired */
        array(
            'id' => 'lp_assignplanexpirybutton',
            'type' => 'button_set',
            'title' => esc_html__('Assign plan to expired listing', 'listingpro'),
            'desc' => esc_html__('Enable to change the listing plan after existing expires', 'listingpro'),
            'options' => array(
                'enable' => 'Enable',
                'disable' => 'Disable',
            ),
            'default' => 'disable',
        ),
        array(
            'id' => 'lp_plan_after_expire',
            'type' => 'select',
            'data' => 'posts',
            'required' => array(
                array('lp_assignplanexpirybutton', 'equals', 'enable')
            ),
            'args' => array('post_type' => array('price_plan'), 'posts_per_page' => -1),
            'title' => __('Assign plan to expired listing', 'listingpro'),
            'desc' => __('Select plan listings will switch to after expired.', 'listingpro'),
        ),
        /* end of expired */
        array(
            'id' => 'lp_listing_paid_claim_switch',
            'type' => 'button_set',
            'title' => esc_html__('Paid Listing Claim', 'listingpro'),
            'required' => array('lp_listing_claim_switch', '=', '1'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_plans_cats',
            'type' => 'button_set',
            'title' => __('Category Specific Plans', 'listingpro'),
            'subtitle' => __('Attach specific plans for specific category', 'listingpro'),
            'desc' => __('Enable to link Plans to selected Listing Categories. This will allow you to have different price plans for each category.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_month_year_plans',
            'type' => 'button_set',
            'title' => __('Monthly & Annual Based Price Plans', 'listingpro'),
            'subtitle' => __('Select Option in Pricing Plans Based on Monthly and Yearly based', 'listingpro'),
            'desc' => __('Enable this to set Price Plans to Monthly (30 days) or yearly (365 days) this will also allow you to have a different price such as discount if the user pays for a full year. <b>Change to Enable/Disable</b>', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_permonth_price_in_plan',
            'type' => 'button_set',
            'title' => __('Show Annual Price Breakdown as Per Month', 'listingpro'),
            'subtitle' => __('If you enable this option, Price will be display as per month in annual pricing plans', 'listingpro'),
            'desc' => __('Enable to show how much the total would be if paid monthly.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
    )
));
/* **********************************************************************
		 * Search filter Options
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Advanced Filter', 'listingpro'),
    'id' => 'search-filter-options',
    'desc' => '',
    'icon' => 'el-icon-filter',
    'fields' => array(
        array(
            'id' => 'info_search_filter',
            'type' => 'info',
            'desc' => __('This section is for search filters on archive page. Here you can show/hide your desired search filter option. You can also show/hide the search filter completely.', 'listingpro')
        ),
        array(
            'id' => 'enable_search_filter',
            'type' => 'switch',
            'title' => esc_html__('Search Filter', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_price_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Price', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_opentime_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Open Time', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_high_rated_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Highest Rated', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_most_reviewed_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Most Reviewed', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_most_viewed_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Most Viewed', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_best_changed_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Best Match', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_cats_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Categories', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'enable_nearme_search_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Near Me', 'listingpro'),
            'desc' => esc_html__('This option will be enabled only if your site has SSL enabled', 'listingpro'),
            'subtitle' => '',
            'default' => 1,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        //near me  and location
        array(
            'id' => 'disable_location_in_nearme_search_filter',
            'type' => 'switch',
            'required' => array(
                array('enable_search_filter', '=', '1'),
                array('enable_nearme_search_filter', '=', '1'),
            ),
            'title' => esc_html__('Location', 'listingpro'),
            'desc' => esc_html__('IF you enable this option, when near me filter is active, location will be excluded in all filter results', 'listingpro'),
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        //end near me  and location
        array(
            'id' => 'lp_nearme_filter_param',
            'type' => 'select',
            'title' => __('Location Distance unit', 'listingpro'),
            'required' => array('enable_nearme_search_filter', '=', '1'),
            'subtitle' => __('Nearme Destination Calculate in', 'listingpro'),
            'desc' => '',
            'options' => array(
                'km' => 'Kilometers',
                'mil' => 'Miles',
            ),
            'default' => 'km',
        ),
        /* array(
            'id'       => 'enable_readious_search_filter',
            'type'     => 'switch',
			'required' => array( 'enable_search_filter', '=', '1' ),
            'title'    => esc_html__( 'Radius Filter', 'listingpro' ),
            'desc'     => '',
            'subtitle' => '',
            'default'  => 1,
            'on'       => esc_html__( 'Enabled', 'listingpro' ),
            'off'      => esc_html__( 'Disabled', 'listingpro' ),
        ),*/
        array(
            'id' => 'enable_readious_search_filter_default',
            'type' => 'text',
            'required' => array('enable_nearme_search_filter', '=', '1'),
            'title' => esc_html__('Default Near Me Value', 'listingpro'),
            'subtitle' => '',
            'desc' => esc_html__('Set default value of near me', 'listingpro'),
            'default' => '105',
        ),
        array(
            'id' => 'enable_readious_search_filter_min',
            'type' => 'text',
            'required' => array('enable_nearme_search_filter', '=', '1'),
            'title' => esc_html__('Min Near Me Range', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'default' => '0',
        ),
        array(
            'id' => 'enable_readious_search_filter_max',
            'type' => 'text',
            'required' => array('enable_nearme_search_filter', '=', '1'),
            'title' => esc_html__('Max Near Me Range', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'default' => '1000',
        ),
        array(
            'id' => 'enable_extrafields_filter',
            'type' => 'switch',
            'required' => array('enable_search_filter', '=', '1'),
            'title' => esc_html__('Additional Filter options', 'listingpro'),
            'desc' => '',
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Search', 'listingpro'),
    'id' => 'lp_search_settings',
    'customizer_width' => '400px',
    'icon' => 'el el-search',
    'fields' => array(
        array(
            'id' => 'lp_what_field_switcher',
            'type' => 'switch',
            'title' => __('What Field', 'listingpro'),
            'desc' => __('Disable to show What Field within the search bar.', 'listingpro'),
            'subtitle' => __('on=hide  off=show', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'lp_what_field_algo',
            'type' => 'select',
            'required' => array('lp_what_field_switcher', '!=', '1'),
            'title' => __('Search mode for home banner search WHAT field', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            // Must provide key => value pairs for select options
            'options' => array(
                'titlematch' => 'Exact Match',
                'keyword' => 'Broad match',
            ),
            'default' => 'titlematch',
        ),
        // array(
        //     'id' => 'lp_default_search_by',
        //     'type' => 'select',
        //     'required' => array('lp_what_field_switcher', '!=', '1'),
        //     'title' => __('Default Search By', 'listingpro'),
        //     'subtitle' =>'',
        //     'desc' => __('If someone does not select any suggestion from "What" field in the search form, then this option will show its impact on search results.', 'listingpro'),
        //     // Must provide key => value pairs for select options
        //     'options' => array(
        //         'title' => 'Title',
        //         'keyword' => 'Keyword/tag',
        //     ),
        //     'default' => 'title',
        // ),
        array(
            'id' => 'lp_location_loc_switcher',
            'type' => 'switch',
            'title' => __('Where Field (Location)', 'listingpro'),
            'subtitle' => __('on=hide  off=show', 'listingpro'),
            'desc' => __('Disable to show Location Field within the search bar.', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'search_loc_option',
            'type' => 'select',
            'required' => array(
                array('lp_location_loc_switcher', '!=', '1'),
                array('lp_auto_current_locations_switch', "equals", '1'),
            ),
            'title' => __('Pre populated location in homepage search', 'listingpro'),
            'subtitle' => '',
            'desc' => __('This require auto location enable', 'listingpro'),
            // Must provide key => value pairs for select options
            'options' => array(
                'yes' => 'Yes',
                'no' => 'No',
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'home_search_loc_switcher',
            'type' => 'switch',
            'required' => array('lp_location_loc_switcher', '!=', '1'),
            'title' => __('Enable typing in homepage location search field', 'listingpro'),
            'subtitle' => '',
            'default' => false,
        ),
        array(
            'id' => 'inner_search_loc_switcher',
            'type' => 'switch',
            'required' => array('lp_location_loc_switcher', '!=', '1'),
            'title' => __('Enable typing in inner pages location search field', 'listingpro'),
            'subtitle' => '',
            'default' => false,
        ),
        array(
            'id' => 'lp_listing_search_locations_type',
            'type' => 'select',
            'title' => __('Select Location Search Type', 'listingpro'),
            'subtitle' => __('Select option about locations Search', 'listingpro'),
            'required' => array('lp_location_loc_switcher', '!=', '1'),
            'desc' => __('locations option for listing search in search form', 'listingpro'),
            'options' => array(
                'manual_loc' => 'Locations by Admin Only',
                'auto_loc' => 'Auto Locations by Google',
            ),
            'default' => 'manual_loc',
        ),
        array(
            'id' => 'lp_listing_search_locations_range',
            'type' => 'text',
            'title' => esc_html__('Add Location Shortcode', 'listingpro'),
            'required' => array('lp_listing_search_locations_type', 'equals', 'auto_loc'),
            'subtitle' => esc_html__('Shortcode to restrict locations for specific country ', 'listingpro'),
            'description' => esc_html__('For locations codes, visit here.', 'listingpro') . '<a href="http://www.fao.org/countryprofiles/iso3list/en/" target="_blank">' . ' ' . esc_html__('here', 'listingpro') . '</a>. You can add single code at a time or you can leave the field empty to activate globally (word-wide).',
            'default' => '',
        ),
        array(
            'id' => 'lp_exclude_listingtitle_switcher',
            'type' => 'switch',
            'title' => __('Exclude Listing Title From Suggestions in Search', 'listingpro'),
            'subtitle' => __('on=exclude,  off=include', 'listingpro'),
            'default' => false,
        ),
    )
));
/* **********************************************************************
		 * Payment setting
		 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Payment', 'listingpro'),
    'id' => 'payment-settings',
    'desc' => '',
    'icon' => 'el-icon-eur',
    'fields' => array(),
));
Redux::setSection($opt_name, array(
    'title' => esc_html__('General', 'listingpro'),
    'id' => 'payment-general',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'listings_admin_approved',
            'type' => 'select',
            'title' => esc_html__('Submitted Listing Require Approval', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Enable to require listings to be approved by an admin before published. If disabled listing will automatically publish after submission and payment made if applicable. Note: If paying via Bank Transfer listings will still require manual approval. ',
            'options' => array(
                'yes' => esc_html__('Yes', 'listingpro'),
                'no' => esc_html__('No', 'listingpro')
            ),
            'default' => 'yes',
        ),
        array(
            'id' => 'enable_paid_submission',
            'type' => 'select',
            'title' => esc_html__('Paid Submission', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Enable to sell listings.',
            'options' => array(
                'no' => esc_html__('No', 'listingpro'),
                'yes' => esc_html__('Yes', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'lp_enable_recurring_payment',
            'type' => 'select',
            'title' => esc_html__('Recurring Payment', 'listingpro'),
            'subtitle' => '',
            'required' => array('enable_paid_submission', '=', 'yes'),
            'desc' => 'Enable to allow recurring payment during checkout.',
            'options' => array(
                'no' => esc_html__('No', 'listingpro'),
                'yes' => esc_html__('Yes', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'lp_enable_auto_recurring_payment_switch',
            'type' => 'button_set',
            'required' => array('lp_enable_recurring_payment', '=', 'yes'),
            'title' => esc_html__('Auto Recurring Payment ', 'listingpro'),
            'subtitle' => '',
            'desc' => 'If this option is enabled auto recurring is enabled by default',
            'options' => array(
                'yes' => esc_html__('Yes', 'listingpro'),
                'no' => esc_html__('No', 'listingpro'),
            ),
            'default' => 'Yes',
        ),
        array(
            'id' => 'lp_recurring_notification_before',
            'type' => 'text',
            'required' => array('lp_enable_recurring_payment', '=', 'yes'),
            'title' => esc_html__('Notify User before Expiry', 'listingpro'),
            'subtitle' => 'Enter No. Of days. Please add only digital value. e.g 2/4/5/9 etc',
            'desc' => 'Enter the number of days the Listing author is notified before expiry.',
            'default' => '2',
        ),
        array(
            'id' => 'per_listing_expire',
            'type' => 'text',
            'required' => array('per_listing_expire_unlimited', '=', '1'),
            'title' => esc_html__('Number of Expire Days', 'listingpro'),
            'subtitle' => 'No of days until a listings will expire. Starts from the moment the listing is published on the website',
            'desc' => '',
            'default' => '30',
        ),
        array(
            'id' => 'currency_paid_submission',
            'type' => 'select',
            'title' => esc_html__('Currency For Paid Submission', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Select the currency users will be charged.',
            'options' => array(
                'USD' => 'USD',
                'EUR' => 'EUR',
                'BDT' => 'BDT',
                'EGP' => 'EGP',
                'AUD' => 'AUD',
                'AED' => 'AED',
                'BRL' => 'BRL',
                'CAD' => 'CAD',
                'CHF' => 'CHF',
                'CZK' => 'CZK',
                'DKK' => 'DKK',
                'HKD' => 'HKD',
                'HUF' => 'HUF',
                'IDR' => 'IDR',
                'ILS' => 'ILS',
                'INR' => 'INR',
                'JPY' => 'JPY',
                'KOR' => 'KOR',
                'KSH' => 'KSH',
                'MYR' => 'MYR',
                'MXN' => 'MXN',
                'NGN' => 'NGN',
                'NOK' => 'NOK',
                'NZD' => 'NZD',
                'PHP' => 'PHP',
                'PLN' => 'PLN',
                'RUB' => 'RUB',
                'GBP' => 'GBP',
                'GHS' => 'GHS',
                'SGD' => 'SGD',
                'SEK' => 'SEK',
                'TWD' => 'TWD',
                'TTD' => 'TTD',
                'THB' => 'THB',
                'TRY' => 'TRY',
                'VND' => 'VND',
                'ZAR' => 'ZAR'
            ),
            'default' => 'USD',
            'validate' => 'not_empty',
        ),
        array(
            'id' => 'pricingplan_currency_position',
            'type' => 'select',
            'title' => esc_html__('Currency Position for pricing plan', 'listingpro'),
            'subtitle' => esc_html__('Symbol of currency to left or right side of price in pricing plans', 'listingpro'),
            'desc' => esc_html__('Select the position for the currency symbol.', 'listingpro'),
            'options' => array(
                'left' => 'Left',
                'right' => 'Right',
            ),
            'default' => 'left',
        ),
		array(
            'id' => 'listingpro_scastripe_switch',
            'type' => 'button_set',
            'title' => __('SCA Stripe Integration', 'listingpro'),
            'subtitle' => __('Enable if you want to setup Stripe new SCA integration', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'payment_terms_condition',
            'type' => 'select',
            'data' => 'pages',
            'title' => esc_html__('Terms & Conditions Page', 'listingpro'),
            'subtitle' => esc_html__('Select terms & conditions page', 'listingpro'),
            'desc' => 'Select page used for Terms & Conditions.',
            'default' => '',
        ),
        array(
            'id' => 'payment-checkout',
            'type' => 'select',
            'data' => 'pages',
            'title' => esc_html__('Checkout Page', 'listingpro'),
            'subtitle' => esc_html__('Select checkout page', 'listingpro'),
            'desc' => 'Select page used for checkout. The page requires Checkout Shortcode.',
            'default' => '',
        ),
        array(
            'id' => 'payment_fail',
            'type' => 'select',
            'data' => 'pages',
            'title' => __('Failed Payment page - after failed payment', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'desc' => __('Select page used for Failed Payment. Page requires Failed Payment Shortcode.', 'listingpro'),
            'default' => '',
        ),
        array(
            'id' => 'payment_success',
            'type' => 'select',
            'data' => 'pages',
            'title' => __('Thank you page - after successful payment', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'desc' => __('Select page used for Successful Payment. Page requires Successful Payment Shortcode.', 'listingpro'),
            'default' => '',
        ),
        array(
            'id' => 'listingpro_coupons_switch',
            'type' => 'button_set',
            'title' => __('Coupons', 'listingpro'),
            'subtitle' => __('Enable disable on checkout page', 'listingpro'),
            'desc' => __('Enable to allow coupons during checkout.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_invoice_start_switch',
            'type' => 'button_set',
            'title' => __('Set Invoice Start', 'listingpro'),
            'subtitle' => __('Enable disable for Custom Invoice Start No.', 'listingpro'),
            'desc' => __('Enable to set custom invoice numbering.', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_invoiceno_no_start',
            'type' => 'text',
            'required' => array('listingpro_invoice_start_switch', '=', 'yes'),
            'title' => esc_html__('Invoice Start Number', 'listingpro'),
            'subtitle' => __('Set you own start no. of Invoice for payment', 'listingpro'),
            'desc' => 'Enter next Invoice number.',
            'default' => '000000001',
        ),
        array(
            'id' => 'listingpro_invoiceno_no_start_ad',
            'type' => 'text',
            'required' => array('listingpro_invoice_start_switch', '=', 'yes'),
            'title' => esc_html__('Ads Invoice Start Number', 'listingpro'),
            'subtitle' => __('Set you own start no. of Invoice for payment of Ads Campaign', 'listingpro'),
            'desc' => 'Enter next Invoice number.',
            'default' => '000000001',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => esc_html__('Paypal', 'listingpro'),
    'id' => 'mem-paypal-settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'enable_paypal',
            'type' => 'switch',
            'title' => esc_html__('Paypal', 'listingpro'),
            //'required' => array( 'enable_paid_submission', '!=', 'no' ),
            'desc' => 'Enable to allow PayPal Payments',
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'paypal_api',
            'type' => 'select',
            'required' => array('enable_paypal', '=', '1'),
            'title' => esc_html__('Api Type', 'listingpro'),
            'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'listingpro'),
            'desc' => esc_html__('Select between Live and Sandbox. Sandbox is used for testing', 'listingpro'),
            'options' => array(
                'sandbox' => 'Sandbox',
                'live' => 'Live',
            ),
            'default' => 'sandbox',
        ),
        array(
            'id' => 'paypal_api_username',
            'type' => 'text',
            'required' => array('enable_paypal', '=', '1'),
            'title' => esc_html__('Paypal API Username', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Sign up here <br> https://developer.paypal.com/docs/api/payments/v1/',
            'default' => '',
        ),
        array(
            'id' => 'paypal_api_password',
            'type' => 'text',
            'required' => array('enable_paypal', '=', '1'),
            'title' => esc_html__('Paypal API Password', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Sign up here <br> https://developer.paypal.com/docs/api/payments/v1/',
            'default' => '',
        ),
        array(
            'id' => 'paypal_api_signature',
            'type' => 'text',
            'required' => array('enable_paypal', '=', '1'),
            'title' => esc_html__('Paypal API Signature', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Sign up here <br> https://developer.paypal.com/docs/api/payments/v1/',
            'default' => '',
        ),
        array(
            'id' => 'paypal_receiving_email',
            'type' => 'text',
            'required' => array('enable_paypal', '=', '1'),
            'title' => esc_html__('Paypal Receiving Email', 'listingpro'),
            'subtitle' => '',
            'desc' => '',
            'default' => '',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => esc_html__('Stripe', 'listingpro'),
    'id' => 'mem-stripe-settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'enable_stripe',
            'type' => 'switch',
            'title' => esc_html__('Stripe', 'listingpro'),
            //'required' => array( 'enable_paid_submission', '!=', 'no' ),
            'desc' => 'Enable to allow Stripe Payment',
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'stripe_api',
            'type' => 'select',
            'required' => array('enable_stripe', '=', '1'),
            'title' => esc_html__('Api Type', 'listingpro'),
            'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'listingpro'),
            'desc' => esc_html__('Select between Live and Sandbox. Sandbox is used for testing ', 'listingpro'),
            'options' => array(
                'sandbox' => 'Sandbox',
                'live' => 'Live',
            ),
            'default' => 'sandbox',
        ),
        array(
            'id' => 'stripe_secrit_key',
            'type' => 'text',
            'required' => array('enable_stripe', '=', '1'),
            'title' => esc_html__('Secret Key', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Get your key here <br> https://stripe.com/docs/api/tokens/create_card',
            'default' => '',
        ),
        array(
            'id' => 'stripe_pubishable_key',
            'type' => 'text',
            'required' => array('enable_stripe', '=', '1'),
            'title' => esc_html__('Publishable Key', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Get your key here <br> https://stripe.com/docs/api/tokens/create_card',
            'default' => '',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => esc_html__('Direct Payment / Wire Payment', 'listingpro'),
    'id' => 'mem-wire-payment',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'enable_wireTransfer',
            'type' => 'switch',
            'title' => esc_html__('Bank Transfer', 'listingpro'),
            //'required' => array( 'enable_paid_submission', '!=', 'no' ),
            'desc' => 'Enable to allow Bank Transfer Payment',
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => 'direct_payment_instruction',
            'type' => 'editor',
            'required' => array('enable_wireTransfer', '=', '1'),
            'title' => esc_html__('Instructions for Bank Transfer', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Enter instruction for Bank Transfer. This will show during checkout.',
            'default' => '
					<div>Your full name and mailing address</div>
					<div>Your Santander Bank account number</div>
					<div>SWIFT Code - SVRNUS33 (International only)</div>
					<div>Santander Bank routing number</div>
			',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
    )
));
/* 2 - checkout */
Redux::setSection($opt_name, array(
    'title' => esc_html__('2Checkout', 'listingpro'),
    'id' => 'mem-2checkout-settings',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'enable_2checkout',
            'type' => 'switch',
            'title' => esc_html__('2Checkout', 'listingpro'),
            'desc' => 'Enable to allow 2Checkout Payment',
            'subtitle' => '',
            'default' => 0,
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
        ),
        array(
            'id' => '2checkout_api',
            'type' => 'select',
            'required' => array('enable_2checkout', '=', '1'),
            'title' => esc_html__('API Type', 'listingpro'),
            'subtitle' => esc_html__('Sandbox = test API. LIVE = real payments API', 'listingpro'),
            'desc' => esc_html__('Select between Live and Sandbox. Sandbox is used for testing ', 'listingpro'),
            'options' => array(
                'sandbox' => 'Sandbox',
                'live' => 'Live',
            ),
            'default' => 'sandbox',
        ),
        array(
            'id' => '2checkout_acount_number',
            'type' => 'text',
            'required' => array('enable_2checkout', '=', '1'),
            'title' => esc_html__('Account ID', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Sign up Here <br> https://www.2checkout.com/',
            'default' => '',
        ),
        array(
            'id' => '2checkout_pubishable_key',
            'type' => 'text',
            'required' => array('enable_2checkout', '=', '1'),
            'title' => esc_html__('Publishable Key', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Get Your Key Here <br> https://knowledgecenter.2checkout.com/',
            'default' => '',
        ),
        array(
            'id' => '2checkout_private_key',
            'type' => 'text',
            'required' => array('enable_2checkout', '=', '1'),
            'title' => esc_html__('Private Key', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Get Your Key Here <br> https://knowledgecenter.2checkout.com/',
            'default' => '',
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => esc_html__('Tax', 'listingpro'),
    'id' => 'lp_tax_setting',
    'desc' => '',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'lp_tax_swtich',
            'type' => 'switch',
            'title' => esc_html__('Taxes', 'listingpro'),
            //'required' => array( 'enable_paid_submission', '!=', 'no' ),
            'desc' => 'Enable to require Taxes',
            'subtitle' => '',
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
            'default' => 0,
        ),
        array(
            'id' => 'lp_tax_label',
            'type' => 'text',
            'required' => array('lp_tax_swtich', '=', '1'),
            'title' => esc_html__('Tax Title', 'listingpro'),
            'subtitle' => '',
            'desc' => 'Enter name for taxes such as VAT/State Fees',
            'default' => esc_html__('Value-Added Tax', 'listingpro'),
        ),
        array(
            'id' => 'lp_tax_amount',
            'type' => 'text',
            'required' => array('lp_tax_swtich', '=', '1'),
            'title' => esc_html__('Tax Rate', 'listingpro'),
            'subtitle' => esc_html__('Add rate without % sign', 'listingpro'),
            'desc' => esc_html__('Enter rate without percentage sign', 'listingpro'),
            'validate_callback' => 'lp_option_taxrate_validate_callback',
            'default' => '5',
        ),
        array(
            'id' => 'lp_tax_with_plan_swtich',
            'type' => 'switch',
            'title' => esc_html__('Include Taxes with Pricing Plans', 'listingpro'),
            'required' => array('lp_tax_swtich', '=', '1'),
            'desc' => 'Enable to include Tax in Price Plans Total Price',
            'subtitle' => '',
            'on' => esc_html__('Enabled', 'listingpro'),
            'off' => esc_html__('Disabled', 'listingpro'),
            'default' => 0,
        ),
    )
));
$adminMail = get_option('admin_email');
$blogName = get_option('blogname');
/* **********************************************************************
 * Email Management
 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Email Management', 'listingpro'),
    'id' => 'listingpro-email-management',
    'desc' => '',
    'icon' => 'el-icon-envelope el-icon-small',
    'fields' => array(
        /* ===================================Email General Setting======================================== */
        array(
            'id' => 'listingpro-general-listing-email-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">General Email Settings</span>', 'listingpro'), $allowed_html_array),
            'subtitle' => esc_html__('Set email address and email sender name here', 'listingpro'),
            'desc' => ''
        ),
        array(
            'id' => 'listingpro_general_email_address',
            'type' => 'text',
            'title' => esc_html__('Email Address', 'listingpro'),
            'subtitle' => esc_html__('Email address for general email sending', 'listingpro'),
            'desc' => 'Enter the email address all emails will be sent from.',
            'default' => $adminMail,
        ),
        array(
            'id' => 'listingpro_general_email_from',
            'type' => 'text',
            'title' => esc_html__('Email From', 'listingpro'),
            'subtitle' => esc_html__('Email sender name for general email sending', 'listingpro'),
            'desc' => 'Enter the name all emails will be sent from.',
            'default' => $blogName,
        ),
        /* General Switch */
        array(
            'id' => 'general-emails-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Global Shortcodes</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Shotcodes for every Email Template . %website_name as Website Name, %website_url as Website URL, %user_name as User Name', 'listingpro')
        ),
        /* ===================================New User Registration======================================== */
        array(
            'id' => 'email-new-user-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">New Registered User</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for user registertaion email. %user_login_register as username, %user_pass_register as user password, %user_email_register as new user email', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_new_user_register',
            'type' => 'text',
            'title' => esc_html__('Subject for New User Notification', 'listingpro'),
            'subtitle' => esc_html__('Email subject for new user notification', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your username and password on %website_url', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_new_user_register',
            'type' => 'editor',
            'title' => esc_html__('Content for New User Notification', 'listingpro'),
            'subtitle' => esc_html__('Email content for new user notification', 'listingpro'),
            'desc' => '',
            'default' => 'Hi there,
									Welcome to %website_url! You can login now using the below credentials:
									Username:%user_login_register
									Password: %user_pass_register
									If you have any problems, please contact us.
									Thank you!',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_admin_new_user_register',
            'type' => 'text',
            'title' => esc_html__('Subject for New User Admin Notification', 'listingpro'),
            'subtitle' => esc_html__('Email subject for new user admin notification', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('New User Registration', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_admin_new_user_register',
            'type' => 'editor',
            'title' => esc_html__('Content for New User Admin Notification', 'listingpro'),
            'subtitle' => esc_html__('Email content for new user admin notification', 'listingpro'),
            'desc' => '',
            'default' => 'New user registration on %website_url.
									Username: %user_login_register,
									E-mail: %user_email_register',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* ==================================New Listing Submit======================================= */
        array(
            'id' => 'listingpro-new-listing-submit-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Submit Listing</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for listing submission email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_new_submit_listing',
            'type' => 'text',
            'title' => esc_html__('New Listing Submission Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject', 'listingpro'),
            // 'required' => array(
            //     array('lp_listing_general_switch', '=', 'yes'),
            //     array('lp_listing_general_switch', '=', 'yes'),
            // ),
            'desc' => '',
            'default' => esc_html__('Your listing has been submitted', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_new_submit_listing_content',
            'type' => 'editor',
            'title' => esc_html__('New Listing Submission Content', 'listingpro'),
            'subtitle' => esc_html__('Email content', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Other Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>45 B Road NY. USA</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_new_submit_listing_admin',
            'type' => 'text',
            'title' => esc_html__('New Listing Submission Subject(for admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('New listing has been submitted', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_new_submit_listing_content_admin',
            'type' => 'editor',
            'title' => esc_html__('New Listing Submission Content(for admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Other Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>45 B Road NY. USA</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* new code by zaheer on 15march */
        /* =====================================Purchased Activated==================================== */
        array(
            'id' => 'email-purchase-activated-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Purchase Activated</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for purchased activated email. %listing_title as Listing title, %listing_url as Listing URL, %plan_title as Plan Title, %invoice_no as Invoice Number', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_purchase_activated',
            'type' => 'text',
            'title' => esc_html__('Purchase Activated Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for purchase activated', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your purchase has  activated', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_purchase_activated',
            'type' => 'editor',
            'title' => esc_html__('Purchase Activated Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for Purchase Activated', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;"><a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">Your purchase has been successful on  <a href="%website_url">%website_name</a></p>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Plan Name:</strong>%plan_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Plan Price:</strong>%plan_price</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Submitted:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Payment Method:</strong>%payment_method</p>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Other Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>45 B Road NY. USA</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_purchase_activated_admin',
            'type' => 'text',
            'title' => esc_html__('Purchase Activated Subject(for admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject for purchase activated', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('A purchased has been made', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_purchase_activated_admin',
            'type' => 'editor',
            'title' => esc_html__('Purchase Activated Content(for admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;"><a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">Payment has been made successful on  <a href="%website_url">%website_name</a></p>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Plan Name:</strong>%plan_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Plan Price:</strong>%plan_price</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Submitted:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Payment Method:</strong>%payment_method</p>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Other Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>45 B Road NY. USA</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Listing Review Reply==================================== */
        array(
            'id' => 'email-reviewreply-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Review Reply</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for review reply email. %listing_title as Listing title, %listing_url as Listing URL, %sender_name as Sender Name, %reply as Review Reply ', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_listing_rev_reply',
            'type' => 'text',
            'title' => esc_html__('Review Reply Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for Review Reply', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Review Reply', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_msg_listing_rev_reply',
            'type' => 'editor',
            'title' => esc_html__('Review Reply Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for Review Reply', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
			You have received a reply on your review.<br> Reply : %review_reply_text
			</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Listing Approved==================================== */
        array(
            'id' => 'email-approved-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Approved Listing</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Listing Approve email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_listing_approved',
            'type' => 'text',
            'title' => esc_html__('Listing Approved Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for approved listing', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your listing approved', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_listing_approved',
            'type' => 'editor',
            'title' => esc_html__('Listing Approved Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for listing approved', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on %website_url </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Order  Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>P-11, Paradise Floor, Sadiq Trade Center</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Listing Expired==================================== */
        array(
            'id' => 'email-expired-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Expired Listing</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Listing Expired email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_listing_expired',
            'type' => 'text',
            'title' => esc_html__('Listing Expired Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for expired listing', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your listing expired', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_listing_expired',
            'type' => 'editor',
            'title' => esc_html__('Listing Expired Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for listing expired', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;"><a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a><div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;"><p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p><h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2><div style="padding: 30px 0px 15px 0px;"><h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3><p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p><p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p></div><div style="padding: 15px 0px 30px 0px;"><h3 style="margin: 0px 0px 5px; font-size: 16px;">Order  Details:</h3><p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>P-11, Paradise Floor, Sadiq Trade Center</p></div></div></div>',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Ads Expired==================================== */
        array(
            'id' => 'email-expired-info-ads',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('<span class="font24">Expired Ad Campaign</span>', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Ad Campaign Expired email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_ads_expired',
            'type' => 'text',
            'title' => esc_html__('Ad Expired Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for expired ads campaign', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your ad campaign has expired', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_ad_campaign_expired',
            'type' => 'editor',
            'title' => esc_html__('Ad Expired Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for ads campaigns expired', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Order  Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>P-11, Paradise Floor, Sadiq Trade Center</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Invoice Email==================================== */
        array(
            'id' => 'email-wire-invoice-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Wire Invoice', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Wire Invoice email. %listing_title as Listing title, %listing_url as Listing URL, %invoice_no as invoice number', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_wire_invoice',
            'type' => 'text',
            'title' => esc_html__('Invoice Email Subject', 'listingpro'),
            // 'required' => array(
            //     array('lp_listing_general_switch', '=', 'yes'),
            //     array('lp_listing_general_switch', '=', 'yes'),
            // ),
            'subtitle' => esc_html__('Email subject wire invoice', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your new listing on %website_url', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_wire_invoice',
            'type' => 'editor',
            'title' => esc_html__('Invoice Email Content', 'listingpro'),
            'subtitle' => esc_html__('Email content', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Order  Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>P-11, Paradise Floor, Sadiq Trade Center</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_wire_invoice_admin',
            'type' => 'text',
            'title' => esc_html__('Invoice Email Subject(admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject(admin)', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your new listing on %website_url', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_wire_invoice_admin',
            'type' => 'editor',
            'title' => esc_html__('Invoice Email Content (admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content(admin)', 'listingpro'),
            'desc' => '',
            'default' => '<div style="width: 100%; background: #f0f1f3; padding: 50px 0px;">
<a style="width: 45%; margin: 0 auto; text-align: center; display: block; padding-bottom: 25px; padding-left: 30px; padding-right: 30px;"><img alt="image" src="images/logo.png" /></a>
<div style="width: 45%; background: #fff; padding: 50px 30px; margin: 0 auto;">
<p style="margin: 0px;">New  Listing has been submitted on <a href="%website_url" >%website_name</a> </p>
<h2 style="color: #2a6ad4; margin: 0px; font-size: 20px;">%listing_title</h2>
<div style="padding: 30px 0px 15px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Details are Following:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing Name:</strong>%listing_title</p>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Listing URL:</strong>%listing_url</p>
</div>
<div style="padding: 15px 0px 30px 0px;">
<h3 style="margin: 0px 0px 5px; font-size: 16px;">Order  Details:</h3>
<p style="margin: 0px; font-size: 14px;"><strong style="padding-right: 10px;">Full Address:</strong>P-11, Paradise Floor, Sadiq Trade Center</p>
</div>
</div>
</div>',
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Listing Claim Email On Submission==================================== */
        array(
            'id' => 'email-listing-claim-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Claim Listing ( submission )', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Claim email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_listing_claimer',
            'type' => 'text',
            'title' => esc_html__('Listing Claim Submission Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for claimer', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your claim has submitted', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_listing_claimer',
            'type' => 'editor',
            'title' => esc_html__('Listing Claim Submission Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for claimer', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your Claim on listing <a href="%listing_url">%listing_title</a> has been submitted', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_listing_author',
            'type' => 'text',
            'title' => esc_html__('Listing Claim Submission Subject (Author)', 'listingpro'),
            'subtitle' => esc_html__('Email subject for Author', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('A claim has been submitted on your listing', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_listing_author',
            'type' => 'editor',
            'title' => esc_html__('Listing Claim Submission Content (Author)', 'listingpro'),
            'subtitle' => esc_html__('Email content for Author', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Hi, A claim has been submitted on your listing <a href="%listing_url">%listing_title</a>. Please contact admin for further details', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_listing_claim_admin',
            'type' => 'text',
            'title' => esc_html__('Listing Claim Submission Subject (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject for Admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('New Claim has been submitted', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_listing_claim_admin',
            'type' => 'editor',
            'title' => esc_html__('Listing Claim Submission Content (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content for Admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('You have received a new claim on a listing <a href="%listing_url">%listing_title</a> Please login on dashboard for more details', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Listing Claim Email On Approval==================================== */
        array(
            'id' => 'email-listing-claim-aproval-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Claim Listing ( Approval )', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Claim Approval email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_listing_claim_approve',
            'type' => 'text',
            'title' => esc_html__('Listing Claim Approval Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject(claimer)', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your claim has approved', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_listing_claim_approve',
            'type' => 'editor',
            'title' => esc_html__('Listing Claim Approval Content', 'listingpro'),
            'subtitle' => esc_html__('Email content(claimer)', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your Claim on listing <a href="%listing_url">%listing_title</a> has been approved', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_listing_claim_approve_old_owner',
            'type' => 'text',
            'title' => esc_html__('Listing Claim Approval Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject(old owner)', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Listing Claim notice', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_listing_claim_approve_old_owner',
            'type' => 'editor',
            'title' => esc_html__('Listing Claim Approval Content', 'listingpro'),
            'subtitle' => esc_html__('Email content(old owner)', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Claim against your listing has been has been approved.Details are : <a href="%listing_url">%listing_title</a>', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Campaign active email for admin==================================== */
        array(
            'id' => 'lp-campaign-active-email-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Campaign Activation( ADMIN)', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Campaign Activation email. %listing_title as Listing title, %listing_url as Listing URL, %campaign_packages as Packages Purchased, %author_name as Author name', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_campaign_activate',
            'type' => 'text',
            'title' => esc_html__('Ad Campaign Active Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject campaign activation', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Ad Campaign Activated', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_campaign_activate',
            'type' => 'editor',
            'title' => esc_html__('Ad Campaign Active Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for Admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('%author_name just activated an ad campaign for a listing <a href="%listing_url">%listing_title</a> with packages %campaign_packages', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Campaign active email for Author==================================== */
        array(
            'id' => 'lp-campaign-active-email-info-author',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Campaign Activation(To Author)', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Author Campaign Activation email. %listing_title as Listing title, %listing_url as Listing URL, %campaign_packages as Packages Purchased', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_campaign_activate_author',
            'type' => 'text',
            'title' => esc_html__('Ad Campaign Active Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject campaign activation', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Ad Campaign Activated', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_campaign_activate_author',
            'type' => 'editor',
            'title' => esc_html__('Ad Campaign Active Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for Admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('You have activated a campaign on a listing <a href="%listing_url">%listing_title</a>  With packages %campaign_packages ', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================recurring payment reminder option==================================== */
        array(
            'id' => 'lp-recurring-payment-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Recurring Payment Reminder Email', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Recurring Payment Notification email. you can use %listing_title as listing title, %plan_title for Plan name,  %plan_price as Plan Price, %plan_duration as Plan Duration and %notifybefore as no. of  day/days before payment deduction. Use shortcodes only in editor', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_recurring_payment',
            'type' => 'text',
            'title' => esc_html__('Recurring Payment Reminder Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for recurring reminder', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Recurring Payment Reminder', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_recurring_payment',
            'type' => 'editor',
            'title' => esc_html__('Recurring Payment Reminder Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for recurring reminder', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('A Payment will be deduct from your card after %notifybefore day/days. Details are: Listing: %listing_title, Plan: %plan_title, Price: %plan_price, Duration: %plan_duration day/days   ', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* for admin */
        array(
            'id' => 'listingpro_subject_recurring_payment_admin',
            'type' => 'text',
            'title' => esc_html__('Recurring Payment Reminder Subject (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject by admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Recurring Payment is due', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_recurring_payment_admin',
            'type' => 'editor',
            'title' => esc_html__('Recurring Payment Reminder Content (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content by admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('A Payment is due by a user after %notifybefore day/days. Details are: Listing: %listing_title, Plan: %plan_title, Price: %plan_price, Duration: %plan_duration day/days   ', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================recurring subscription cancel email==================================== */
        array(
            'id' => 'lp-subscription-cancel-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Subscription Cancel Email', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for cancel recurring submission  email. %listing_title as Listing title, %listing_url as Listing URL', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_cancel_subscription',
            'type' => 'text',
            'title' => esc_html__('Subscription Cancelled Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for subscription cancel', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Cancel Subscription Notification', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_cancel_subscription',
            'type' => 'editor',
            'title' => esc_html__('Subscription Cancelled Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for cancel subscription', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Your subscription has been canceled successfully', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* for admin */
        array(
            'id' => 'listingpro_subject_cancel_subscription_admin',
            'type' => 'text',
            'title' => esc_html__('Subscription Cancelled Subject (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email subject by admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Subscription cancel notification', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_cancel_subscription_admin',
            'type' => 'editor',
            'title' => esc_html__('Subscription Cancelled Content (Admin)', 'listingpro'),
            'subtitle' => esc_html__('Email content by admin', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('A subscription has been cancelled.', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Lead form email template==================================== */
        array(
            'id' => 'lp-lead-form-email-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Lead Form Email Template', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Lead form email. %listing_title as listing title, %sender_name for sender name, %sender_email for sender email, %sender_phone for sender phone and %sender_message for sender message', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_lead_form',
            'type' => 'text',
            'title' => esc_html__('Lead Form Subject', 'listingpro'),
            'subtitle' => esc_html__('Email subject for lead form', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Somone contacted for a listing', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_lead_form',
            'type' => 'editor',
            'title' => esc_html__('Lead Form Content', 'listingpro'),
            'subtitle' => esc_html__('Email content for lead form', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Someone has contacted you for the listing "%listing_title". Details are following<br>Name: %sender_name<br>Email: %sender_email<br>Phone:%sender_phone<br>Message: %sender_message', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        /* =====================================Reviews email template==================================== */
        array(
            'id' => 'lp-review-form-email-info',
            'type' => 'info',
            'notice' => false,
            'style' => 'info',
            'title' => wp_kses(__('Reviews Email Templates', 'listingpro'), $allowed_html_array),
            'desc' => esc_html__('Use these shortcodes only for Review email. %listing_title as listing title, %listing_url as listing link, %reviewtext as Review Message, and %reviewer_email for Reviewer Email in content', 'listingpro')
        ),
        array(
            'id' => 'listingpro_subject_review_author',
            'type' => 'text',
            'title' => esc_html__('Review Submission Subject (Author)', 'listingpro'),
            'subtitle' => esc_html__('Enter Subject of Email for Listing Author', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Review Submit Email', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_review_author',
            'type' => 'editor',
            'title' => esc_html__('Review Submission Content (Author)', 'listingpro'),
            'subtitle' => esc_html__('Email content for Listing Author', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Someone has reviewed on the listing "%listing_title" which is yours. Details are following<br>Reviewer: %reviewer_email<br>Review: %reviewtext<br>Listing:%listing_title<br>URL: %listing_url', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
        array(
            'id' => 'listingpro_subject_reviewer',
            'type' => 'text',
            'title' => esc_html__('Review Submission Subject(Reviewer)', 'listingpro'),
            'subtitle' => esc_html__('Enter Subject of Email for Reviewer', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('Review Submit Email', 'listingpro'),
        ),
        array(
            'id' => 'listingpro_content_reviewer',
            'type' => 'editor',
            'title' => esc_html__('Review Submission content(Reviewer)', 'listingpro'),
            'subtitle' => esc_html__('Email content for Reviewer', 'listingpro'),
            'desc' => '',
            'default' => esc_html__('You have reviewed on the listing "%listing_title". Details are following<br>Listing:%listing_title<br>URL: %listing_url', 'listingpro'),
            'args' => array(
                'teeny' => true,
                'textarea_rows' => 10,
                'wpautop' => false
            )
        ),
    ),
));
/* **********************************************************************
        * privacy settings
        * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Privacy', 'listingpro'),
    'id' => 'privacy-settings',
    'desc' => '',
    'icon' => 'el-icon-eye-open',
    'fields' => array(
        array(
            'id' => 'payment_terms_condition',
            'type' => 'select',
            'data' => 'pages',
            'title' => esc_html__('Privacy Page', 'listingpro'),
            //                'subtitle' => esc_html__('Select terms & conditions page', 'listingpro'),
            'desc' => 'Select Page containing Privacy Policy',
            'default' => '',
        ),
        array(
            'id' => 'listingpro_privacy_register',
            'type' => 'button_set',
            'title' => __('Signup', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for User Signup', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy during using Signup', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'yes',
        ),
        array(
            'id' => 'listingpro_privacy_listing',
            'type' => 'button_set',
            'title' => __('Listing Submission', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for listing submission', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy during Listing Submission', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'yes',
        ),
        array(
            'id' => 'listingpro_privacy_review',
            'type' => 'button_set',
            'title' => __('Review Submission', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for review submission', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy during Review Submission', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_privacy_leadform',
            'type' => 'button_set',
            'title' => __('Lead Form', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for lead form on listing detail page', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy on Lead Forms', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_privacy_claimform',
            'type' => 'button_set',
            'title' => __('Listing Claim Form', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for listing claim form', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy during Listing Claim Submission', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listingpro_privacy_contactform',
            'type' => 'button_set',
            'title' => __('Contact Form', 'listingpro'),
            //                'subtitle' => __('Privacy policy option for contact form', 'listingpro'),
            'desc' => __('Show Agree to the Privacy Policy on Contact Page Form', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
    ),
));
/* **********************************************************************
        * Invoices
        * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Invoice', 'listingpro'),
    'id' => 'listing-invoice',
    'desc' => '',
    'icon' => 'el-icon-list-alt',
    'fields' => array(
        array(
            'id' => 'invoice_logo',
            'url' => true,
            'type' => 'media',
            'title' => esc_html__('Company Logo', 'listingpro'),
            'read-only' => false,
            'default' => array('url' => get_template_directory_uri() . '/assets/images/flogo.png'),
            'subtitle' => esc_html__('Upload company logo for invoices.', 'listingpro'),
        ),
        array(
            'id' => 'invoice_company_name',
            'type' => 'text',
            'title' => esc_html__('Company Name', 'listingpro'),
            'default' => 'Company Name',
            'subtitle' => esc_html__('Enter company full name', 'listingpro'),
        ),
        array(
            'id' => 'invoice_address',
            'type' => 'editor',
            'title' => esc_html__('Company Address', 'listingpro'),
            'default' => '1161 Washingtown Avenue 299<br> Miami Beach 33141 FL',
            'subtitle' => esc_html__('Enter company full address', 'listingpro'),
            'args' => array(
                'teeny' => false,
                'textarea_rows' => 10,
                'wpautop' => false,
            )
        ),
        array(
            'id' => 'invoice_phone',
            'type' => 'text',
            'title' => esc_html__('Company Phone', 'listingpro'),
            'default' => '(987)654 3210',
            'subtitle' => '',
        ),
        array(
            'id' => 'invoice_additional_info',
            'type' => 'editor',
            'title' => esc_html__('Additional Info', 'listingpro'),
            'default' => '<p>The lorem ipsum text is typically a scrambled section of De finibus bonorum et malorum, a 1st-century BC Latin text by Cicero, with words altered, added, and removed to make it nonsensical, improper Latin.[citation needed]</p>',
            'subtitle' => 'This information is only for Wire invoices.'
        ),
        array(
            'id' => 'invoice_thankyou',
            'type' => 'text',
            'title' => esc_html__('Thank You text', 'listingpro'),
            'default' => 'Thank you for your business with us.',
            'subtitle' => 'This information is only for Wire invoices.',
        ),
    ),
));
/* **********************************************************************
         * Ads Options
         * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Ads', 'listingpro'),
    'id' => 'listing-ads',
    'desc' => '',
    'icon' => 'el-icon-screen',
    'fields' => array(
        array(
            'id' => 'listingpro_ads_campaign_style',
            'type' => 'button_set',
            'title' => __('Campaign type', 'listingpro'),
            'subtitle' => __('Select type of campaign', 'listingpro'),
            'desc' => __('Select Type of Ad Campaign Used', 'listingpro'),
            'options' => array(
                'adsperduration' => __('Pay Per Day (PPD)', 'listingpro'),
                'adsperclick' => __('Pay Per Click (PPC)', 'listingpro'),
            ),
            'default' => 'adsperduration',
        ),
        array(
            'id' => 'lp_pro_title_offer',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Promotion Title', 'listingpro'),
            'read-only' => false,
            'default' => 'Black Friday 50%',
        ),
        array(
            'id' => 'lp_random_ads_switch',
            'type' => 'switch',
            'title' => __('Spotlight Ads', 'listingpro'),
            'desc' => __('Enable Spotlight Ads. Spotlight Ads show in the Listing Posts Element', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'lp_random_ads',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Spotlight Price (PPD)', 'listingpro'),
            'desc'  => 'Price Per Day for Spotlight Ad. No Currency symbol required',
            'read-only' => false,
            'required' => array(
                array('lp_random_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperduration'),
            ),
            'default' => '10',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include any currency sign ).', 'listingpro'),
        ),
        array(
            'id' => 'lp_random_ads_pc',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Spotlight Price (PPC)', 'listingpro'),
            'desc'  => 'Price Per Click for Spotlight Ad. No Currency symbol required',
            'read-only' => false,
            'required' => array(
                array('lp_random_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperclick'),
            ),
            'default' => '10',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include any currency sign ).', 'listingpro'),
        ),
        array(
            'id' => 'lp_detail_page_ads_switch',
            'type' => 'switch',
            'title' => __('Side of Listing Ads', 'listingpro'),
            'desc' => __('Enable Side of Listing Ads. Side of Listing ads requires Widget within Listing', 'listingpro'),
            'default' => 1,
        ),
        array(
            'id' => 'lp_detail_page_ads',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Side of Listing (PPD)', 'listingpro'),
            'desc'  => 'Price Per Day for Side of Listing Ad. No Currency symbol required',
            'read-only' => false,
            'required' => array(
                array('lp_detail_page_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperduration'),
            ),
            'default' => '20',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include currency sign ).', 'listingpro'),
        ),
        array(
            'id' => 'lp_detail_page_ads_pc',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Side of Listing (PPC)', 'listingpro'),
            'desc'  => 'Price Per Click for Side of Listing Ad. No Currency symbol required',
            'read-only' => false,
            'required' => array(
                array('lp_detail_page_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperclick'),
            ),
            'default' => '20',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include currency sign ).', 'listingpro'),
        ),
        array(
            'id' => 'lp_top_in_search_page_ads_switch',
            'type' => 'switch',
            'title' => __('Top of Search Ads', 'listingpro'),
            'desc'  => 'Enable Top of Search Ads. Top of Search Ads show above all other Listings within Archive/Category Pages',
            'default' => 1,
        ),
        array(
            'id' => 'lp_top_in_search_page_ads',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Top of Search Ads Price (PPD)', 'listingpro'),
            'read-only' => false,
            'desc'  => 'Price per Day for Top of Search Ad. No Currency symbol required',
            'required' => array(
                array('lp_top_in_search_page_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperduration'),
            ),
            'default' => '50',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include currency sign ).', 'listingpro'),
        ),
        array(
            'id' => 'lp_top_in_search_page_ads_pc',
            'url' => true,
            'type' => 'text',
            'title' => esc_html__('Top of Search Price (PPC)', 'listingpro'),
            'read-only' => false,
            'desc'  =>  'Price per Click for Top of Search Ad. No Currency symbol required',
            'required' => array(
                array('lp_top_in_search_page_ads_switch', 'equals', '1'),
                array('listingpro_ads_campaign_style', 'equals', 'adsperclick'),
            ),
            'default' => '50',
            'subtitle' => esc_html__('Enter the amount you want to charge business owners to promote their listings for this format and placement( Do not include currency sign ).', 'listingpro'),
        ),

    ),
));
/* **********************************************************************
	 * Captcha Settings
	 * **********************************************************************/
Redux::setSection($opt_name, array(
    'title' => esc_html__('Form reCaptcha', 'listingpro'),
    'id' => 'listing-captcha',
    'desc' => '',
    'icon' => 'el-icon-lock',
    'fields' => array(
        array(
            'id' => 'lp_recaptcha_switch',
            'type' => 'switch',
            'title' => __('Google reCAPTCHA v3', 'listingpro'),
            'desc' => __('Enable to display Google reCAPTCHA and Load Script', 'listingpro'),
            'default' => 0,
        ),
        array(
            'id' => 'lp_recaptcha_site_key',
            'type' => 'text',
            'title' => __('Google reCAPTCHA v3 Site Key', 'listingpro'),
            'desc' => __('Create Key <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>', 'listingpro'),
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
            'subtitle' => __('Site Key For Google Recaptcha', 'listingpro'),
            'default' => '',
        ),
        array(
            'id' => 'lp_recaptcha_secret_key',
            'type' => 'text',
            'title' => __('Google reCAPTCHA v3 Secret Key', 'listingpro'),
            'desc' => __('Create Key <a href="https://www.google.com/recaptcha/admin" target="_blank">here</a>', 'listingpro'),
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
            'subtitle' => __('Secret Key For Google Recaptcha', 'listingpro'),
            'default' => '',
        ),
        array(
            'id' => 'lp_recaptcha_registration',
            'type' => 'switch',
            'title' => __('User registration', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA during User Registration', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_login',
            'type' => 'switch',
            'title' => __('User login', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA during User Login', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_listing_submission',
            'type' => 'switch',
            'title' => __('Listing Submission', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA during Listing Submission', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_listing_edit',
            'type' => 'switch',
            'title' => __('Listing Edit', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA during Edit Listing', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_lead',
            'type' => 'switch',
            'title' => __('Lead Form', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA on Listing Lead Form', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_reviews',
            'type' => 'switch',
            'title' => __('Review Form', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA on Listing Review Submission', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_contact',
            'type' => 'switch',
            'title' => __('Contact Page Form', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA on Contact Page Form', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
        array(
            'id' => 'lp_recaptcha_userprofile',
            'type' => 'switch',
            'title' => __('User Profile', 'listingpro'),
            'desc' => __('Show Google reCAPTCHA v3 on user profile', 'listingpro'),
            'default' => 0,
            'required' => array('lp_recaptcha_switch', 'equals', '1'),
        ),
    ),
));


// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('URL Config', 'listingpro'),
    'id' => 'URL settings',
    'customizer_width' => '400px',
    'icon' => 'el el-link',
    'fields' => array(
        array(
            'id' => 'lp_info_warning',
            'type' => 'info',
            'title' => __('URL Rewrite', 'listingpro'),
            'style' => 'warning',
            'desc' => __('Please update permalinks ( under Settings menu ) after any changes you make in following slugs ( to avoid a 404 error page )', 'listingpro')
        ),
        array(
            'id' => 'listingpro_recreate_pages_switch',
            'type' => 'button_set',
            'title' => __('Enable if you want to recreate Pages', 'listingpro'),
            'subtitle' => __('This option is to recreate pages after deleting', 'listingpro'),
            'options' => array(
                'yes' => __('Yes', 'listingpro'),
                'no' => __('No', 'listingpro'),
            ),
            'default' => 'no',
        ),
        array(
            'id' => 'listing_slug',
            'type' => 'text',
            'title' => __('listing slug', 'listingpro'),
            'subtitle' => __('Default is "listing"', 'listingpro'),
            'default' => 'listing',
        ),
        array(
            'id' => 'listing_cat_slug',
            'type' => 'text',
            'title' => __('listing category slug', 'listingpro'),
            'subtitle' => __('Default is "listing-category"', 'listingpro'),
            'default' => 'listing-category',
        ),
        array(
            'id' => 'listing_loc_slug',
            'type' => 'text',
            'title' => __('Location slug', 'listingpro'),
            'subtitle' => __('Default is "location"', 'listingpro'),
            'default' => 'location',
        ),
        array(
            'id' => 'listing_features_slug',
            'type' => 'text',
            'title' => __('Features slug', 'listingpro'),
            'subtitle' => __('Default is "features"', 'listingpro'),
            'default' => 'features',
        ),
        array(
            'id' => 'events_slug',
            'type' => 'text',
            'title' => __('Events slug', 'listingpro'),
            'subtitle' => __('Default is "events"', 'listingpro'),
            'default' => 'events',
        ),
        array(
            'id' => 'listing-author',
            'type' => 'text',
            'title' => __('Frontend Dashboard Page', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'validate' => 'url',
            'desc'      =>  'Select the page used for the Frontend Dashboard (Page must include the Dashboard Shortcode)',
            'default' => ''
        ),
        array(
            'id' => 'submit-listing',
            'type' => 'text',
            'title' => __('Submit Listing Page', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'desc' => __('Select the page used for Listing Submission (Page must include the Submit Listing Shortcode)', 'listingpro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'edit-listing',
            'type' => 'text',
            'title' => __('Edit Listing Page', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'desc' => __('Select the page used for Editing Listings (Page must include the Edit Listing Shortcode)', 'listingpro'),
            'validate' => 'url',
            'default' => ''
        ),
        array(
            'id' => 'pricing-plan',
            'type' => 'text',
            'title' => __('Price plans Page', 'listingpro'),
            'subtitle' => __('This must be an URL.', 'listingpro'),
            'desc' => __('Select the page used for Price Plans (Page must include the Price Plan Shortcode)', 'listingpro'),
            'validate' => 'url',
            'default' => ''
        ),
    )
));

// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Contact Page', 'listingpro'),
    'desc' => __('Translate all text strings into your own language', 'listingpro'),
    'id' => 'contact_page',
    'customizer_width' => '400px',
    'icon' => 'el el-phone'
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Contact Information', 'listingpro'),
    'desc' => __('Enable Contact Information to show on the Contact Page', 'listingpro'),
    'id' => 'contact_page_information',
    'customizer_width' => '400px',
    'icon' => 'el el-home',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'cp-show-contact-details',
            'type' => 'switch',
            'title' => __("Contact information", 'listingpro'),
            'subtitle' => __('ON=SHOW, OFF= HIDE', 'listingpro'),
            'desc'      =>    'Enable Contact Information to show on the Contact Page',
            'default' => 1
        ),
        array(
            'id' => 'Address',
            'type' => 'text',
            'title' => __("Title for contact information", 'listingpro'),
            'subtitle' => '',
            'default' => 'Address',
            'required' => array('cp-show-contact-details', 'equals', '1')
        ),
        array(
            'id' => 'cp-address',
            'type' => 'text',
            'title' => __("Address", 'listingpro'),
            'subtitle' => '',
            'default' => ' Your Address at Lutaco Tower 007A Nguyen Van Troi',
            'required' => array('cp-show-contact-details', 'equals', '1')
        ),
        array(
            'id' => 'cp-number',
            'type' => 'text',
            'title' => __("Phone", 'listingpro'),
            'subtitle' => '',
            'default' => '+008 1234 6789',
            'required' => array('cp-show-contact-details', 'equals', '1')
        ),
        array(
            'id' => 'cp-email',
            'type' => 'text',
            'title' => __("Email", 'listingpro'),
            'subtitle' => '',
            'default' => 'xyz@example.com',
            'required' => array('cp-show-contact-details', 'equals', '1'),
        ),
        array(
            'id' => 'cp-social-links',
            'type' => 'switch',
            'title' => __("Social Links", 'listingpro'),
            'desc' => __('Enable Social Media Links to show', 'listingpro'),
            'default' => 1,
            'required' => array('cp-show-contact-details', 'equals', '1'),
        ),
        array(
            'id' => 'fb_co',
            'type' => 'text',
            'title' => __("Facebook URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'tw_co',
            'type' => 'text',
            'title' => __("Twitter URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'insta_co',
            'type' => 'text',
            'title' => __("Instagram URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'tumb_co',
            'type' => 'text',
            'title' => __("Tumbler URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'f_yout_co',
            'type' => 'text',
            'title' => __("Youtube URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'f_linked_co',
            'type' => 'text',
            'title' => __("LinkedIn URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'f_pintereset_co',
            'type' => 'text',
            'title' => __("Pinterest URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
        array(
            'id' => 'f_vk_co',
            'type' => 'text',
            'title' => __("VK URL", 'listingpro'),
            'desc' => __('Enter full URL including HTTPS://', 'listingpro'),
            'default' => '',
            'required' => array('cp-social-links', 'equals', '1'),
        ),
    )
));
Redux::setSection($opt_name, array(
    'title' => __('Form', 'listingpro'),
    'desc' => __('Add or edit Form settings', 'listingpro'),
    'id' => 'contact_page_form',
    'customizer_width' => '400px',
    'icon' => 'el el-caret-up',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'contact_form_settings',
            'type' => 'select',
            'title' => __('Contact Form Provider', 'listingpro'),
            'desc' => __('Enter the Shortcode for your preferred form. (be sure to install wpforms lite.)', 'listingpro'),
            'options' => array(
                'cubewp_forms' => 'CubeWP Forms',
                'wpforms_lite' => 'WpForms Lite',
				'listingpro' => 'ListingPro',
            ),
            'default' => 'listingpro',
        ),
        array(
            'id' => 'form-title',
            'type' => 'text',
            'title' => __("Header for From", 'listingpro'),
            'subtitle' => '',
            'default' => 'Contact us'
        ),
        array(
            'id' => 'cp-success-message',
            'type' => 'textArea',
            'title' => __("Successful Submission Message", 'listingpro'),
            'subtitle' => '',
            'default' => 'Your message was sent successfully! I will be in touch as soon as I can.',
            'required' => array('contact_form_settings', 'equals', 'listingpro'),
        ),
        array(
            'id' => 'cp-failed-message',
            'type' => 'textArea',
            'title' => __("Failed Submission Message", 'listingpro'),
            'subtitle' => '',
            'default' => 'Something went wrong, try refreshing and submitting the form again.',
            'required' => array('contact_form_settings', 'equals', 'listingpro'),
        ),
        array(
            'id' => 'wpform_lite_shortcode',
            'type' => 'text',
            'title' => __("Shortcode for WPForms Lite", 'listingpro'),
            'desc' => __('Enter the Shortcode for your preferred form.', 'listingpro'),
            'required' => array('contact_form_settings', 'equals', 'wpforms_lite'),
        ),
        array(
            'id' => 'cubewp_forms_shortcode',
            'type' => 'text',
            'title' => __("Shortcode for CubeWP Forms", 'listingpro'),
            'desc' => __('Enter the Shortcode for your preferred form.', 'listingpro'),
            'required' => array('contact_form_settings', 'equals', 'cubewp_forms'),
        ),
    )
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Contact Map', 'listingpro'),
    'desc' => __('Set Latitude and longitude for contact page map', 'listingpro'),
    'id' => 'contact_page_map',
    'customizer_width' => '400px',
    'icon' => 'el el-home',
    'subsection' => true,
    'fields' => array(
        array(
            'id' => 'contact_page_map_switch',
            'type' => 'switch',
            'title' => __('Contact Page Map', 'listingpro'),
            'desc' => __('Enable to show Map', 'listingpro'),
            'default' => true,
        ),
        array(
            'id' => 'cp-lat',
            'type' => 'text',
            'title' => __("Latitude", 'listingpro'),
            'subtitle' => '',
            'required' => array('contact_page_map_switch', 'equals', '1'),
            'default' => '51.516576'
        ),
        array(
            'id' => 'cp-lan',
            'type' => 'text',
            'title' => __("Longitude", 'listingpro'),
            'subtitle' => '',
            'required' => array('contact_page_map_switch', 'equals', '1'),
            'default' => '-0.137508'
        ),
    )
));
// -> START Basic Fields
Redux::setSection($opt_name, array(
    'title' => __('Footer', 'listingpro'),
    'desc' => __('Add or edit Footer information', 'listingpro'),
    'id' => 'footer_section_information',
    'customizer_width' => '400px',
    'icon' => 'el el-home',
    //'subsection' => true,
    'fields' => array(
        array(
            'id' => 'footer_style',
            'type' => 'image_select',
            'title' => esc_html__('Footer Layout', 'listingpro'),
            'subtitle' => esc_html__('Select Footer layout', 'listingpro'),
            'desc' => esc_html__('Select from 1 of 11 Layout Styles.', 'listingpro'),
            'options' => array(
                'footer1' => array(
                    'alt' => 'footer 1',
                    'img' => get_template_directory_uri() . '/assets/images/new/fot1.png'
                ),
                'footer2' => array(
                    'alt' => 'footer 2',
                    'img' => get_template_directory_uri() . '/assets/images/new/fot2.png'
                ),
                'footer3' => array(
                    'alt' => 'footer 3',
                    'img' => get_template_directory_uri() . '/assets/images/admin/fot3.jpg'
                ),
                // Start new footer layouts view
                'footer4' => array(
                    'alt' => 'footer 4',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-4.jpg'
                ),
                'footer5' => array(
                    'alt' => 'footer 5',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-5.jpg'
                ),
                'footer6' => array(
                    'alt' => 'footer 6',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-6.jpg'
                ),
                'footer7' => array(
                    'alt' => 'footer 7',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-7.jpg'
                ),
                'footer8' => array(
                    'alt' => 'footer 8',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-9.jpg'
                ),
                'footer9' => array(
                    'alt' => 'footer 9',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-10.jpg'
                ),
                'footer10' => array(
                    'alt' => 'footer 10',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-11.jpg'
                ),
                'footer11' => array(
                    'alt' => 'footer 11',
                    'img' => get_template_directory_uri() . '/assets/images/admin/footer-12.jpg'
                ),
				'footer_classic' => array(
                    'alt' => 'Classic Footer',
                    'img' => get_template_directory_uri() . '/assets/images/new/classic-footer.png'
                ),
                // End new footer layouts view
            ),
            'default' => 'footer1'
        ),
        array(
            'id' => 'footer_layout',
            'type' => 'select',
            'title' => __('Select  footer widget layout', 'listingpro'),
            'subtitle' => __('Select  footer widget layout', 'listingpro'),
            'desc' => __('Control the number of columns in the footer', 'listingpro'),
            'options' => array(
                '12' => '1 columns',
                '6-6' => '2 columns',
                '4-4-4' => '3 columns',
                '3-3-3-3' => '4 columns',
                '2-2-2-2-2-2' => '5 columns',
            ),
            'default' => '3-3-3-3',
            /* 'required' => array( 'footer_style', '=', 'footer2' ), */
            'required' => array('footer_style', '=', array('footer2', 'footer4', 'footer5', 'footer7', 'footer8', 'footer10', 'footer11')),
        ),
        array(
            'id' => 'footer_top_bgcolor',
            'type' => 'color',
            'title' => __('Footer top area Background color', 'listingpro'),
            'subtitle' => __('(default: #363f48).', 'listingpro'),
            'desc' => __('Controls the Background Color of the Footer ', 'listingpro'),
            'default' => '#363f48',
            'validate' => 'color',
            'required' => array('footer_style', '=', array('footer1', 'footer2', 'footer4', 'footer5', 'footer7', 'footer8', 'footer10', 'footer11' , 'footer_classic')),
        ),
        array(
            'id' => 'footer_bgcolor',
            'type' => 'color',
            'title' => __('Footer bottom area Background color', 'listingpro'),
            'subtitle' => __('(default: #45505b).', 'listingpro'),
            'default' => '#45505b',
            'validate' => 'color',
            'required' => array('footer_style', '=', array('footer1', 'footer3', 'footer4', 'footer5', 'footer6', 'footer7', 'footer8', 'footer9', 'footer10' , 'footer_classic')),
        ),
        array(
            'id' => 'footer_top_color_switch',
            'type' => 'switch',
            'title' => esc_html__('Footer Top Text Color ON/OFF', 'listingpro'),
            'subtitle' => '',
            'default' => false,
            'required' => array('footer_style', '=', array('footer1', 'footer2', 'footer4', 'footer5', 'footer7', 'footer8', 'footer10', 'footer11' , 'footer_classic')),
        ),
        array(
            'id' => 'footer_top_text_color',
            'type' => 'color',
            'title' => __('Footer top area text color', 'listingpro'),
            'subtitle' => __('(default: #363f48).', 'listingpro'),
            'default' => '#ffffff',
            'validate' => 'color',
            'required' => array('footer_top_color_switch', 'equals', '1'),
        ),
        array(
            'id' => 'footer_bottom_color_switch',
            'type' => 'switch',
            'title' => esc_html__('Footer Bottom Text Color ON/OFF', 'listingpro'),
            'subtitle' => '',
            'default' => false,
            'required' => array('footer_style', '=', array('footer1', 'footer3', 'footer4', 'footer5', 'footer6', 'footer7', 'footer8', 'footer9', 'footer10' , 'footer_classic' )),
        ),
        array(
            'id' => 'footer_bottom_text_color',
            'type' => 'color',
            'title' => __('Footer bottom area text color', 'listingpro'),
            'subtitle' => __('(default: #45505b).', 'listingpro'),
            'default' => '#45505b',
            'validate' => 'color',
            'required' => array('footer_bottom_color_switch', 'equals', '1'),
        ),
		array(
            'id' => 'footer_bottom_icon_color_switch',
            'type' => 'color',
            'title' => esc_html__('Footer Icon Color', 'listingpro'),
            'subtitle' => '',
            'default' => '#ffffff',
            'default' => false,
            'required' => array('footer_style', '=', 'footer_classic'),
        ),
        array(
            'id' => 'fb',
            'type' => 'text',
            'title' => __("Facebook URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'tw',
            'type' => 'text',
            'title' => __("Twitter URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'insta',
            'type' => 'text',
            'title' => __("Instagram URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'tumb',
            'type' => 'text',
            'title' => __("Tumbler URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'f-yout',
            'type' => 'text',
            'title' => __("Youtube URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'f-linked',
            'type' => 'text',
            'title' => __("LinkedIn URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'f-pintereset',
            'type' => 'text',
            'title' => __("Pinterest URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'f-vk',
            'type' => 'text',
            'title' => __("VK URL", 'listingpro'),
            'subtitle' => '',
            'default' => '#',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'copy_right',
            'type' => 'text',
            'title' => __("Copyright text", 'listingpro'),
            'desc' => __('Enter the text that displays in the Copyright Bar', 'listingpro'),
            'default' => 'Copyright © 2017 Listingpro'
        ),
        array(
            'id' => 'footer_logo',
            'type' => 'media',
            'url' => true,
            'title' => __('Footer Logo', 'listingpro'),
            'compiler' => 'true',
            //'mode'      => false, // Can be set to false to allow any media type, or can also be set to any mime type.
            'desc' => __('Select Image to show with the Page Footer', 'listingpro'),
            'default' => array('url' => get_template_directory_uri() . '/assets/images/logo2.png'),
            'required' => array('footer_style', 'equals', array('footer3', 'footer4', 'footer10', 'footer6', 'footer7', 'footer9', 'footer8', 'footer11')),
        ),
        array(
            'id' => 'footer_address',
            'type' => 'text',
            'title' => __("Address", 'listingpro'),
            'subtitle' => '',
            'default' => '45 B Road NY. USA',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'phone_number',
            'type' => 'text',
            'title' => __("Phone", 'listingpro'),
            'subtitle' => '',
            'default' => '007-123-456',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'author_info',
            'type' => 'text',
            'title' => __("Theme Author Information", 'listingpro'),
            'subtitle' => '',
            'default' => 'Proudly Listingpro by <a href="http://www.cridio.com/" target="_blank">Cridio Studio</a>',
            'required' => array('footer_style', '!=', 'footer3'),
        ),
        array(
            'id' => 'about_heading',
            'type' => 'text',
            'title' => __("Footer Text Section Heading", 'listingpro'),
            'subtitle' => '',
            'default' => 'About Us',
            'required' => array('footer_style', '=', 'footer10'),
        ),
        array(
            'id' => 'about_description',
            'type' => 'text',
            'title' => __("Footer Text Section Content", 'listingpro'),
            'subtitle' => '',
            'default' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam eleifend urna eu sem commodo euismod. Nam nec mauris et magna scelerisque vulputate id a lorem. In neque erat, interdum sed neque in, gravida scelerisque tellus',
            'required' => array('footer_style', '=', 'footer10'),
        ),
        array(
            'id' => 'footer_contact_us',
            'type' => 'text',
            'title' => __("Footer Button Text", 'listingpro'),
            'subtitle' => '',
            'default' => 'Contact Us',
            'required' => array('footer_style', '=', 'footer10'),
        ),
        array(
            'id' => 'footer_contact_us_url',
            'type' => 'text',
            'title' => __("Footer Button URL", 'listingpro'),
            'subtitle' => '',
            'default' => 'Enter Page Url',
            'required' => array('footer_style', '=', 'footer10'),
        ),
    )
));
/*  */
if (file_exists(dirname(__FILE__) . '/../README.md')) {
    $section = array(
        'icon' => 'el el-list-alt',
        'title' => __('Documentation', 'listingpro'),
        'fields' => array(
            array(
                'id' => '17',
                'type' => 'raw',
                'markdown' => true,
                'content_path' => dirname(__FILE__) . '/../README.md', // FULL PATH, not relative please
                //'content' => 'Raw content here',
            ),
        ),
    );
    Redux::setSection($opt_name, $section);
}
/*
     * <--- END SECTIONS
     */
/*
     *
     * YOU MUST PREFIX THE FUNCTIONS BELOW AND ACTION FUNCTION CALLS OR ANY OTHER CONFIG MAY OVERRIDE YOUR CODE.
     *
     */
/*
    *
    * --> Action hook examples
    *
    */
// If Redux is running as a plugin, this will remove the demo notice and links
//add_action( 'redux/loaded', 'remove_demo' );
// Function to test the compiler hook and demo CSS output.
// Above 10 is a priority, but 2 in necessary to include the dynamically generated CSS to be sent to the function.
//add_filter('redux/options/' . $opt_name . '/compiler', 'compiler_action', 10, 3);
// Change the arguments after they've been declared, but before the panel is created
//add_filter('redux/options/' . $opt_name . '/args', 'change_arguments' );
// Change the default value of a field after it's been set, but before it's been useds
//add_filter('redux/options/' . $opt_name . '/defaults', 'change_defaults' );
// Dynamically add a section. Can be also used to modify sections/fields
//add_filter('redux/options/' . $opt_name . '/sections', 'dynamic_section');
/**
 * This is a test function that will let you see when the compiler hook occurs.
 * It only runs if a field    set with compiler=>true is changed.
 * */
if (!function_exists('compiler_action')) {
    function compiler_action($options, $css, $changed_values)
    {
        echo '<h1>The compiler hook has run!</h1>';
        echo "<pre>";
        print_r($changed_values); // Values that have changed since the last save
        echo "</pre>";
        //print_r($options); //Option values
        //print_r($css); // Compiler selector CSS values  compiler => array( CSS SELECTORS )
    }
}
/**
 * Custom function for the callback validation referenced above
 * */
if (!function_exists('redux_validate_callback_function')) {
    function redux_validate_callback_function($field, $value, $existing_value)
    {
        $error = false;
        $warning = false;
        //do your validation
        if ($value == 1) {
            $error = true;
            $value = $existing_value;
        } elseif ($value == 2) {
            $warning = true;
            $value = $existing_value;
        }
        $return['value'] = $value;
        if ($error == true) {
            $return['error'] = $field;
            $field['msg'] = 'your custom error message';
        }
        if ($warning == true) {
            $return['warning'] = $field;
            $field['msg'] = 'your custom warning message';
        }
        return $return;
    }
}
/**
 * Custom function for the callback referenced above
 */
if (!function_exists('redux_my_custom_field')) {
    function redux_my_custom_field($field, $value)
    {
        print_r($field);
        echo '<br/>';
        print_r($value);
    }
}
/**
 * Custom function for filtering the sections array. Good for child themes to override or add to the sections.
 * Simply include this function in the child themes functions.php file.
 * NOTE: the defined constants for URLs, and directories will NOT be available at this point in a child theme,
 * so you must use get_template_directory_uri() if you want to use any of the built in icons
 * */
if (!function_exists('dynamic_section')) {
    function dynamic_section($sections)
    {
        //$sections = array();
        $sections[] = array(
            'title' => __('Section via hook', 'listingpro'),
            'desc' => __('<p class="description">This is a section created by adding a filter to the sections array. Can be used by child themes to add/remove sections from the options.</p>', 'listingpro'),
            'icon' => 'el el-paper-clip',
            // Leave this as a blank section, no options just some intro text set above.
            'fields' => array()
        );
        return $sections;
    }
}
/**
 * Filter hook for filtering the args. Good for child themes to override or add to the args array. Can also be used in other functions.
 * */
if (!function_exists('change_arguments')) {
    function change_arguments($args)
    {
        //$args['dev_mode'] = true;
        return $args;
    }
}
/**
 * Filter hook for filtering the default value of any given field. Very useful in development mode.
 * */
if (!function_exists('change_defaults')) {
    function change_defaults($defaults)
    {
        $defaults['str_replace'] = 'Testing filter hook!';
        return $defaults;
    }
}
/**
 * Removes the demo link and the notice of integrated demo from the redux-framework plugin
 */
if (!function_exists('remove_demo')) {
    function remove_demo()
    {
        // Used to hide the demo mode link from the plugin page. Only used when Redux is a plugin.
        if (class_exists('ReduxFrameworkPlugin')) {
            $lp_remove_filter_var = 'remove';
            $lp_remove_filter_var .= '_filter';
            $lp_remove_filter_var('plugin_row_meta', array(
                ReduxFrameworkPlugin::instance(),
                'plugin_metalinks'
            ), null, 2);
            // Used to hide the activation notice informing users of the demo panel. Only used when Redux is a plugin.
            remove_action('admin_notices', array(ReduxFrameworkPlugin::instance(), 'admin_notices'));
        }
    }
}

if (!function_exists('lp_option_taxrate_validate_callback')) {
    function lp_option_taxrate_validate_callback($field, $value, $existing_value)
    {
        $return['value'] = $value;
        if (!preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $value)) {
            $field['msg']    = 'Please enter only numeric value.';
            $return['error'] = $field;
            $return['value'] = $existing_value;
        } else if ($value <= 0 || $value > 100) {
            $field['msg']    = 'Min Value:1 Max Value:100';
            $return['error'] = $field;
            $return['value'] = $existing_value;
        }
        return $return;
    }
}

//new code 2.6.15
if (!function_exists('lp_option_mapzoom_validate_callback')) {
    function lp_option_mapzoom_validate_callback($field, $value, $existing_value)
    {
        $return['value'] = $value;
        if (!preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $value)) {
            $field['msg']    = 'Please enter only numeric value.';
            $return['error'] = $field;
            $return['value'] = $existing_value;
        } else if ($value < 0 || $value > 17) {
            $field['msg']    = 'Min Value:0 Max Value:17';
            $return['error'] = $field;
            $return['value'] = $existing_value;
        }
        return $return;
    }
}
//end new code 2.6.15