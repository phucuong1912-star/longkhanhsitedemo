<?php
/**
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage
 * @since 
 */

					$type = 'listing';
					global $paged;
					$args=array(
						'post_type' => $type,
						'post_status' => 'publish',
						'posts_per_page' => 12,
						'paged'       => $paged,
						'author' => get_queried_object_id(), 
					);
					
					$my_query = null;
					$my_query = new WP_Query($args);
get_header(); 
?>
	<section>
		<div class="container page-container margin-top-80">
			<div class="row lp-list-page-grid" id="content-grids" >
				<?php
					if( $my_query->have_posts() ) {
						while ($my_query->have_posts()) : $my_query->the_post();  
							get_template_part( 'listing-loop' );
						endwhile;
					}
				?>
				<div class="md-overlay"></div>
			</div>
		</div>
	</section>


<?php get_footer(); ?>