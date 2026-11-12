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

<!--footer 7-->
<div class="clearfix"></div>
<footer class="footer-style7 padding-top-60 padding-bottom-60">
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
        <div class="footer7-bottom-area">
            <div class="container">
                <div class="row">
                    <div class="col-md-6">
                       <?php
                        if( !empty( $copy_right ) ):
                    ?>
                        <span class="copyrights"><?php echo esc_attr($copy_right); ?></span>
                    
                    <?php endif; ?>
                    </div>

                    <div class="col-md-6">
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
                </div>
            </div>
        </div>
</footer>