<?php
namespace ElementorListingpro\Widgets;
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Listingpro_Events extends Widget_Base {

    public function get_name() {
        return 'listingpro-evnets';
    }

    public function get_title() {
        return __( 'Events', 'elementor-listingpro' );
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
            'number_events',
            [
                'label' => __( 'Number of Activities', 'elementor-listingpro' ),
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
                    '-1' => __( 'all', 'elementor-listingpro' ),
                ],
                'default' => '3'
            ]
        );
        $this->end_controls_section();
    }
    protected function render() {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_lp_events( $settings );
    }
    protected function content_template() {}
    public function render_plain_content() {}
}
if(!function_exists('listingpro_shortcode_lp_events')) {
    function listingpro_shortcode_lp_events($atts, $content = null) {
        extract(shortcode_atts(array(
            'number_events'   => '3',
        ), $atts));
        $output = null;
        $time_now   =   strtotime("-1 day");
        $args   =   array(
            'post_type' => 'events',
            'posts_per_page' => $number_events,
            'meta_key'   => 'event-date',
            'orderby'    => 'meta_value_num',
            'order'      => 'ASC',
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key'     => 'event-date',
                    'value'   => $time_now,
                    'compare' => '>',
                ),
                array(
                    'key'     => 'event-date-e',
                    'value'   => $time_now,
                    'compare' => '>',
                ),
            ),
        );

        $lp_events  =   new \WP_Query( $args );
        ob_start();
        ?>
        <div class="lp-section-content-container listingcampaings">
            <div class="lp-listings grid-style">
                <div class="row">
                    <?php
                    if( $lp_events->have_posts() ):
                        $event_counter  =   0;
                        ?>
                        <div class="events-element-content-area-wrap" data-num="<?php echo $lp_events->post_count; ?>">
                            <?php
                            while ( $lp_events->have_posts() ): $lp_events->the_post();
                                $event_counter++;
                                get_template_part( 'templates/loop-events' );
                                if($event_counter % 3 == 0) {
                                    echo '<div class="clearfix"></div>';
                                }
                            endwhile; wp_reset_postdata();
                            ?>
                        </div>
                        <?php
                    else:
                        echo  '<p>'.esc_html__( 'No Events Found', 'listingpro-plugin' ).'</p>';
                    endif;
                    ?>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}