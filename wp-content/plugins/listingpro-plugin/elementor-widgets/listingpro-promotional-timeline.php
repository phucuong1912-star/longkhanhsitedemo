<?php

namespace ElementorListingpro\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
} // Exit if accessed directly

class Listingpro_Promotional_Timeline extends Widget_Base {

    public function get_name() {
        return 'listingpro-promotional-timeline';
    }

    public function get_title() {
        return __( 'Promotional Timeline', 'elementor-listingpro' );
    }

    public function get_icon() {
        return 'eicon-posts-ticker';
    }

    public function get_categories() {
        return [ 'listingpro' ];
    }

    public function render_plain_content() {
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'section_content',
            [
                'label' => __( 'Content', 'elementor-listingpro' ),
            ]
        );
        $this->add_control(
            'listing_pro_timeline_title',
            [
                'label' => __( 'Timeline Title', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'pro_timeline_short_desc',
            [
                'label' => __( 'Timeline Short Description', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'pro_timeline_title_first',
            [
                'label' => __( 'Timeline First Title', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'pro_timeline_desc_first',
            [
                'label' => __( 'Timeline Short Description', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'pro_timeline_first_img',
            [
                'label'   => __( 'Timeline Right Image', 'elementor-listingpro' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri() . "/assets/images/time1.png" ]
            ]
        );
        $this->add_control(
            'pro_timeline_title_second',
            [
                'label' => __( 'Timeline Second Title', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXT,
            ]
        );
        $this->add_control(
            'pro_timeline_desc_second',
            [
                'label' => __( 'Timeline Second Description', 'elementor-listingpro' ),
                'type'  => Controls_Manager::TEXTAREA,
            ]
        );
        $this->add_control(
            'pro_timeline_second_img',
            [
                'label'   => __( 'Timeline Left Image', 'elementor-listingpro' ),
                'type'    => Controls_Manager::MEDIA,
                'default' => [ 'url' => get_template_directory_uri() . "/assets/images/time2.png" ]
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __( 'Style', 'elementor-listingpro' ),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_transform',
            [
                'label'     => __( 'Text Transform', 'elementor-listingpro' ),
                'type'      => Controls_Manager::SELECT,
                'options'   => [
                    ''           => __( 'None', 'elementor-hello-world' ),
                    'uppercase'  => __( 'UPPERCASE', 'elementor-listingpro' ),
                    'lowercase'  => __( 'lowercase', 'elementor-listingpro' ),
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
        echo listingpro_shortcode_pro_services_elementor( $settings );
    }

    protected function content_template() {
    }
}

if ( ! function_exists( 'listingpro_shortcode_pro_services_elementor' ) ) {
    function listingpro_shortcode_pro_services_elementor( $atts, $content = null ) {
        extract( shortcode_atts( array(
            'listing_pro_timeline_title' => '',
            'pro_timeline_short_desc'    => '',
            'pro_timeline_title_first'   => '',
            'pro_timeline_desc_first'    => '',
            'pro_timeline_first_img'     => get_template_directory_uri() . "/assets/images/time1.png",
            'pro_timeline_title_second'  => '',
            'pro_timeline_desc_second'   => '',
            'pro_timeline_second_img'    => get_template_directory_uri() . "/assets/images/time2.png",
        ), $atts ) );
        $output = null;

        $timelilneImg1 = '';
        if ( ! empty( $pro_timeline_first_img ) ) {
            $bgImage       = wp_get_attachment_image_src( $pro_timeline_first_img['id'], 'full' );
            $timelilneImg1 = '<img src="' . $bgImage[0] . '" alt="image">';
        } else {
            $timelilneImg1 = '';
        }

        $timelilneImg2 = '';
        if ( ! empty( $pro_timeline_second_img ) ) {
            $bgImage       = wp_get_attachment_image_src( $pro_timeline_second_img['id'], 'full' );
            $timelilneImg2 = '<img src="' . $bgImage[0] . '" alt="image">';
        } else {
            $timelilneImg2 = '';
        }

        $output .= '
            <div class="promotional-timeline">
                <div class="top-desc">
                    <h2>' . $listing_pro_timeline_title . '</h2>
                    <p>' . $pro_timeline_short_desc . '</p>
                </div>
                <div class="timeline-section">
                    <div class="promotional-text-details">
                        <h3>' . $pro_timeline_title_second . '</h3>
                        <p>' . $pro_timeline_desc_second . '</p>
                    </div>
                    <div class="promotional-thumb">
                        ' . $timelilneImg1 . '
                    </div>
                </div>
                <div class="timeline-section">
                    <div class="promotional-thumb">
                        ' . $timelilneImg2 . '
                    </div>
                    <div class="promotional-text-details">
                        <h3>' . $pro_timeline_title_first . '</h3>
                        <p>' . $pro_timeline_desc_first . '</p>
                    </div>
                </div>
            </div>
        ';

        return $output;
    }
}