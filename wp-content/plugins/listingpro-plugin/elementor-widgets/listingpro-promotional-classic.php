<?php

namespace ElementorListingpro\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;

if (!defined('ABSPATH')) {
    exit;
} 
// Exit if accessed directly

class Listingpro_Promotional_Classic extends Widget_Base
{

    public function get_name()
    {
        return 'listingpro-promotional-classic';
    }

    public function get_title()
    {
        return __('Promotional Classic', 'elementor-listingpro');
    }

    public function get_icon()
    {
        return 'eicon-posts-ticker';
    }

    public function get_categories()
    {
        return ['listingpro'];
    }

    public function render_plain_content()
    {
    }

    protected function _register_controls()
    {
        // Content Section
        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'elementor-listingpro'),
            ]
        );

        $repeater = new \Elementor\Repeater();

        $repeater->add_control(
            'title',
            [
                'label' => __('Title', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Title', 'elementor-listingpro'),
            ]
        );

        $repeater->add_control(
            'description',
            [
                'label' => __('Description', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'default' => __('Description', 'elementor-listingpro'),
            ]
        );

        $repeater->add_control(
            'image',
            [
                'label' => __('Image', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::MEDIA,
                'default' => [
                    'url' => \Elementor\Utils::get_placeholder_image_src(),
                ],
            ]
        );
		  $repeater->add_control(
            'link_title',
            [
                'label' => __('URL title', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('Learn More', 'elementor-listingpro'),
            ]
        );
		  $repeater->add_control(
            'link_url',
            [
                'label' => __('URL', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::TEXT,
                'default' => __('https://', 'elementor-listingpro'),
            ]
        );
        $this->add_control(
            'repeater_items',
            [
                'label' => __('Repeater Items', 'elementor-listingpro'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'title' => __('Title 1', 'elementor-listingpro'),
                        'description' => __('Description 1', 'elementor-listingpro'),
                    ],
                ],
                'title_field' => '{{{ title }}}',
            ]
        );

        $this->end_controls_section();
    }


    protected function render()
    {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_promotional_classic($settings);
    }

    
}
function listingpro_shortcode_promotional_classic($atts)
{
    extract(shortcode_atts(array(
        'repeater_items' => [],
    ), $atts));
    wp_enqueue_style('lp-classic-promotional');

    $output = '<div class="listingpro-classic-promotional-section">
    <div class="listingpro-classic-box">
				<div class="col-md-9 m-auto">
					<div class="row">
                    ';
	$count = 1 ;
    foreach ($repeater_items as $item) {
        $title = !empty($item['title']) ? $item['title'] : '';
        $description = !empty($item['description']) ? $item['description'] : '';
        $image_url = !empty($item['image']['url']) ? $item['image']['url'] : '';
 		$link_title = !empty($item['link_title']) ? $item['link_title'] : '';
		 $link_url = !empty($item['link_url']) ? $item['link_url'] : '';
        $output .= '
        <div class="col-md-4">
        <div class="classic-box">
			<div class="classic-promotional-box-counters">'.$count.'</div>
            <div class="classic-image">
                <img src="' . esc_url($image_url) . '">
            </div>
            <div class="classic-text">
                <h3>' . esc_html($title) . '</h3>
                <p>' . esc_html($description) . '</p>
				<a class="classic-promotional-box-url" href="'.$link_url.'">'.$link_title.' <i class="fa-solid fa-arrow-right"></i></a>
            </div>
			
            </div>
        </div>';
		$count++;
    }

    $output .= '</div></div></div></div>';

    return $output;
}