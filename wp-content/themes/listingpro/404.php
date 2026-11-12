<?php
/**
 * The template for displaying 404 Error Page.
 * @version 2.0
 */
 
get_header();
?>

<!--==================================Section Open=================================-->
	<section>
		<div class="lp-section-row aliceblue text-center lp-section-content-container">
			<div class="container white mr-top-60 mr-bottom-60">
				<div class="row">
					<div class="col-xs-12 col-sm-12 col-md-12">
						<h2 class="page-404-title pg-404-tit mr-top-0 "><?php esc_html_e('4', 'listingpro'); ?><img alt="image" src="<?php echo get_template_directory_uri(); ?>/assets/images/404.png" /><?php esc_html_e('4', 'listingpro'); ?></h2>
						<p class="pg-404-p text-center padding-top-20"> <?php esc_html_e('Ooops', 'listingpro'); ?>, <?php esc_html_e('Ghost HERE', 'listingpro'); ?> </p>
					</div>	
				</div>	
				<div class="row">
					<div class="col-md-12 text-center pad-top-15 padding-bottom-30">
						<div class="row">
							<div class="col-md-4 col-md-offset-4">
								<p>	
									<?php esc_html_e('The page you are looking for might have been removed or is temporarily unavailable.', 'listingpro'); ?>...
								</p>
							</div>		
						</div>		
					</div>		
				</div>		
			</div>
		</div>
	</section>
	<!--==================================Section Close=================================-->

<?php
get_footer();
?>