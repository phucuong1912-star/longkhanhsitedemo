<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Events_Calendar extends Widget_Base {

    public function get_name() {
        return 'evnets-calendar';
    }

    public function get_title() {
        return __( 'Events Calendar', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }
    protected function _register_controls() {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'events_view',
            [
                'label' => __( 'Events View', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'calander' => __( 'Calander', 'elementor-listingpro' ),
                    'list' => __( 'List', 'elementor-listingpro' ),
                ],
                'default' => 'list'
            ]
        );
        $this->add_control(
            'events_calander_duration',
            [
                'label' => __( 'Events Calender Duration', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'monthly' => __( 'Monthly', 'elementor-listingpro' ),
                    'weekly' => __( 'Weekly', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'events_view' => array( 'calander' )
                ],
                'default' => 'monthly'
            ]
        );
        $this->add_control(
            'events_calander_view',
            [
                'label' => __( 'Events Calender View', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'classic' => __( 'Classic', 'elementor-listingpro' ),
                    'classic2' => __( 'Classic 2', 'elementor-listingpro' ),
                    'modern' => __( 'Modern', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'events_calander_duration' => array( 'monthly' ),
                    'events_view' => array( 'calander' ),
                ],
                'default' => 'classic'
            ]
        );
        $this->add_control(
            'events_per_page',
            [
                'label' => __( 'Events Per Page', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    '3' => __( '3', 'elementor-listingpro' ),
                    '4' => __( '4', 'elementor-listingpro' ),
                    '5' => __( '5', 'elementor-listingpro' ),
                    '6' => __( '6', 'elementor-listingpro' ),
                    '7' => __( '7', 'elementor-listingpro' ),
                    '8' => __( '8', 'elementor-listingpro' ),
                    '9' => __( '9', 'elementor-listingpro' ),
                    '10' => __( '10', 'elementor-listingpro' ),
                ],
                'condition' => [
                    'events_view' => array( 'list' )
                ],
                'default' => '5'
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_lp_events_calander( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_lp_events_calander')) {
    function listingpro_shortcode_lp_events_calander($atts, $content = null) {
        extract(shortcode_atts(array(
            'events_view'   => '',
            'events_calander_view'   => '',
            'events_per_page'   => '',
            'events_calander_duration' => ''
        ), $atts));
        $output = null;

        $GLOBALS['events_calander_duration']    =   $events_calander_duration;
        ob_start();

        if( $events_calander_duration == 'weekly' )
        {
            get_template_part( 'templates/events-calender/event-calender-weekly' );
        }
        else
        {
            if( $events_view == 'list' )
            {
                $GLOBALS['events_per_page']  =   $events_per_page;
                get_template_part( 'templates/events-calender/event-list-view' );
            }
            elseif ( $events_calander_view == 'classic' || $events_calander_view == 'classic2' )
            {
                $GLOBALS['calender_type']   =   $events_calander_view;
                get_template_part( 'templates/events-calender/event-calender-classic' );
            }
            else
            {
                get_template_part( 'templates/events-calender/event-calender-modern' );
            }
        }

        return ob_get_clean();
    }
}