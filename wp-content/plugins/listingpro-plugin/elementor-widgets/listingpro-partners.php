<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Partners extends Widget_Base {

    public function get_name() {
        return 'listingpro-partners';
    }

    public function get_title() {
        return __( 'Listingpro Partners', 'elementor-listingpro' );
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
            'p_image1_url', [
                'label' => __( 'Logo Url', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => __( '#' , 'elementor-listingpro' ),
                'label_block' => true,
            ]
        );
        $repeater->add_control(
            'p_image1', [
                'label' => __( 'Partner logo', 'elementor-listingpro' ),
                'type' => Controls_Manager::MEDIA,
                'show_label' => true,
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
                        'p_image1_url' => '',
                        'p_image1' => '#',
                    ],
                ],
                'title_field' => '{{{ p_image1_url }}}',
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
        <div class="travel-brands padding-bottom-30 padding-top-30">
            <div class="row">
                <?php
                if( $settings['content_boxes'] )
                {
                    foreach ( $settings['content_boxes'] as $item )
                    {
                        $box_settings   =   array(
                            'p_image1_url' =>  $item['p_image1_url'],
                            'p_image1' => $item['p_image1'],
                        );
                        echo listingpro_shortcode_listingpro_partner( $box_settings );
                    }
                }
                ?>
            </div>
        </div>
        <?php

//        if ( $settings['content_boxes'] ) {
//            echo '<dl>';
//            foreach (  $settings['content_boxes'] as $item ) {
//                echo '<dt class="elementor-repeater-item1-' . $item['_id'] . '">' . $item['single_content_box_style'] . '</dt>';
//                echo '<dt class="elementor-repeater-item3-' . $item['_id'] . '">' . $item['content_title'] . '</dt>';
//                echo '<dt class="elementor-repeater-item2-' . $item['_id'] . '">' . $item['content_desc'] . '</dt>';
//            }
//            echo '</dl>';
//        }
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listingpro_partner')) {
    function listingpro_shortcode_listingpro_partner($atts, $content = null) {
        extract(shortcode_atts(array(
            'p_image1'		=> '',
            'p_image1_url'		=> '',
        ), $atts));

        $output = null;

        $pimahe1 = '';
        if ( $p_image1 ) {
            if( is_array( $p_image1 ) )
            {
                $p_image1   =   $p_image1['id'];
            }
            $imgurl = wp_get_attachment_image_src( $p_image1, 'full');

            if($imgurl){
                $thumbnail = $imgurl[0];
            }else{
                $thumbnail = 'https://placehold.co/570x228';
            }
        }
        $output .= '<div class="col-md-2 partner-box text-center">
					<div class="partner-box-inner">
						<div class="partner-image">
							<a href="'.$p_image1_url.'"><img src="'.$thumbnail.'" alt="image" /></a>
						</div>
					</div>
				</div>';
        return $output;
    }
}