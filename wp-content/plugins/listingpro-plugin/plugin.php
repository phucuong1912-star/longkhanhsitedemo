<?php
/*
Plugin Name: ListingPro Plugin
Plugin URI: 
Description: This plugin Only compatible With listingpro Theme By CridioStudio.
Version: 2.9.12
Author: CridioStudio (Dev Team)
Author URI: http://www.crid.io
Author Email: support@cridio.com
Copyright 2021 CridioStudio
*/

$active_theme = wp_get_theme();
if ($active_theme->get('Name') != 'Listingpro' && $active_theme->get('Template') != 'listingpro') {
    return;
}

/* ============== Visual composer added ============ */

define('THEMENAME', 'listingpro');
define('LISTINGPRO_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('LISTINGPRO_PLUGIN_URI', plugin_dir_url(__FILE__));

function listingpro_plugin_uploader()
{
    global $listingpro_options;

    /* end js by haroon */

    wp_register_script('main', plugins_url('/assets/js/main.js', __FILE__), array('jquery'));
    if (!empty(lp_theme_option('listingpro_scastripe_switch')) && lp_theme_option('listingpro_scastripe_switch') == 'yes') {
        wp_register_script('stripe-sca', plugins_url('/assets/js/stripe-sca.js', __FILE__), array('jquery'));
    }
    wp_enqueue_script('main');
    wp_enqueue_script('lpAutoPlaces', LISTINGPRO_PLUGIN_URI . 'assets/js/auto-places.js', 'jquery', '', true);
}
add_action('wp_enqueue_scripts', 'listingpro_plugin_uploader');

/* ============== Listingpro js for edit and submit=========== */

if (!function_exists('listingpro_listingpage_scripts')) {
    function listingpro_listingpage_scripts()
    {
        wp_register_script('uploader', plugins_url('/assets/js/uploader.js', __FILE__), array('jquery'));
        wp_enqueue_script('uploader');
    }
}
add_action('lp_call_maps_scripts', 'listingpro_listingpage_scripts');


function listingpro_plugin_admin_script()
{
    wp_register_script('adminjs', plugins_url('/assets/js/admin.js', __FILE__), array('jquery'));
    wp_enqueue_script('adminjs');
    global $pagenow, $post;
    $page = null;

    if (isset($pagenow) && $pagenow == 'post-new.php' && isset($_GET['post_type']) && $_GET['post_type'] == 'listing') {
        $page = 'listing';
    } elseif (isset($pagenow) && $pagenow == 'post.php' && isset($post) && get_post_type($post->ID) == 'listing') {
        $page = 'listing';
    }

    wp_localize_script('adminjs', 'lp_admin_vars', array(
        'is_page' => $page,
        'admin_nonce' => wp_create_nonce('lp_admin_nonce_action')
    ));
}
add_action('admin_enqueue_scripts', 'listingpro_plugin_admin_script');

function listingpro_plugin_admin_style()
{
    wp_enqueue_style('fontawesome', 'https:////netdna.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.css', '', '4.7.0', 'all');
}
add_action('admin_init', 'listingpro_plugin_admin_style');



add_action('init', 'jplug_admin_init');
function jplug_admin_init()
{

    /* Register our script. */
    if (class_exists('WPBakeryVisualComposerAbstract')) {
        //vc_disable_frontend();

        require WP_PLUGIN_DIR . '/listingpro-plugin/inc/vc_mods/vc_mods.php';
        $vc_template_dir =  WP_PLUGIN_DIR . '/listingpro-plugin/inc/vc_mods/vc_templates';
        vc_set_shortcodes_templates_dir($vc_template_dir);
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/vc_special_elements.php');
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/vc-icon-param.php');
        $check = get_option('theme_activation');
        if (!empty($check) && $check != 'none') {
            include_once(WP_PLUGIN_DIR . '/listingpro-plugin/shortcodes/pricing.php');
            include_once(WP_PLUGIN_DIR . '/listingpro-plugin/shortcodes/submit.php');
            include_once(WP_PLUGIN_DIR . '/listingpro-plugin/shortcodes/edit.php');
        }

        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/shortcodes/checkout.php');
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/paypal/form-handler.php');
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/paypal/form-handler2.php');
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/shortcodes/category-element.php');
    }
}

function listingpro_load_textdomain() {
    $domain = 'listingpro-plugin';
    $mofile = WP_PLUGIN_DIR . '/listingpro-plugin/languages/' . $domain . '-' . determine_locale() . '.mo';
    if (file_exists($mofile)) {
        load_textdomain($domain, $mofile);
    }
}
add_action('plugins_loaded', 'listingpro_load_textdomain');

function post_type_pricing()
{
    $labels = array(
        'name' => _x('Pricing Plans', 'post type general name', 'listingpro-plugin'),
        'singular_name' => _x('Price Plans', 'post type singular name', 'listingpro-plugin'),
        'add_new' => _x('Add New Price Plan', 'book', 'listingpro-plugin'),
        'add_new_item' => __('Add New Price Plan', 'listingpro-plugin'),
        'edit_item' => __('Edit Price Plan', 'listingpro-plugin'),
        'new_item' => __('New Price Plan', 'listingpro-plugin'),
        'view_item' => __('View Price Plan', 'listingpro-plugin'),
        'search_items' => __('Search Price Plans', 'listingpro-plugin'),
        'not_found' =>  __('No Price Plan found', 'listingpro-plugin'),
        'not_found_in_trash' => __('No Price Plans found in Trash', 'listingpro-plugin'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => false,
        'show_ui' => true,
        'query_var' => true,
        'rewrite' => true,
        'capability_type' => 'post',
        'hierarchical' => false,
        'menu_position' => null,
        'supports' => array('title'),
        'menu_icon' => plugins_url('listingpro-plugin/images/plans.png', dirname(__FILE__))
    );

    register_post_type('price_plan', $args);
}

add_action('init', 'post_type_pricing');
function lp_price_plan_columns($columns)
{
    return array(
        'cb' => '<input type="checkbox" />',
        'title' => esc_html__('Title', 'listingpro-plugin'),
        'number-of-listings' => esc_html__('Number of Listings', 'listingpro-plugin'),
        'date' => esc_html__('Date', 'listingpro-plugin'),
    );
}
add_filter('manage_price_plan_posts_columns', 'lp_price_plan_columns');

function lp_price_plan_columns_content($column, $post_id)
{

    if ($column == 'number-of-listings') {

        $lp_listing_arr = array(
            'post_type' => 'listing',
            'post_status' => 'publish',
            'posts_per_page' => -1,
            'meta_key' => 'plan_id',
            'meta_value' => $post_id,
            'meta_compare' => '=',
            'fields' => 'ids',
        );

        $lp_litinga = new WP_Query($lp_listing_arr);

        echo $lp_litinga->found_posts;
    }
}
add_action('manage_price_plan_posts_custom_column', 'lp_price_plan_columns_content', 10, 2);


function post_type_listing()
{

    global $listingpro_options;
    $listingSLUG = '';
    if (class_exists('ReduxFramework')) {
        if (isset($listingpro_options['listing_slug'])) {
            $listingSLUG = $listingpro_options['listing_slug'];
        }
        if (!empty($listingSLUG)) {
            $listingSLUG = trim($listingSLUG);
        }
    }
    if (empty($listingSLUG)) {
        $listingSLUG = 'listing';
    }

    $labels = array(
        'name' => _x('Listings', 'post type general name', 'listingpro-plugin'),
        'singular_name' => _x('Listing', 'post type singular name', 'listingpro-plugin'),
        'add_new' => _x('Add New', 'book', 'listingpro-plugin'),
        'add_new_item' => __('Add New List', 'listingpro-plugin'),
        'edit_item' => __('Edit List', 'listingpro-plugin'),
        'new_item' => __('New Listing', 'listingpro-plugin'),
        'view_item' => __('View List', 'listingpro-plugin'),
        'search_items' => __('Search Listing', 'listingpro-plugin'),
        'not_found' =>  __('No List found', 'listingpro-plugin'),
        'not_found_in_trash' => __('No List found in Trash', 'listingpro-plugin'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => true,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => 'listing',
        'rewrite'   => array('slug' => $listingSLUG),
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'show_in_rest'       => true,
        'supports' => array('title', 'editor', 'author', 'thumbnail', 'comments'),
        'menu_icon' => plugins_url('listingpro-plugin/images/reviews.png', dirname(__FILE__))
    );

    register_post_type('listing', $args);
}

add_action('init', 'post_type_listing', 0);

function post_type_form_fields()
{
    $labels = array(
        'name' => _x('Form Fields', 'post type general name', 'listingpro-plugin'),
        'singular_name' => _x('Field', 'post type singular name', 'listingpro-plugin'),
        'add_new' => _x('Add New Field', 'book', 'listingpro-plugin'),
        'add_new_item' => __('Add New Field', 'listingpro-plugin'),
        'edit_item' => __('Edit Field', 'listingpro-plugin'),
        'new_item' => __('New Field', 'listingpro-plugin'),
        'view_item' => __('View Field', 'listingpro-plugin'),
        'search_items' => __('Search Fields', 'listingpro-plugin'),
        'not_found' =>  __('No Field found', 'listingpro-plugin'),
        'not_found_in_trash' => __('No Field found in Trash', 'listingpro-plugin'),
        'parent_item_colon' => ''
    );
    $args = array(
        'labels' => $labels,
        'public' => false,
        'publicly_queryable' => true,
        'show_ui' => true,
        'query_var' => 'form-fields',
        'rewrite' => true,
        'capability_type' => 'post',
        'has_archive' => false,
        'hierarchical' => false,
        'menu_position' => null,
        'exclude_from_search' => true,
        'show_in_menu' => 'edit.php?post_type=listing',
        'supports' => array('title'),
        'menu_icon' => plugins_url('listingpro-plugin/images/blog.png', dirname(__FILE__))
    );

    register_post_type('form-fields', $args);
}
add_action('init', 'post_type_form_fields', 0);


function listingpro_form_field_sideHide()
{
    $screen = get_current_screen();
    if (!empty($screen)) {
        if (in_array($screen->id, array('form-fields'))) {
            echo '<style>#minor-publishing { display: none; }</style>';
        }
    }
}
add_action('admin_head', 'listingpro_form_field_sideHide');
function remove_post_custom_fields()
{
    remove_meta_box('commentstatusdiv', 'listing', 'normal');
    remove_meta_box('commentsdiv', 'listing', 'normal');
    remove_meta_box('revisionsdiv', 'listing', 'normal');
    remove_meta_box('authordiv', 'listing', 'normal');
    remove_meta_box('featuresdiv', 'listing', 'side');
}
add_action('admin_menu', 'remove_post_custom_fields');

function listing_category()
{
    global $listingpro_options;
    $listing_cat_slug = '';
    if (class_exists('ReduxFramework')) {
        if (isset($listingpro_options['listing_cat_slug']) && !empty($listingpro_options['listing_cat_slug'])) {
            $listing_cat_slug = $listingpro_options['listing_cat_slug'];
            $listing_cat_slug = trim($listing_cat_slug);
        }
    }
    if (empty($listing_cat_slug)) {
        $listing_cat_slug = 'listing-category';
    }
    register_taxonomy(
        'listing-category',
        'listing',
        array(
            'labels' => array(
                'name' => 'Categories',
                'add_new_item' => 'Add New Category',
                'new_item_name' => "New Category"
            ),
            'show_ui' => true,
            'show_tagcloud' => false,
            'hierarchical' => true,
            'rewrite'           => array('slug' => $listing_cat_slug, 'hierarchical' => true),
            'query_var'     => true,
            'public'            => true,
            'show_in_rest'       => true,
            'capabilities' => array(
                'assign_terms' => 'assign_listing-category',
            )
        )
    );
}
add_action('init', 'listing_category', 0);


function listing_features()
{
    global $listingpro_options;
    $listing_features_slug = '';
    if (class_exists('ReduxFramework')) {
        if (isset($listingpro_options['listing_features_slug']) && !empty($listingpro_options['listing_features_slug'])) {
            $listing_features_slug = $listingpro_options['listing_features_slug'];
            $listing_features_slug = trim($listing_features_slug);
        }
    }
    if (empty($listing_features_slug)) {
        $listing_features_slug = 'features';
    }
    register_taxonomy(
        'features',
        'listing',
        array(
            'hierarchical'  => false,
            'labels' => array(
                'name' => 'Features',
                'add_new_item' =>  __('Add New Feature', 'listingpro-plugin'),
                'new_item_name' =>  __('New Feature', 'listingpro-plugin'),
                'view_item' =>  __('View Feature', 'listingpro-plugin'),
            ),
            'singular_name' => "Feature",
            'show_ui' => true,
            'rewrite' => array('slug' => $listing_features_slug),
            'query_var'     => true,
            'public'            => true,
            'show_in_quick_edit' => false,
            'capabilities' => array(
                'assign_terms' => 'assign_features',
            )
        )
    );
}
add_action('init', 'listing_features', 0);
function listing_tags()
{

    register_taxonomy(
        'list-tags',
        'listing',
        array(
            'hierarchical'  => false,
            'label'         => "Tags",
            'singular_name' => "Tag",
            'show_ui' => true,
            'rewrite'       => true,
            'query_var'     => true,
            'public'            => true,
            'show_in_rest'       => true,
            'capabilities' => array(
                'assign_terms' => 'assign_list-tags',
            )
        )
    );
}
add_action('init', 'listing_tags', 0);
function listing_location()
{
    global $listingpro_options;
    $listing_loc_slug = '';
    if (class_exists('ReduxFramework')) {
        if (isset($listingpro_options['listing_loc_slug']) && !empty($listingpro_options['listing_loc_slug'])) {
            $listing_loc_slug = $listingpro_options['listing_loc_slug'];
            $listing_loc_slug = trim($listing_loc_slug);
        }
    }
    if (empty($listing_loc_slug)) {
        $listing_loc_slug = 'location';
    }
    $location_labels = array(
        'name' => __('Location', 'listingpro-plugin'),
        'singular_name' => __('Location', 'listingpro-plugin'),
        'search_items' =>  __('Search Locations', 'listingpro-plugin'),
        'view_item' =>  __('View Location', 'listingpro-plugin'),
        'popular_items' => __('Popular Locations', 'listingpro-plugin'),
        'all_items' => __('All Locations', 'listingpro-plugin'),
        'parent_item' => __('Parent Location', 'listingpro-plugin'),
        'parent_item_colon' => __('Parent Location:', 'listingpro-plugin'),
        'edit_item' => __('Edit Location', 'listingpro-plugin'),
        'update_item' => __('Update Location', 'listingpro-plugin'),
        'add_new_item' => __('Add New Location', 'listingpro-plugin'),
        'new_item_name' => __('New Location Name', 'listingpro-plugin'),
        'separate_items_with_commas' => __('Separate Locations with commas', 'listingpro-plugin'),
        'add_or_remove_items' => __('Add or remove Location', 'listingpro-plugin'),
        'choose_from_most_used' => __('Choose from the most used Locations', 'listingpro-plugin'),
        'menu_name' => __('Locations', 'listingpro-plugin'),
        'back_to_items' => __('Back to Locations', 'listingpro-plugin')
    );
    register_taxonomy(
        "location",
        array("listing"),
        array(
            "hierarchical" => true,
            'labels' => $location_labels,
            'show_ui' => true,
            'query_var' => true,
            'rewrite'   => array('slug' => $listing_loc_slug),
            'show_in_rest'       => true,
            'capabilities' => array(
                'assign_terms' => 'assign_location',
            )
        )
    );
}
add_action('init', 'listing_location', 0);

add_action('init', 'listingpro_plugin_functions', 0);
function listingpro_plugin_functions()
{

    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/functions.php');

    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/metaboxes/location-meta.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/metaboxes/category-meta.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/metaboxes/features-meta.php');

    if (!empty(lp_theme_option('lp_recaptcha_switch'))) {
        include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/recaptcha.php');
    }

    include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/paypal/campaign-save.php");

    if (!empty(lp_theme_option('listingpro_scastripe_switch')) && lp_theme_option('listingpro_scastripe_switch') == 'yes') {
        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/stripe/stripe-ajax-sca.php");
    } else {
        include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/stripe/stripe-ajax.php");
    }
    include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/2checkout/payment-campaigns.php");
    include_once(WP_PLUGIN_DIR . "/listingpro-plugin/inc/2checkout/payment.php");

    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/field-types.php');

    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/claims.php');

    /* ============== listing Widgets ============ */
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/widget_most_viewed.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/widget_ads_listing.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/widget_nearby_listing.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/contact_widget.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/category_widget.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/recent_posts_widget.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/widgets/social_widget.php');

    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/metaboxes-plans.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/pricing/loop/claim_plans.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/metaboxes/post-type.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/invoices.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/templates/hours-form.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/setup-pages.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/last-review.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/submit-ajax.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/frontend-media.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/invoices-ads.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/register_new_status.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/register_reported_status.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp_functions.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/subscriptions.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp-reports/reports.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/welcome-listingpro.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/classes/lp_payment.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp-coupons/coupons.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/submit-form/submit-form.php');
    include_once(WP_PLUGIN_DIR . '/listingpro-plugin/inc/lp-wizard.php');
}

register_activation_hook(__FILE__, 'save_default_rating_fields');
if (!function_exists('save_default_rating_fields')) {
    function save_default_rating_fields()
    {
        $default_ratings_settings   =   get_option('lp-ratings-default-settings');
        if (!$default_ratings_settings) {
            global $listingpro_options;

            $settings_fields =   array();
            for ($i = 1; $i <= 4; $i++) {
                $field_active_status = '';
                if (isset($listingpro_options['lp_multi_ratiing' . $i . '_switch'])) {
                    $field_active_status    =   $listingpro_options['lp_multi_ratiing' . $i . '_switch'];
                }
                if ($field_active_status == 1) {
                    $settings_fields['default'][]   =   $listingpro_options['lp_multi_ratiing' . $i . '_label_switch'];
                }
            }
            update_option('lp-ratings-default-settings', $settings_fields);
        }
    }
}
if (!function_exists('post_type_events')) {
    function post_type_events()
    {
        global $listingpro_options;
        $eventsSLUG = '';
        if (class_exists('Redux') && isset($listingpro_options) && !empty($listingpro_options)) {
            $eventsSLUG = lp_theme_option('events_slug');
            $eventsSLUG = trim($eventsSLUG);
        }
        if (empty($eventsSLUG)) {
            $eventsSLUG = 'events';
        }
        $labels = array(
            'name' => _x('Events', 'post type general name', 'listingpro-plugin'),
            'singular_name' => _x('Events', 'post type singular name', 'listingpro-plugin'),
            'add_new' => _x('Add New Event', 'book', 'listingpro-plugin'),
            'add_new_item' => __('Add New Event', 'listingpro-plugin'),
            'edit_item' => __('Edit Event', 'listingpro-plugin'),
            'new_item' => __('New Event', 'listingpro-plugin'),
            'view_item' => __('View Event', 'listingpro-plugin'),
            'search_items' => __('Search Events', 'listingpro-plugin'),
            'not_found' =>  __('No Event found', 'listingpro-plugin'),
            'not_found_in_trash' => __('No Event found in Trash', 'listingpro-plugin'),
            'parent_item_colon' => ''
        );
        $args = array(
            'labels' => $labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'query_var' => true,
            'rewrite'   => array('slug' => $eventsSLUG),
            'capability_type' => 'post',
            'hierarchical' => false,
            'menu_position' => null,
            'supports' => array('title', 'editor', 'thumbnail'),
        );

        register_post_type('events', $args);
    }
}

add_action('init', 'post_type_events');


add_action('add_meta_boxes', 'event_details_meta');
add_action('save_post', 'save_event_metas');
if (!function_exists('event_details_meta')) {
    function event_details_meta()
    {
        add_meta_box('event_meta_box', __('Event Details', 'listingpro'), 'event_meta_box', 'events');
    }
}

if (!function_exists('event_meta_box')) {
    function event_meta_box($post)
    {
        wp_nonce_field(basename(__FILE__), 'event_meta_box_nonce');
        $event_id   =   $post->ID;

        $event_date         =   get_post_meta($event_id, 'event-date', true);
        $event_time         =   get_post_meta($event_id, 'event-time', true);
        $event_date_e       =   get_post_meta($event_id, 'event-date-e', true);
        $event_time_e       =   get_post_meta($event_id, 'event-time-e', true);
        $event_lon          =   get_post_meta($event_id, 'event-lat', true);
        $event_lat             =   get_post_meta($event_id, 'event-lon', true);
        $event_loc            =   get_post_meta($event_id, 'event-loc', true);
        $event_ticket_url   =   get_post_meta($event_id, 'ticket-url', true);
        $lsiting_id         =   get_post_meta($event_id, 'event-lsiting-id', true);

?>
        <div class="inside">
            <table class="form-table lp-metaboxes">
                <tbody>
                    <tr id="lp_field_event_listing">
                        <th>
                            <label for="tagline_text">
                                <strong>Listing</strong>
                            </label>
                        </th>
                        <td>
                            <div class="type_listing add_item_medium">
                                <input value="<?php echo get_the_title($lsiting_id); ?>" type="text" name="s" class="unique-for-events form-control search-autocomplete lpautocomplete" placeholder="Search">
                                <input type="hidden" name="event-lsiting-id" id="lpautocompletSelec" value="<?php echo $lsiting_id; ?>">
                                <i class="lp-listing-sping fa-li fa fa-spinner fa-spin"></i>
                                <div class="lpsuggesstion-box"></div>
                            </div>
                        </td>
                    </tr>
                    <tr id="lp_field_event_date">
                        <th>
                            <label for="tagline_text">
                                <strong>Date</strong>
                            </label>
                        </th>
                        <td>
                            <input <?php if (!empty($event_date)) : ?> value="<?php echo date('M d, Y', $event_date); ?>" <?php endif; ?> type="text" name="event_date" id="event_date">
                        </td>
                    </tr>
                    <tr id="lp_field_event_time">
                        <th>
                            <label for="tagline_text">
                                <strong>Time</strong>
                            </label>
                        </th>
                        <td>
                            <input value="<?php echo $event_time; ?>" type="text" name="event_time" id="event_time">
                        </td>
                    </tr>
                    <tr id="lp_field_event_date_e">
                        <th>
                            <label for="lp_field_event_date_e">
                                <strong>End Date</strong>
                            </label>
                        </th>
                        <td>
                            <input <?php if (!empty($event_date_e)) : ?> value="<?php echo date('M d, Y', $event_date_e); ?>" <?php endif; ?> type="text" name="event_date_e" id="event_date_e">
                        </td>
                    </tr>
                    <tr id="lp_field_event_time_e">
                        <th>
                            <label for="lp_field_event_time_e">
                                <strong>End Time</strong>
                            </label>
                        </th>
                        <td>
                            <input value="<?php echo $event_time_e; ?>" type="text" name="event_time_e" id="event_time_e">
                        </td>
                    </tr>
                    <tr id="lp_field_event_loc">
                        <th>
                            <label for="tagline_text">
                                <strong>Location</strong>
                            </label>
                        </th>
                        <td>
                            <input value="<?php echo $event_loc; ?>" type="text" name="event_loc" id="event_loc">
                            <input value="<?php echo $event_lat; ?>" type="hidden" name="event_lat" id="event_lat">
                            <input value="<?php echo $event_lon; ?>" type="hidden" name="event_lon" id="event_lon">
                        </td>
                    </tr>
                    <tr id="lp_field_event_ticket_url">
                        <th>
                            <label for="tagline_text">
                                <strong>Ticket URL</strong>
                            </label>
                        </th>
                        <td>
                            <input value="<?php echo $event_ticket_url; ?>" type="text" name="event_ticket_url" id="event_ticket_url">
                        </td>
                    </tr>
                    <?php wp_nonce_field('', 'events_meta_nonce'); ?>
                </tbody>
            </table>
        </div>
<?php

    }
}

if (!function_exists('save_event_metas')) {
    function save_event_metas($post_id)
    {
        if (!isset($_POST['events_meta_nonce']) && empty($_POST['events_meta_nonce'])) {
            return;
        }
        $post_type = get_post_type($post_id);
        if ("events" != $post_type) return;

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $event_date = strtotime(sanitize_text_field($_POST['event_date']));
            $event_time = sanitize_text_field($_POST['event_time']);
            $event_date_e = strtotime(sanitize_text_field($_POST['event_date_e']));
            $event_time_e = sanitize_text_field($_POST['event_time_e']);
            $event_loc = sanitize_text_field($_POST['event_loc']);
            $event_lat = sanitize_text_field($_POST['event_lat']);
            $event_lon = sanitize_text_field($_POST['event_lon']);
            $event_ticket_url = sanitize_text_field($_POST['event_ticket_url']);
            $eLID   =   sanitize_text_field($_POST['event-lsiting-id']);

            $get_current_event_ids  =   get_post_meta($eLID, 'event_id', true);
            if (isset($get_current_event_ids) && is_array($get_current_event_ids)) {
                $get_current_event_ids[]    =   $post_id;
            }
            if (isset($get_current_event_ids) && !is_array($get_current_event_ids)) {
                $get_current_event_ids = (array)$get_current_event_ids;
                $get_current_event_ids[]    =   $post_id;
            }

            update_post_meta($eLID, 'event_id', $get_current_event_ids);

            update_post_meta($post_id, 'event-lsiting-id', $eLID);
            update_post_meta($post_id, 'event-date', $event_date);
            update_post_meta($post_id, 'event-time', $event_time);
            update_post_meta($post_id, 'event-date-e', $event_date_e);
            update_post_meta($post_id, 'event-time-e', $event_time_e);
            update_post_meta($post_id, 'event-loc', $event_loc);
            update_post_meta($post_id, 'event-lat', $event_lat);
            update_post_meta($post_id, 'event-lon', $event_lon);
            update_post_meta($post_id, 'ticket-url', $event_ticket_url);
        }
    }
}


//elementor initialize class
final class Elementor_Listingpro
{
    const VERSION = '1.2.0';
    const MINIMUM_ELEMENTOR_VERSION = '2.0.0';
    const MINIMUM_PHP_VERSION = '7.0';
    public function __construct()
    {
        // Load translation
        add_action('init', array($this, 'i18n'));
        // Init Plugin
        add_action('plugins_loaded', array($this, 'init'));
    }

    public function i18n()
    {
        load_plugin_textdomain('elementor-hello-world');
    }
    public function init()
    {
        // Check if Elementor installed and activated
        if (!did_action('elementor/loaded')) {
            add_action('admin_notices', array($this, 'admin_notice_missing_main_plugin'));
            return;
        }
        // Check for required Elementor version
        if (!version_compare(ELEMENTOR_VERSION, self::MINIMUM_ELEMENTOR_VERSION, '>=')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_elementor_version'));
            return;
        }
        // Check for required PHP version
        if (version_compare(PHP_VERSION, self::MINIMUM_PHP_VERSION, '<')) {
            add_action('admin_notices', array($this, 'admin_notice_minimum_php_version'));
            return;
        }
        // Once we get here, We have passed all validation checks so we can safely include our plugin
        require_once('elementor_special_widgets.php');
    }
    public function admin_notice_missing_main_plugin()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor */
            esc_html__('"%1$s" requires "%2$s" to be installed and activated.', 'elementor-hello-world'),
            '<strong>' . esc_html__('Elementor Hello World', 'elementor-hello-world') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'elementor-hello-world') . '</strong>'
        );
        //printf( '<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message );
    }
    public function admin_notice_minimum_elementor_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: Elementor 3: Required Elementor version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'listingpro-plugin'),
            '<strong>' . esc_html__('Elementor Listingpro', 'listingpro-plugin') . '</strong>',
            '<strong>' . esc_html__('Elementor', 'listingpro-plugin') . '</strong>',
            self::MINIMUM_ELEMENTOR_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
    public function admin_notice_minimum_php_version()
    {
        if (isset($_GET['activate'])) {
            unset($_GET['activate']);
        }
        $message = sprintf(
            /* translators: 1: Plugin name 2: PHP 3: Required PHP version */
            esc_html__('"%1$s" requires "%2$s" version %3$s or greater.', 'listingpro-plugin'),
            '<strong>' . esc_html__('Elementor Listingpro', 'listingpro-plugin') . '</strong>',
            '<strong>' . esc_html__('PHP', 'listingpro-plugin') . '</strong>',
            self::MINIMUM_PHP_VERSION
        );
        printf('<div class="notice notice-warning is-dismissible"><p>%1$s</p></div>', $message);
    }
}
ob_start();
new Elementor_listingpro();
