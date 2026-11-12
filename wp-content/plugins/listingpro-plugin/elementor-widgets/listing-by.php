<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_BY extends Widget_Base {

    public function get_name() {
        return 'listing-by';
    }

    public function get_title() {
        return __( 'Listing By', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $locations = get_terms('location', array('hide_empty' => false));
        $loc = array();
        foreach($locations as $location) {
            $loc[$location->term_id] = $location->name;
        }

        $categories = get_terms('listing-category', array('hide_empty' => false));
        $cats = array();
        foreach($categories as $category) {
            $cats[$category->term_id] = $category->name;
        }


        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'listing_multi_options',
            [
                'label' => __( 'Listing Options', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'cat_view' => __( 'By Category', 'elementor-listingpro' ),
                    'location_view' => __( 'By Location', 'elementor-listingpro' ),
                    'location_cat_view' => __( 'By Location and Category', 'elementor-listingpro' ),
                    'recent_view' => __( 'Recent', 'elementor-listingpro' ),
                ],
                'default' => 'cat_view'
            ]
        );
        $this->add_control(
            'listing_loc',
            [
                'label' => __( 'Select Location', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => $loc,
                'condition' => [
                    'listing_multi_options' => array( 'location_view', 'location_cat_view' )
                ]
            ]
        );
        $this->add_control(
            'listing_cat',
            [
                'label' => __( 'Select Category', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => $cats,
                'condition' => [
                    'listing_multi_options' => array( 'cat_view', 'location_cat_view' )
                ]
            ]
        );
        $this->add_control(
            'listing_per_page',
            [
                'label' => __( 'Listing per page', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '3'
            ]
        );
        $this->add_control(
            'listing_layout',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'list_view' => __( 'List View', 'elementor-listingpro' ),
                    'grid_view' => __( 'Grid View', 'elementor-listingpro' ),
                ],
            ]
        );
        $this->add_control(
            'listing_grid_style',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'grid_view1' => __( 'Grid Style 1', 'elementor-listingpro' ),
                    'grid_view2' => __( 'Grid Style 2', 'elementor-listingpro' ),
                    'grid_view3' => __( 'Grid Style 3', 'elementor-listingpro' ),
                    'grid_view4' => __( 'Grid Style 4', 'elementor-listingpro' ),
                    'grid_view5' => __( 'Grid Style 5', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'listing_layout' => 'grid_view'
                ]
            ]
        );
        $this->add_control(
            'listing_list_style',
            [
                'label' => __( 'Listing Layout', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'listing_views_1' => __( 'List Style 1', 'elementor-listingpro' ),
                    'list_view_v2' => __( 'List Style 2', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'listing_layout' => 'list_view'
                ]
            ]
        );
        $this->add_control(
            'grid3_button_text',
            [
                'label' => __( 'Button Text', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'listing_grid_style' => 'grid_view3'
                ]
            ]
        );
        $this->add_control(
            'grid3_button_link',
            [
                'label' => __( 'Button Link', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'listing_grid_style' => 'grid_view3'
                ]
            ]
        );
        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'elementor-listingpro' ),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_transform',
            [
                'label' => __( 'Text Transform', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '' => __( 'None', 'elementor-hello-world' ),
                    'uppercase' => __( 'UPPERCASE', 'elementor-listingpro' ),
                    'lowercase' => __( 'lowercase', 'elementor-listingpro' ),
                    'capitalize' => __( 'Capitalize', 'elementor-listingpro' ),
                ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_options( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listing_options')) {
    function listingpro_shortcode_listing_options($atts, $content = null) {
        extract(shortcode_atts(array(
            'listing_multi_options'   	=> 'cat_view',
            'listing_loc'   			=> '',
            'listing_cat'   			=> '',
            'listing_per_page'   		=> '3',
            'listing_layout'   		=> 'grid_view',
            'listing_grid_style'   => 'grid_view1',
            'listing_list_style'   => 'listing_views_1',
            'grid3_button_text'   => '',
            'grid3_button_link'   => '',
        ), $atts));

        $output =   listing_elements_loop_cb( 'listing_options', $atts );

        return $output;
    }
}