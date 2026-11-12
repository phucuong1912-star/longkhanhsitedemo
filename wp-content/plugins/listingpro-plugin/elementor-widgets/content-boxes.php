<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Content_Boxes extends Widget_Base {

    public function get_name() {
        return 'content-boxes';
    }

    public function get_title() {
        return __( 'Content Boxes', 'elementor-listingpro' );
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
        $repeater = new \Elementor\Repeater();
        $repeater->add_control(
            'single_content_box_style', [
                'label' => __( 'Select Single Content Box Style', 'elementor-listingpro' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'classic',
                'options' => [
                    'classic' => __( 'Old Style', 'elementor-listingpro' ),
                    'moderen' => __( 'New Style', 'elementor-listingpro' ),
                ],
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'content_title', [
                'label' => __( 'Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( 'Planning' , 'plugin-domain' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'content_desc', [
                'label' => __( 'Content', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __( 'Sed ut perspiciatis unde omnis iste natus error sit v oluptatem accusantium or sit v oluptatem accusantiumor sit v oluptatem' , 'elementor-listingpro' ),
                'show_label' => true,
            ]
        );
        $repeater->add_control(
            'content_icon',
            [
                'label' => __( 'Social Icons', 'elementor-listingpro' ),
                'type' => Controls_Manager::ICON,
                'default' => 'fa fa-facebook',
            ]
        );
        $this->add_control(
            'content_boxes',
            [
                'label' => __( 'Repeater List', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'single_content_box_style' => 'classic',
                        'content_title' => __( 'Planning', 'elementor-listingpro' ),
                        'content_desc' => __( 'Sed ut perspiciatis unde omnis iste natus error sit v oluptatem accusantium or sit v oluptatem accusantiumor sit v oluptatem' , 'elementor-listingpro' ),
                        'content_icon' => 'fa fa-facebook',
                    ],
                ],
                'title_field' => '{{{ content_title }}}',
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
        ?>
        <div class="about-box-container">
            <div class="lp-section-content-container clearfix">
                <?php
                if( $settings['content_boxes'] )
                {
                    foreach ( $settings['content_boxes'] as $item )
                    {
                        $box_settings   =   array(
                            'single_content_box_style' =>  $item['single_content_box_style'],
                            'content_title' => $item['content_title'],
                            'content_desc' => $item['content_desc'],
                            'content_icon' => $item['content_icon'],
                            'from-elementor' => true
                        );
                        echo listingpro_shortcode_content_box( $box_settings );
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_content_box')) {
    function listingpro_shortcode_content_box($atts, $content = null) {
        extract(shortcode_atts(array(
            'single_content_box_style'   => 'classic',
            'content_title'   => 'PLANNING',
            'content_desc'   => 'Sed ut perspiciatis unde omnis iste natus error sit v oluptatem accusantium or sit v oluptatem accusantiumor sit v oluptatem',
            'content_icon'   => '',
        ), $atts));


        if(array_key_exists('from-elementor', $atts)) {
            $icon_class =   $content_icon;
        } else {
            $icon_class =   'fa fa-'.$content_icon;
        }

        $output = null;
        if($single_content_box_style == 'classic'){
            $output .= '<div class="col-md-3 col-sm-6 about-box text-center">
						<div class="about-box-inner lp-border-radius-5 lp-border">
							<div class="about-box-slide">
								<div class="about-box-icon">
									<!-- Inspection icon by Icons8 -->
									<i class="'.$icon_class.'"></i>
								</div>
								<div class="about-box-title clearfix">
									<h4>'.$content_title.'</h4>
								</div>
								<div class="about-box-description">
									<p class="paragraph-small">
										'.$content_desc.'
									</p>
								</div>
							</div>
						</div>
					</div>';
        }else{

            $output .= '<div class="col-md-4 col-sm-6 about-box about-box-style2">
						<div class="about-box-inner lp-border-radius-5 lp-border">
							<div class="about-box-slide">
								<div class="about-box-icon-style2">
									<!-- Inspection icon by Icons8 -->
									<i class="'.$icon_class.'"></i>
								</div>
								<div class="about-box-title-style2 clearfix">
									<h4>'.$content_title.'</h4>
								</div>
								<div class="about-box-description-style2">
									<p class="paragraph-small">
										'.$content_desc.'
									</p>
								</div>
							</div>
						</div>
					</div>';
        }

        return $output;
    }
}