<?php
global $listingpro_options;
	$copy_right = $listingpro_options['copy_right'];
	$footer_logo    =   $listingpro_options['footer_logo'];
    $footer_address = $listingpro_options['footer_address'];
    $phone_number = $listingpro_options['phone_number'];
    $author_info = $listingpro_options['author_info'];
    $fb = $listingpro_options['fb'];
    $tw = $listingpro_options['tw'];
    $insta = $listingpro_options['insta'];
    $tumb = $listingpro_options['tumb'];
    $fyout = $listingpro_options['f-yout'];
    $flinked = $listingpro_options['f-linked'];
    $fpintereset = $listingpro_options['f-pintereset'];
    $fvk = $listingpro_options['f-vk'];
    $footer_style = $listingpro_options['footer_style'];

?>

<div class="clearfix"></div>
    <footer class="footer-style4">
    	<div class="padding-top-60 padding-bottom-60">
	        <div class="container">
	            <div class="row">
	                <?php
	                    $grid = $listingpro_options['footer_layout'] ? $listingpro_options['footer_layout'] : '12';

	                    $i = 1;
	                    foreach (explode('-', $grid) as $g) {
	                        echo '<div class="clearfix col-md-' . $g . ' col-' . $i . '">';
	                        dynamic_sidebar("footer-sidebar-$i");
	                        echo '</div>';
	                        $i++;
	                    }

	                ?>
	            </div>
	        </div>
    	</div>

    	<div class="footer4-bottom-area lp-footer-bootom-border">
    		<div class="container">
    			<div class="row">
    				
    				<div class="col-md-2">
    					<div class="lp-footer-logo">
		                    <a href="<?php echo esc_url(home_url('/')); ?>">
		                        <?php
		                        if( isset( $footer_logo ) && !empty( $footer_logo ) ):
		                        ?>
		                        <img src="<?php echo esc_url($footer_logo['url']); ?>" alt="image">
		                        <?php endif; ?>
		                    </a>
                		</div>
                	</div>
                	
            		<div class="col-md-10">
		                <div class="footer-menu">
		                    <?php listingpro_footer_menu(); ?>
		                </div>
        			</div>
        			<?php
		                if( !empty( $copy_right ) ):
		            ?>
        			<div class="col-md-12 lp-footer4-copyrights">
		                <span class="copyrights"><?php echo esc_attr($copy_right); ?></span>
                	</div>
                	<?php endif; ?>
    			</div>
    		</div>
    	</div>
    </footer>