<?php get_header();?>

<?php

    global $listingpro_options;
    $blog_template                      = isset($listingpro_options['blog_template']) ? $listingpro_options['blog_template'] : '';
    $blog_sidebar_style                 = isset($listingpro_options['blog_sidebar_style']) ? $listingpro_options['blog_sidebar_style'] : '';
?>
	<!--==================================Section Open=================================-->
	<section class="aliceblue lp-blog-for-app-view">
		<div class="container page-container-five">
			<div class="row">
                <?php
                if( $blog_template == 'left_sidebar' )
                {
                    
					if( $blog_sidebar_style == 'sidebar_style1' ){
                        echo '<div class="col-lg-4 col-md-4 sidebar-container"><div class="sidebar-style1 listing-second-view">';
                            get_sidebar();
                        echo '</div></div>';
						}else{
							 echo '<div class="col-lg-4 col-md-4 sidebar-container"><div class="sidebar-style2 listing-second-view">';
							get_sidebar();
							echo '</div></div>';
							
						}
                }
                if( $blog_template == 'right_sidebar' || $blog_template == 'left_sidebar' )
                {
                    echo '<div class="col-lg-8 col-md-8 blog-content-container"><div class="row">';
                }
                ?>
				<?php get_template_part( 'loop', 'archive' ); ?>
                <?php
                if( $blog_template == 'right_sidebar' || $blog_template == 'left_sidebar' )
                {
                    echo '</div></div>';
                }
                if( $blog_template == 'right_sidebar' )
                {
                   if( $blog_sidebar_style == 'sidebar_style1' ){
                        echo '<div class="col-lg-4 col-md-4 listing-second-view"><div class="sidebar-style1 listing-second-view">';
                            get_sidebar();
                        echo '</div></div>';
						}else{
							 echo '<div class="col-lg-4 col-md-4 listing-second-view"><div class="sidebar-style2 listing-second-view">';
							get_sidebar();
							echo '</div></div>';
							
						}
                }
                ?>
			</div>
		</div>
	</section>
	<!--==================================Section Close=================================-->

<?php get_footer();?>