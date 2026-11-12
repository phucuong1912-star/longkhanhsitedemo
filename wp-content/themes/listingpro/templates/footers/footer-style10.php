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
    
    $about_heading = $listingpro_options['about_heading'];
    $about_description = $listingpro_options['about_description'];
    $footer_contact_us_url = $listingpro_options['footer_contact_us_url'];
    $footer_contact_us = $listingpro_options['footer_contact_us'];

?>

<!--footer 10-->
<div class="clearfix"></div>
<footer class="footer-style10">
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

        <div class="footer10-bottom-area lp-footer-bootom-border">
            <div class="container">
                <div class="row">
                    
                    <?php if(!empty($footer_logo['url'])){ ?>
                    <div class="col-md-3">
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
                    <?php } ?>
                    
                    <div class="col-md-6">
                        <div class="footer-aboutus">
                            <span><?php echo esc_attr($about_heading); ?></span>
                            <p><?php echo esc_attr($about_description); ?></p>
                        </div>
                       
                    </div>
                    <div class="col-md-3">
                        <a href="<?php echo esc_url($footer_contact_us_url); ?>" target="_blank" id="footer-contact-us"><?php echo esc_attr($footer_contact_us); ?></a>
                    </div>
                   
                </div>
            </div>
        </div>
</footer>