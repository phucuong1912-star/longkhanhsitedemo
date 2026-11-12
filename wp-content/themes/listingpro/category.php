<?php
/**
 * The template for displaying Category Page.
 * @version 2.0
 */
 
 get_header();
$blog_template  =   lp_theme_option('blog_template');
$blog_sidebar_style   =   lp_theme_option('blog_sidebar_style');
if( $blog_template == 'right_sidebar' || $blog_template == 'left_sidebar' ){
	$archiveColClass = "col-lg-8 col-md-8";
}else{
	$archiveColClass = "col-lg-12 col-md-12";
}
if( $blog_sidebar_style == 'sidebar_style1' ){
	$sidebarClass = "sidebar-style1";
}else{
	$sidebarClass = "sidebar-style2";
}
 ?>
	<!--==================================Section Open=================================-->
    <section class="aliceblue lp-blog-for-app-view">
		<div class="container page-container-five">
			<div class="row">
							
				<!--=======Left sidebar=========-->
				<?php if( $blog_template == 'left_sidebar'){ ?>
				<div class="col-md-4">
					<div class="<?php echo esc_attr($sidebarClass); ?> listing-second-view">
						<?php get_sidebar(); ?>
					</div>
				</div>
				<?php } ?>
				<!--=======Content area=========-->
				<div class="<?php echo esc_attr($archiveColClass); ?> blog-content-container">
				<?php get_template_part( 'loop', 'archive' ); ?>
				</div>
				<!--=======Right sidebar=========-->
				<?php if( $blog_template == 'right_sidebar'){ ?>
				<div class="col-md-4">
					<div class="<?php echo esc_attr($sidebarClass); ?> listing-second-view">
						<?php get_sidebar(); ?>
					</div>
				</div>
				<?php } ?>

			</div>
		</div>
	</section>
	<!--==================================Section Close=================================-->

<?php get_footer();?>