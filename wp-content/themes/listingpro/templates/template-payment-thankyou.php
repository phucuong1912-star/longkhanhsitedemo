<?php
/**
 * Template name: Payment Thankyou
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
 ?>
 <?php
	get_header();
	if (!isset($_SESSION)) { session_start(); }
 ?>
 <div class="container">
	 <div class="page-container-four clearfix">
		 <?php 
		 
			if( !empty($_SESSION['wire_invoice']) ){
				$output = $_SESSION['wire_invoice'];	
				echo wp_kses_post($output);
				unset( $_SESSION['wire_invoice'] );
			}
			
			else{
				if(has_shortcode( get_the_content(), 'vc_row' ) ) {
					the_content();
				}else{
					echo esc_html__('Congratulations! Your payment has been made successfull','listingpro');
					the_content();
				}
			}
			
		 ?>
	 </div>
 </div>
	<!--==================================Section Close=================================-->
	<div class="md-overlay"></div>
	
<?php get_footer();?>