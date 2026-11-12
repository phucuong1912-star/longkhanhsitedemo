<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Columns_Element extends Widget_Base {

    public function get_name() {
        return 'listingpro-columns-element';
    }

    public function get_title() {
        return __( 'Columns Element', 'elementor-listingpro' );
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
            'listing_cols_left_img',
            [
                'label' => __( 'Column Left Image', 'elementor-listingpro' ),
                'type' => \Elementor\Controls_Manager::MEDIA,
            ]
        );
        $this->add_control(
            'listing_first_col_title',
            [
                'label' => __( 'First Column Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => '1- Claimed'
            ]
        );
        $this->add_control(
            'listing_first_col_desc',
            [
                'label' => __( 'First Column Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Best way to start managing your business listing is by claiming it so you can update.', 'elementor-listingpro' )
            ]
        );
        $this->add_control(
            'listing_second_col_title',
            [
                'label' => __( 'Second Column Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( '2- Promote', 'elementor-listingpro' )
            ]
        );
        $this->add_control(
            'listing_second_col_desc',
            [
                'label' => __( 'Second Column Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Promote your business to target customers who need your services or products.', 'elementor-listingpro' )
            ]
        );
        $this->add_control(
            'listing_third_col_title',
            [
                'label' => __( 'Third Column Title', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( '3- Convert', 'elementor-listingpro' )
            ]
        );
        $this->add_control(
            'listing_third_col_desc',
            [
                'label' => __( 'Third Column Description', 'elementor-listingpro' ),
                'type' => Controls_Manager::TEXT,
                'default' => esc_html__( 'Turn your visitors into paying customers with exciting offers and services on your page.', 'elementor-listingpro' )
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
        echo listingpro_shortcode_columns( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}

if(!function_exists('listingpro_shortcode_columns')) {
    function listingpro_shortcode_columns($atts, $content = null) {
        extract(shortcode_atts(array(
            'listing_cols_left_img'      => get_template_directory_uri()."/assets/images/columns.png",
            'listing_first_col_title'    => 'Claimed',
            'listing_first_col_desc'     => 'Best way to start managing your business listing is by claiming it so you can update.',
            'listing_second_col_title' 	 => 'Promote',
            'listing_second_col_desc' 	 => 'Promote your business to target customers who need your services or products.',
            'listing_third_col_title' 	 => 'Convert',
            'listing_third_col_desc' 	 => 'Turn your visitors into paying customers with exciting offers and services on your page.',
        ), $atts));

        $output = null;

        $leftImg = '';
        if (!empty($listing_cols_left_img)) {
            if( is_array( $listing_cols_left_img ) )
            {
                $listing_cols_left_img  =   $listing_cols_left_img['id'];
            }
            $bgImage = wp_get_attachment_image_src( $listing_cols_left_img, 'full' );
            if (isset($bgImage[0])) {
                $leftImg = '<img src="'.$bgImage[0].'" alt="image">';
            }
        }else{
            $leftImg = '';
        }

        $output .='
	<div class="promotional-element listingpro-columns">
		<div class="listingpro-row padding-top-60 padding-bottom-60">
			<div class="promotiona-col-left">
				'.$leftImg.'
			</div>
			<div class="promotiona-col-right">
				<article>
					<h3><i class="fa-solid fa-1"></i>'.$listing_first_col_title.'</h3>
					<p>'.$listing_first_col_desc.'</p>
				</article>
				<article>
					<h3><i class="fa-solid fa-2"></i>'.$listing_second_col_title.'</h3>
					<p>'.$listing_second_col_desc.'</p>
				</article>
				<article>
					<h3><i class="fa-solid fa-3"></i>'.$listing_third_col_title.'</h3>
					<p>'.$listing_third_col_desc.'</p>
				</article>
			</div>
		</div>
	</div>';

        return $output;
    }
}