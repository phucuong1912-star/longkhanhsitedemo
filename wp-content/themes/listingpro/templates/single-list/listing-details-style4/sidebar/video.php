<?php
if( isset( $video ) && $video_show == true ):
    ?>
    <div class="lp-widget <?php if( $lp_detail_page_styles == 'lp_detail_page_styles3' ){ echo 'hide'; } ?> " id="lp-sidebar-video">
        <?php if(wp_oembed_get( $video )){?>
            <?php echo wp_oembed_get($video); ?>

        <?php }else{ ?>

            <?php echo wp_kses_post($video); ?>

        <?php } ?>

    </div>

    <?php
endif;
?>