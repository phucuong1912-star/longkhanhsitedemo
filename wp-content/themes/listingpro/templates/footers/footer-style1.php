<?php

/**
 * Template part for footer style 1.
 * templates/footers/footer-style1
 * @version 2.0
*/
?>
<footer class="text-center footer-style1">
	<?php
	if(has_nav_menu('footer_menu')){
		?>
		<div class="footer-upper-bar">
			<div class="container">
				<div class="row">
					<div class="col-md-12">
						<?php echo listingpro_footer_menu(); ?>
					</div>
				</div>
			</div>
		</div>
		<!-- /footer-upper-bar -->
	<?php } ?>
	<div class="footer-bottom-bar">
		<div class="container">
			<div class="row">
				<div class="col-md-12">
				 <!-- company info -->
				<?php get_template_part('templates/footers/company-info'); ?>
				<!-- social shares -->
				<?php get_template_part('templates/footers/footer-social'); ?>
				</div>
			</div>
		</div>
	</div>
	<!-- /footer-bottom-bar -->
</footer>