<?php

namespace ElementorListingpro;

class Plugin {

    private static $_instance = null;

    public static function instance() {
        if ( is_null( self::$_instance ) ) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
    private function include_widgets_files() {
        require_once(__DIR__ . '/elementor-widgets/listing-categories.php');
        require_once(__DIR__ . '/elementor-widgets/blog-grids.php');
        require_once(__DIR__ . '/elementor-widgets/listing-locations.php');
        require_once(__DIR__ . '/elementor-widgets/client-testimonial.php');
        require_once(__DIR__ . '/elementor-widgets/call-top-action.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-notification.php');
        require_once(__DIR__ . '/elementor-widgets/listing-entries.php');
        require_once(__DIR__ . '/elementor-widgets/feature-box.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-columns.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional-services.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional-timeline.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional-presentation.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional-support.php');
        require_once(__DIR__ . '/elementor-widgets/video-testimonial.php');
        require_once(__DIR__ . '/elementor-widgets/image-gallery.php');
        require_once(__DIR__ . '/elementor-widgets/activities.php');
        require_once(__DIR__ . '/elementor-widgets/listing-by.php');
        require_once(__DIR__ . '/elementor-widgets/listing-posts.php');
        require_once(__DIR__ . '/elementor-widgets/listing-posts-by-id.php');
        require_once(__DIR__ . '/elementor-widgets/listing-with-coupons.php');
        require_once(__DIR__ . '/elementor-widgets/coupons.php');
        require_once(__DIR__ . '/elementor-widgets/claimed-listings.php');
        require_once(__DIR__ . '/elementor-widgets/content-boxes.php');
        require_once(__DIR__ . '/elementor-widgets/listingpro-partners.php');
        require_once(__DIR__ . '/elementor-widgets/events-calander.php');
        require_once(__DIR__ . '/elementor-widgets/events.php');
        require_once(__DIR__ . '/elementor-widgets/pricing-plans.php');
        require_once(__DIR__ . '/elementor-widgets/submit-listing.php');
        require_once(__DIR__ . '/elementor-widgets/edit-listing.php');
        require_once(__DIR__ . '/elementor-widgets/checkout.php');
        require_once(__DIR__ . '/elementor-widgets/popular-listings.php');
        require_once(__DIR__ . '/elementor-widgets/announcements.php');
        include_once(WP_PLUGIN_DIR.'/listingpro-plugin/inc/paypal/form-handler.php');
        include_once(WP_PLUGIN_DIR.'/listingpro-plugin/inc/paypal/form-handler2.php');
         // added classec`
        require_once(__DIR__ . '/elementor-widgets/listingpro-promotional-classic.php');
    }
    public function register_widgets() {
        // Its is now safe to include Widgets files
        $this->include_widgets_files();
        // Register Widgets
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Categories() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Blog_Grids() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Locations() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Client_Testimonial() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Call_To_Action() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Notification() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Entries() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Feature_Box() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Columns_Element() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Element() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Services() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Timeline() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Presentation() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Support() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Video_Testimonial() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Image_Gallery() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Activities() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_BY() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Posts() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Posts_By_ID() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_With_Coupons() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Claimed_Listings() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Content_Boxes() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Partners() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Events_Calendar() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Events() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Pricing_Plans() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Submit_Listing() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Edit_Listing() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Checkout() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Coupons() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_Announcements() );
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listing_With_Popular() );
          // classic promotional section
        \Elementor\Plugin::instance()->widgets_manager->register_widget_type( new Widgets\Listingpro_Promotional_Classic() );
    }
    public function add_elementor_listingpro_widget_category( $elements_manager ) {
        $elements_manager->add_category(
            'listingpro',
            [
                'title' => __( 'ListingPro', 'elementor-listingpro' ),
                'icon' => 'fa fa-plug',
            ]
        );
    }
    public function __construct() {
        // Register categories
        add_action( 'elementor/elements/categories_registered', [ $this, 'add_elementor_listingpro_widget_category'] );

        // Register widgets
        add_action( 'elementor/widgets/register', [ $this, 'register_widgets' ] );
    }
}
// Instantiate Plugin Class
Plugin::instance();