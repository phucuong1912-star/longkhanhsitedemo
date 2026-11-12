<?php
/**
 * Extend Recent Posts Widget 
 *
 * Adds different formatting to the default WordPress Recent Posts Widget
 */

class featured_ads_Widget extends WP_Widget{
function __construct() {
	parent::__construct(
		'listingPro_ads_widget', // Base ID
		'ListingPro - Ads Listing', // Name
		array('description' => esc_html__('It will show the Listings in ads', 'listingpro') )
   	);
}
function form($instance) {
	$numberOfListings = '';
	$title = '';
	if( $instance) {
		$title = esc_attr($instance['title']);
		$numberOfListings = esc_attr($instance['numberOfListings']);

	} else {
		$title = '';
		$numberOfListings = '';


	}
	?>
		<p>
			<label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo esc_html__('Title', 'listingpro'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo $title; ?>" />
		</p>				<p>			<label for="<?php echo esc_attr($this->get_field_id('numberOfListings')); ?>"><?php echo esc_html__('Number of Listings:', 'listingpro'); ?></label>			<input class="widefat" id="<?php echo $this->get_field_id('numberOfListings'); ?>" name="<?php echo $this->get_field_name('numberOfListings'); ?>" type="text" value="<?php echo $numberOfListings; ?>" />			<!-- <select id="<?php echo $this->get_field_id('numberOfListings'); ?>"  name="<?php echo $this->get_field_name('numberOfListings'); ?>">				<?php for($x=1;$x<=10;$x++): ?>				<option <?php echo $x == $numberOfListings ? 'selected="selected"' : '';?> value="<?php echo $x;?>"><?php echo $x; ?></option>				<?php endfor;?>			</select> -->		</p>
		<p>
			<?php echo esc_html__('Ads listings will show in sidebar in "random" order', 'listingpro'); ?>
		</p>
        
       
	<?php
}

function widget($args, $instance) {
	extract( $args );
	$title = apply_filters('widget_title', $instance['title']);
	$numberOfListings = '';
	if(isset($instance['numberOfListings']) && !empty($instance['numberOfListings'])){
		$numberOfListings = $instance['numberOfListings'];
	}	
	echo $before_widget;

	if ( $title ) {
		echo $before_title . $title . $after_title;
	}
	$s = '';
	global $post;
	global $listingpro_options;
    $listing_layout =   $listingpro_options['listing_views'];
	?>
	<?php
	   if( $listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2' ):
	   ?>
           <div class="lp-listings-widget listing-second-view">
           <div class="listing-post clearfix">
	   <?php
	   else:
	   ?>
		   <div class=" paid-listing listing-second-view">
		   <div class="listing-post clearfix">
	   <?php
	   endif;
	   ?>
			<?php
			$GLOBALS['sidebar_add_loop']    =   'yes';
			$listing_cats = array();
			$terms = get_the_terms( $post, 'listing-category' );
			if ( $terms && ! is_wp_error( $terms ) ){
				foreach ( $terms as $term ) {
					$listing_cats[] = $term->term_id;
				}
			}
			
			$taxQuery = array(
								'taxonomy' => 'listing-category',
								'field'    => 'term_id',
								'terms'    => $listing_cats,
							);
			$data = listingpro_get_campaigns_listing_sidebar( 'lp_detail_page_ads', false, array(), array(),array() ,$s, $numberOfListings, null);
			print_r($data);
			wp_reset_postdata();
			?>

		<?php
		if( $listing_layout == 'list_view_v2' || $listing_layout == 'grid_view_v2' ):
		?>
		   </div>
            </div>
		<?php
		else:
		?>
		   </div>
		   </div>
		<?php
		endif;
		?>
	<?php

	echo $after_widget;
}



function update($new_instance, $old_instance) {
	$instance = $old_instance;
	$instance['title'] = strip_tags($new_instance['title']);	$instance['numberOfListings'] = strip_tags($new_instance['numberOfListings']);
	return $instance;
}
 
 
} //end class featured_ads_Widget

if(!function_exists('featured_ads_Widget_registration')) {
    function featured_ads_Widget_registration()
    {
        register_widget('featured_ads_Widget');
    }
}
add_action('widgets_init', 'featured_ads_Widget_registration');