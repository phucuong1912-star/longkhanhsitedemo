<?php 
/**
 * Template name: Page without sidebar
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 */
get_header();
the_post();
$elementor_page = get_post_meta( get_the_ID(), '_elementor_edit_mode', true );
if (!empty($elementor_page) ) {
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
					<div class="col-md-12 col-sm-12">
                        <div class="blog-post clearfix">
                            <div class="post-content blog-test-page">
                            <?php the_content(); ?>
                            </div>
                        </div>
					</div>
					<!-- content close -->				
				</div>
			</div>
	</div>
	<?php
}
get_footer(); ?>