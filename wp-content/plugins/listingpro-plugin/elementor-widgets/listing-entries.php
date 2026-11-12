<?php

namespace ElementorListingpro\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Listing_Entries extends Widget_Base
{

    public function get_name()
    {
        return 'listing-entries';
    }

    public function get_title()
    {
        return __('Listing Entries', 'elementor-listingpro');
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
            'number_posts',
            [
                'label' => __('Posts per page', 'elementor-listingpro'),
                'type' => Controls_Manager::TEXT,
                'default' => '3',
            ]
        );

        //new code 2.6.15
        $this->add_control(
            'listing_orders',
            [
                'label' => __('Listings Order By', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'options' => [
                    'listing_order_date' => __('Date', 'elementor-listingpro'),
                    'listing_order_ran' => __('Random', 'elementor-listingpro'),
                ],
                'default' => 'listing_order_date'
            ]
        );
        //end new code 2.6.15

        $this->end_controls_section();

        $this->start_controls_section(
            'section_style',
            [
                'label' => __('Style', 'elementor-listingpro'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        $this->add_control(
            'text_transform',
            [
                'label' => __('Text Transform', 'elementor-listingpro'),
                'type' => Controls_Manager::SELECT,
                'default' => '',
                'options' => [
                    '' => __('None', 'elementor-hello-world'),
                    'uppercase' => __('UPPERCASE', 'elementor-listingpro'),
                    'lowercase' => __('lowercase', 'elementor-listingpro'),
                    'capitalize' => __('Capitalize', 'elementor-listingpro'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .title' => 'text-transform: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }
    protected function render()
    {
        $settings = $this->get_settings_for_display();
        echo listingpro_shortcode_listing_entries($settings);
    }
    protected function content_template()
    {
    }
    public function render_plain_content()
    {
    }
}
if (!function_exists('listingpro_shortcode_listing_entries')) {
    function listingpro_shortcode_listing_entries($atts, $content = null)
    {
        extract(shortcode_atts(array(
            'number_posts'   => '3',
            'listing_orders' => 'listing_order_date'
        ), $atts));

        $output = null;
        $type = 'listing';

        //new code 2.6.15
        $order_listings = '';
        if ($listing_orders == 'listing_order_ran') {
            $order_listings = 'rand';
        } else if ($listing_orders == 'listing_order_date') {
            $order_listings = 'date';
        }

        $args = array(
            'post_type' => $type,
            'post_status' => 'publish',
            'posts_per_page' => $number_posts,
            'orderby' => $order_listings,
        );

        //end new code 2.6.15

        $listingcurrency = '';
        $listingprice = '';
        $listing_query = null;
        $listing_query = new \WP_Query($args);

        global $listingpro_options;
        $listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
        $img_url    =     $listingpro_options['lp_def_featured_image']['url'];
        if ($listing_mobile_view == 'app_view2' && wp_is_mobile()) {
            ob_start();
            if ($listing_query->have_posts()) {
                $listing_entries_counter    =   1;
                while ($listing_query->have_posts()) : $listing_query->the_post();
                    if ($listing_entries_counter == 1) {
                        echo '<div class="app-view2-first-recent">';
                        get_template_part('mobile/listing-loop-app-view-adds');
                        echo '</div>';
                    } else {
                        get_template_part('mobile/listing-loop-app-view-new');
                    }
                    $listing_entries_counter++;
                endwhile;
            } else {
                echo 'no listings found';
            }
            $output .= ob_get_contents();
            ob_end_clean();
            ob_flush();
        } else {
            $post_count = 1;
            $output .= '
	<div class="listing-second-view paid-listing lp-section-content-container lp-list-page-grid">
		<div class="listing-post">
			<div class="row">';
            if ($listing_query->have_posts()) {
                while ($listing_query->have_posts()) : $listing_query->the_post();
                    $phone = listing_get_metabox('phone');
                    $website = listing_get_metabox('website');
                    $email = listing_get_metabox('email');
                    $latitude = listing_get_metabox('latitude');
                    $longitude = listing_get_metabox('longitude');
                    $gAddress = listing_get_metabox('gAddress');
                    $priceRange = listing_get_metabox('price_status');
                    $listingpTo = listing_get_metabox('list_price_to');
                    $listingprice = listing_get_metabox('list_price');
                    $isfavouriteicon = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = true);
                    $isfavouritetext = listingpro_is_favourite_grids(get_the_ID(), $onlyicon = false);
                    $claimed_section = listing_get_metabox('claimed_section');
                    $rating = get_post_meta(get_the_ID(), 'listing_rate', true);
                    $rate = $rating;

                    $output .= '
						<div class="col-md-4 col-sm-4 col-xs-12">
							<article>
								<figure>';
                    if (has_post_thumbnail()) {
                        $image = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'listingpro-blog-grid');
                        if (!empty($image[0])) {
                            $output .= '
												<a href="' . get_the_permalink() . '" >
													<img alt="image" src="' . $image[0] . '" />
												</a>';
                        } else {
                            $output .= '
												<a href="' . get_the_permalink() . '" >
													<img src="' . esc_html__('https://placehold.co/372x240', 'listingpro-plugin') . '" alt="image">
												</a>';
                        }
                    } else {
                        $output .= '
										<a href="' . get_the_permalink() . '" >
											<img src="' . $img_url . '" alt="image">
										</a>';
                    }
                    $output .= '
									<figcaption>';
                    if (!empty($listingprice)) {
                        $output .= '
											<div class="listing-price">';
                        $output .= esc_html($listingprice);
                        if (!empty($listingpTo)) {
                            $output .= ' - ';
                            $output .= esc_html($listingpTo);
                        }
                        $output .= '
											</div>';
                    }
                    $output .= '
										<div class="bottom-area">
											<div class="listing-cats">';
                    $cats = get_the_terms(get_the_ID(), 'listing-category');
                    if (!empty($cats)) {
                        foreach ($cats as $cat) {
                            $term_link = get_term_link($cat);
                            $output .= '
														<a href="' . $term_link . '">
															' . $cat->name . '
														</a>';
                        }
                    }
                    $output .= '
											</div>';
                    if (!empty($rate)) {
                        $output .= '
												<span class="rate">' . $rate . '<sup>/5</sup></span>';
                    }
                    $output .= '
											<h4>
												<a href="' . get_the_permalink() . '">
													' . mb_substr(get_the_title(), 0, 40) . '
												</a>
											</h4>';
                    if (!empty($gAddress)) {
                        $output .= '
												<div class="listing-location">
													<p>' . $gAddress . '</p>
												</div>';
                    }
                    $output .= '
										</div>
									</figcaption>
								</figure>
							</article>
						</div>';
                    if ($post_count == 3) {
                        $output .= '<div class="clearfix"></div>';
                        $post_count = 1;
                    } else {
                        $post_count++;
                    }
                endwhile;
            }
            $output .= '
			</div>
		</div>
	</div>';
        }



        return $output;
    }
}
