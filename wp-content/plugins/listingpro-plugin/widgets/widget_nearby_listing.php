<?php

/**
 * Extend Nearby Listing 
 *
 * Nearby Listing in sidebar
 */

class Nearby_listing_Widget extends WP_Widget
{
	function __construct()
	{
		parent::__construct(
			'listingPro_nearby', // Base ID
			'ListingPro - Nearby Listing', // Name
			array('description' => esc_html__('It will show nearby listings', 'listingpro'))
		);
	}
	function form($instance)
	{
		$title = '';
		if ($instance) {
			$title = esc_attr($instance['title']);
		} else {
			$title = '';
		}
?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Title', 'listingpro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>" />
		</p>
		<p>
			<?php echo esc_html__('Nearby listings will show in sidebar in "random" order', 'listingpro'); ?>
		</p>


	<?php
	}

	function widget($args, $instance)
	{
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		echo $before_widget;
		if ($title) {
			echo $before_title . $title . $after_title;
		}
		$TxQuery = array();
		global $post, $listingpro_options;
		$unit = $listingpro_options['listingpro_nearby_dest_in'];
		$curr_listing_id = $post->ID;
		$distance = $listingpro_options['listingpro_nearby_dest'];
		$noOfListing = $listingpro_options['listingpro_nearby_noposts'];
		$catFilter = $listingpro_options['listingpro_nearby_filter'];
		$nearbylistingArray = array();

		$calParam = $listingpro_options['listingpro_nearby_dest_in'];
		if (empty($calParam)) {
			$calParam = 'km';
		}
		$cat_id = array();
		if ($catFilter == "yes") {
			$category = get_the_terms($curr_listing_id, 'listing-category');
			if (!empty($category)) {
				foreach ($category as $ccat) {
					$cat_id[] = $ccat->term_id;
				}

				$TxQuery = array(
					array(
						'taxonomy' => 'listing-category',
						'field' => 'id',
						'terms' => $cat_id,
					),
				);
			}
		}

		if (empty($distance)) {
			$distance = 100;
		}
		if (empty($noOfListing)) {
			$noOfListing = 5;
		}

		global $listingpro_options;
		$listing_layout =   $listingpro_options['listing_views'];
	?>
		<?php
		if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') :
		?>
			<div class="lp-nearby paid-listing">
				<div class="listing-post">
				<?php
			else :
				?>
					<div class="lp-nearby paid-listing">
						<div class="listing-post">
						<?php
					endif;
						?>
						<?php

						$curr_lat = listing_get_metabox_by_ID('latitude', $curr_listing_id);
						$curr_long = listing_get_metabox_by_ID('longitude', $curr_listing_id);
						if (!empty($curr_lat) && !empty($curr_long) && $catFilter == "no") {
							$args = array(
								'post_type' => 'listing',
								'post_status' => 'publish',
								'posts_per_page' => -1,
								'post__not_in' => array($curr_listing_id),
								'tax_query' => $TxQuery,
								'orderby'        => 'post__in',
								'order'          => 'ASC'
							);
							$my_query = null;
							$my_query = new WP_Query($args);
							if ($my_query->have_posts()) {
								while ($my_query->have_posts()) : $my_query->the_post();
									$this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
									$this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
									if (!empty($this_lat) && !empty($this_long)) {

										$calDistance = GetDrivingDistance($curr_lat, $this_lat, $curr_long, $this_long, $unit);

										//New Update 2.7.0
										if ($calDistance['distance'] == 0) {
											$calDistance['distance'] = 0.1;
										}
										//End New Update 2.7.0
										if (!empty($calDistance['distance'])) {
											if ($calDistance['distance'] < $distance) {
												$nearbylistingArray[get_the_ID()] = $calDistance['distance'];
											}
										}
									}
								endwhile;
								wp_reset_postdata();


								$keysArrray = array();
								if (!empty($nearbylistingArray)) {
									asort($nearbylistingArray);
									foreach ($nearbylistingArray as $key => $val) {
										$keysArrray[] = $key;
									}

									$args = array(
										'post_type' => 'listing',
										'post_status' => 'publish',
										'posts_per_page' => $noOfListing,
										'post__in' => $keysArrray,
										'tax_query' => $TxQuery,
										'orderby'        => 'post__in',
										'order'          => 'ASC'
									);
									$my_query = null;
									$my_query = new WP_Query($args);
									if ($my_query->have_posts()) {
										while ($my_query->have_posts()) : $my_query->the_post();
											$this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
											$this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
											$calDistance = GetDrivingDistance($curr_lat, $this_lat, $curr_long, $this_long, $unit);

											//New Update 2.7.0
											if ($calDistance['distance'] == 0) {
												$calDistance['distance'] = 0.1;
											}
											//End New Update 2.7.0
											if (!empty($calDistance['distance'])) {
												$listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
												if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') {
													if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
														echo  '<div class="row lp-row-app">';
														get_template_part('mobile/listing-loop-app-view');
														echo  '<div class="lp-distance-sidebar">' . $calDistance['distance'] . ' ' . $calParam . '</div>';
														echo '</div>';
													} else {
														echo '<div class="lp-sidebar-nearby lp-sidebar-nearby-archive">';
														get_template_part('templates/details-page-nearby');
														echo  '<div class="lp-distance-sidebar">' . $calDistance['distance'] . ' ' . $calParam . '</div>';
														echo '</div>';
													}
												} else {
													echo '<div class="lp-sidebar-nearby">';

													if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
														echo  '<div class="row lp-row-app">';
														get_template_part('mobile/listing-loop-app-view');
														echo '</div>';
													} else {
														get_template_part('templates/details-page-nearby');
													}
													echo  '<div class="lp-distance-sidebar">' . $calDistance['distance'] . ' ' . $calParam . '</div>';
													echo '</div>';
												}
											}
										endwhile;
										wp_reset_postdata();
									}
								}
							}
						} elseif (!empty($curr_lat) && !empty($curr_long) && $catFilter == "yes") {
							/* current cat */
							$args = array(
								'post_type' => 'listing',
								'post_status' => 'publish',
								'posts_per_page' => -1,
								'post__not_in' => array($curr_listing_id),
								'tax_query' => $TxQuery
							);
							$my_query = null;
							$my_query = new WP_Query($args);
							if ($my_query->have_posts()) {
								while ($my_query->have_posts()) : $my_query->the_post();

									$this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
									$this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
									if (!empty($this_lat) && !empty($this_long)) {
										$calDistance = GetDrivingDistance($curr_lat, $this_lat, $curr_long, $this_long, $unit);
										if (!empty($calDistance['distance']) && $noOfListing > 0) {
											if ($calDistance['distance'] < $distance) {
												$nearbylistingArray[get_the_ID()] = $calDistance['distance'];
											}
										}
									}

								endwhile;
								wp_reset_postdata();

								$keysArrray = array();
								if (!empty($nearbylistingArray)) {
									asort($nearbylistingArray);
									foreach ($nearbylistingArray as $key => $val) {
										$keysArrray[] = $key;
									}

									$args = array(
										'post_type' => 'listing',
										'post_status' => 'publish',
										'posts_per_page' => $noOfListing,
										'post__in' => $keysArrray,
										'orderby'        => 'post__in',
										'order'          => 'ASC'
									);
									$my_query = null;
									$my_query = new WP_Query($args);
									if ($my_query->have_posts()) {
										while ($my_query->have_posts()) : $my_query->the_post();
											$this_lat = listing_get_metabox_by_ID('latitude', get_the_ID());
											$this_long = listing_get_metabox_by_ID('longitude', get_the_ID());
											$calDistance = GetDrivingDistance($curr_lat, $this_lat, $curr_long, $this_long, $unit);
											//New Update 2.7.0
											if ($calDistance['distance'] == 0) {
												$calDistance['distance'] = 0.1;
											}
											//End New Update 2.7.0
											if (!empty($calDistance['distance'])) {
												echo '<div class="lp-sidebar-nearby">';


												global $listingpro_options;
												$listing_mobile_view    =   $listingpro_options['single_listing_mobile_view'];
												if ($listing_mobile_view == 'app_view' && wp_is_mobile()) {
													echo  '<div class="row lp-row-app">';
													get_template_part('mobile/listing-loop-app-view');
													echo '</div>';
												} else {
													get_template_part('templates/details-page-nearby');
												}
												echo  '<div class="lp-distance-sidebar">' . $calDistance['distance'] . ' ' . $calParam . '</div>';
												echo '</div>';
											}
										endwhile;
										wp_reset_postdata();
									}
								}
							}
						}

						?>
						<?php
						if ($listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2') :
						?>
						</div>
					</div>
				<?php
						else :
				?>
				</div>
			</div>
		<?php
						endif;
		?>
<?php

		echo $after_widget;
	}



	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
	}
} //end class featured_ads_Widget

if (!function_exists('listingPro_nearby_Widget_registration')) {
	function listingPro_nearby_Widget_registration()
	{
		register_widget('Nearby_listing_Widget');
	}
}
add_action('widgets_init', 'listingPro_nearby_Widget_registration');
