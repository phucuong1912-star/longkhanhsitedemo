<?php
/**
 * Template name: Payment Cancel
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
 ?>
 <?php
	get_header();
 ?>
 <div class="container">
	 <div class="page-container-four clearfix">
		 <?php 
				if(has_shortcode( get_the_content(), 'vc_row' ) ) {
					the_content();
				}else{
					echo esc_html__('Oh Sorry! Your payment failed','listingpro');
				}
			
		 ?>
	</div>
</div>
	
	<!--==================================Section Close=================================-->
	<div class="md-overlay"></div>
	
<?php get_footer();?>