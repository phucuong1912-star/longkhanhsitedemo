<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listing_Announcements extends Widget_Base {

    public function get_name() {
        return 'listing-announcements';
    }

    public function get_title() {
        return __( 'LP Announcements', 'elementor-listingpro' );
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
            'listings',
            [
				'label' => __( 'Select Listing', 'text-domain' ),
				'type' => \Elementor\Controls_Manager::SELECT2,
				'options' => $this->get_listing_options(),
				'multiple' => true,
            ]
        );
        $this->add_control(
            'number_posts',
            [
                'label' => __( 'Posts per page', 'elementor-listingpro' ),
                'type' => Controls_Manager::NUMBER,
                'default' => '3'
            ]
        );
        $this->end_controls_section();
    }
	// Function to retrieve a list of available listings
	private function get_listing_options() {
		$args = array(
			'post_type' => 'listing',
			'posts_per_page' => -1,
		);

		$listings = get_posts( $args );
		$options = array();

		foreach ( $listings as $listing ) {
			$options[ $listing->ID ] = $listing->post_title;
		}

		return $options;
	}
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_announcements( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_listing_announcements')) {
    function listingpro_shortcode_listing_announcements($atts, $content = null) {
        extract(shortcode_atts(array(
            'listings'   => 'listings',
            'number_posts'   => '3',
            'style'   => 'style_1',
        ), $atts));
		if( !is_array( $listings )){
			$listings = explode( ',', $listings);
		}
		$announcement_query =  new \WP_Query( array(
			'post__in' => $listings,
			'post_type' => 'listing'
		) );
		$announcement_count = 0;
		require_once (THEME_PATH . "/include/aq_resizer.php");
		echo '<div class="row lp_element_announcement">';
		if( $announcement_query->have_posts() ) {
			while ($announcement_query->have_posts()) : $announcement_query->the_post();
				$listing_discount_data = get_post_meta( get_the_ID(), 'listing_discount_data', true );
				$lp_listing_announcements_raw  =   get_post_meta( get_the_ID(), 'lp_listing_announcements', true );
				$lp_listing_announcements   =   array();
				if( $lp_listing_announcements_raw != '' && is_array( $lp_listing_announcements_raw ) && count($lp_listing_announcements_raw) > 0 ) {
					foreach ( $lp_listing_announcements_raw as $k => $v ) {
						if($v['annStatus']) {
							$lp_listing_announcements[] =   $v;
						}
					}
				}
				if( $lp_listing_announcements != '' && is_array( $lp_listing_announcements ) && count($lp_listing_announcements) > 0 ):
					if($announcement_count >= $number_posts ) {
						break;
					}
					?>
					<div class="tab-pane" id="announcements_tab">
						<div class="lp-listing-announcement">

							<?php
							foreach ( $lp_listing_announcements as $k => $v ):
								if( $v['annLI'] == get_the_ID() ):
									if( !isset( $v['annStatus'] ) || $v['annStatus'] == 1 ):
										$annSt  =   'style1';
										if( isset( $v['annSt'] ) && !empty( $v['annSt'] ) )
										{
											$annSt  =    $v['annSt'];
										}
										$icon_class =   'fa fa-bullhorn';

										if( !empty( $v['annIC'] ) )

										{

											$icon_class =   $v['annIC'];

										}
										?>
										<div class="announcement-wrap ann-<?php echo esc_attr( $annSt ); ?>">
											<i class="<?php echo esc_attr( $icon_class ); ?>" aria-hidden="true"></i>
											<p>
												<?php
												if( !empty( $v['annTI'] ) ):
													?>
													<strong><?php echo esc_attr( $v['annTI'] ); ?></strong>
												<?php endif; ?>
												<span><?php echo esc_attr( $v['annMsg'] ); ?></span>
											</p>
											<?php
											if( !empty( $v['annBT'] ) ):
												?>
												<a target="_blank" href="<?php echo esc_attr( $v['annBL'] ); ?>" class="announcement-btn"><?php echo esc_attr( $v['annBT'] ); ?></a>
											<?php endif; ?>
											<div class="clearfix"></div>
										</div>
									<?php endif; endif; endforeach; ?>
							<div class="clearfix"></div>
						</div>
					</div>
				<?php
					$announcement_count++;
				endif;
			endwhile;
			echo '</div>';
		}
    }
}
