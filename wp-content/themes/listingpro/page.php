<?php 
get_header();
the_post();
$elementor_page = get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
if(has_shortcode( get_the_content(), 'vc_row' ) || has_shortcode( get_the_content(), 'listingpro_submit' ) || has_shortcode( get_the_content(), 'listingpro_pricing' ) || is_front_page()) {
	
	if(has_shortcode( get_the_content(), 'vc_row' ) && has_shortcode( get_the_content(), 'listingpro_promotional' )){
		?>
		<section class="aliceblue lp-blog-for-app-view">
				<?php the_content(); ?>
		</section>
		 <?php
	}else{
		?>
	 <section>
		<?php the_content(); ?>
	 </section>
<?php
    }


    }elseif (!empty($elementor_page) ) {
    ?>
    <section>
        <?php the_content(); ?>
    </section>
    <?php
}else{
    ?>
	<!-- section-contianer open -->
	<div class="section-contianer">
		
			<div class="container page-container-five">
				<!-- page title close -->
				<div class="row">
					<!-- content open -->
					<div class="col-md-8 col-sm-8">
						<div class="blog-post clearfix">
						<div class="post-content blog-test-page">
							<?php the_content(); ?>
						</div>
						<?php wp_link_pages(); ?>
						<?php comments_template('', true); ?>
					</div>
					</div>
					<!-- content close -->
					<!-- sider open -->
					<div class="col-md-4 col-sm-4 listing-second-view">
						<div class="side-bar">
						<?php get_template_part("sidebar");?>
						</div>
					</div>
					
				</div>
			</div>
		
	</div>
					
					
				
		
				
	

	<?php
}
get_footer(); ?>