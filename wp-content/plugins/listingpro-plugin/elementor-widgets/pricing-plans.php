<?php

namespace ElementorListingpro\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (! defined('ABSPATH')) exit; // Exit if accessed directly

class Pricing_Plans extends Widget_Base
{

    public function get_name()
    {
        return 'pricing-plans';
    }

    public function get_title()
    {
        return __('Pricing Plans', 'elementor-listingpro');
    }

    public function get_icon()
    {
        return 'eicon-posts-ticker';
    }

    public function get_categories()
    {
        return ['listingpro'];
    }
    protected function _register_controls()
    {

        $this->start_controls_section(
            'section_content',
            [
                'label' => __('Content', 'elementor-listingpro'),
            ]
        );
        $this->add_control(
            'title_subtitle_show',
            [
                'label' => __('Show Title And Subtitle', 'elementor-listingpro'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Show', 'your-plugin'),
                'label_off' => __('Hide', 'your-plugin'),
                'return_value' => 'show_hide',
            ]
        );
        $this->add_control(
            'title',
            [
                'label' => __('Title', 'elementor-listingpro'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'title_subtitle_show' => array('show_hide')
                ],
            ]
        );
        $this->add_control(
            'subtitle',
            [
                'label' => __('Subtitle', 'elementor-listingpro'),
                'type' => Controls_Manager::TEXT,
                'condition' => [
                    'title_subtitle_show' => array('show_hide')
                ],
            ]
        );
        $this->add_control(
            'pricing_views',
            [
                'label' => __('Events View', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal_view' => __('Horizontal View', 'elementor-listingpro'),
                    'vertical_view' => __('Vertical View', 'elementor-listingpro'),
                ],
                'default' => 'vertical_view'
            ]
        );
        $this->add_control(
            'pricing_horizontal_view',
            [
                'label' => __('Horizontal Views', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'horizontal_view_1' => __('Horizontal View 1', 'elementor-listingpro'),
                    'horizontal_view_2' => __('Horizontal View 2', 'elementor-listingpro'),
                ],
                'condition' => [
                    'pricing_views' => array('horizontal_view')
                ],
                'default' => 'horizontal_view_1'
            ]
        );
        $this->add_control(
            'pricing_vertical_view',
            [
                'label' => __('Vertical Views', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'vertical_view_1' => __('Vertical View 1', 'elementor-listingpro'),
                    'vertical_view_2' => __('Vertical View 2', 'elementor-listingpro'),
                    'vertical_view_5' => __('Vertical View 3', 'elementor-listingpro'),
                    'vertical_view_9' => __('Vertical View 4', 'elementor-listingpro'),
                ],
                'condition' => [
                    'pricing_views' => array('vertical_view')
                ],
                'default' => 'vertical_view_1'
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_pricing($settings);
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if (!function_exists('listingpro_shortcode_pricing')) {
    function listingpro_shortcode_pricing($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'title_subtitle_show'        => '',
            'title'               => '',
            'subtitle'           => '',
            'pricing_views'       => 'horizontal_view',
            'pricing_horizontal_view'  => 'horizontal_view_1',
            'pricing_vertical_view'  => 'vertical_view_1',
            'plan_status'  => '',
        ), $atts));
        $output = null;
        global $listingpro_options;

        //set_query_var('pricing_plan_style', $pricing_views);
        $pricing_views = isset($pricing_views) ? sanitize_file_name($pricing_views) : '';
        $allowed_statuses = array('horizontal_view', 'vertical_view');
        $GLOBALS['pricing_views'] = $pricing_views;
        $GLOBALS['pricing_horizontal_view'] = $pricing_horizontal_view;
        $GLOBALS['pricing_vertical_view'] = $pricing_vertical_view;


        $lp_plans_cats = lp_theme_option('listingpro_plans_cats');
        $lp_plans_cats_position = lp_theme_option('listingpro_plans_cats');
        $lp_listing_paid_claim_switchh = lp_theme_option('lp_listing_paid_claim_switch');
        $output .= '<div class="col-md-10 col-md-offset-1 padding-bottom-40 lp-margin-top-case ' . $pricing_views . '">';
        //Title and subtitle field optional
        if ($title_subtitle_show == 'show_hide') {
            $output .= '<div class="page-header">
						<h3>' . $title . '</h3>
						<p>' . $subtitle . '</p>
			</div>';
        } elseif ($lp_plans_cats == 'no') {
            $output .= '<div class="lp-no-title-subtitle">
            </div>';
        }
        if ($lp_listing_paid_claim_switchh == 'yes' && !is_front_page()) {
            $output .= '<div class="lp-no-title-subtitleeeeeeeee">
                ' . esc_html__("Choose a Plan to Claim Your Business", "listingpro-plugin") . '
         </div>';
        }
        if ($plan_status != 'claim') {
            if ($lp_plans_cats == 'yes') {

                ob_start();
                include_once(LISTINGPRO_PLUGIN_PATH . 'templates/pricing/by_category.php');
                $output .= ob_get_contents();
                ob_end_clean();
                ob_flush();
                if (in_array($pricing_views, $allowed_statuses, true)) {
                    ob_start();
                    include_once(LISTINGPRO_PLUGIN_PATH . "templates/pricing/" . $pricing_views . '.php');
                    $output .= ob_get_contents();
                    ob_end_clean();
                    ob_flush();
                }
            } else {

                if (in_array($pricing_views, $allowed_statuses, true)) {
                    ob_start();
                    include_once(LISTINGPRO_PLUGIN_PATH . "templates/pricing/" . $pricing_views . '.php');
                    $output .= ob_get_contents();
                    ob_end_clean();
                    ob_flush();
                }
            }
        } else {
            ob_start();
            include_once(LISTINGPRO_PLUGIN_PATH . 'templates/pricing/loop/claim_plans.php');
            $output .= ob_get_contents();
            ob_end_clean();
            ob_flush();
        }
        $output .= '	</div>';

        return $output;
    }
}
