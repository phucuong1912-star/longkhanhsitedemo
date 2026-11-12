<?php
/**
 * Template footer
 * root
 * @version 2.0
 */

do_action('lp_add_at_startof_footer');
$listing_mobile_view  = lp_theme_option('single_listing_mobile_view');
$responsive_footer  = lp_theme_option('listingpro_responsive_footer_app_switch');
if( ( $responsive_footer != 'yes' && ( $listing_mobile_view == 'app_view' || $listing_mobile_view == 'app_view2' ) && wp_is_mobile() && !is_page_template('template-dashboard.php') )){
    if( $listing_mobile_view == 'app_view2' ){
        get_template_part( 'templates/footers/footer-app-view-new' );
    }
    else{
        get_template_part( 'templates/footers/footer-app-view' );
    }
}else{

	$footer_style = lp_theme_option('footer_style');
    $ep_footer_style = lp_theme_option('ep_footer_style');
    $ep_footer_switch= lp_theme_option('ep_footer_switch');
    $footerNeed = true;
		$listing_style = lp_theme_option('listing_style');
    if(isset($_GET['list-style']) && !empty($_GET['list-style'])){
        $listing_style = esc_html($_GET['list-style']);
    }
    if(is_tax('location') || is_tax('listing-category')  || is_tax('list-tags') || is_tax('features') || is_search()){
        if($listing_style == '2' || $listing_style == '3' || $listing_style == '5' || $listing_style == '6'){
            $footerNeed = false;
        }
    }
    if($footerNeed == true) {
        if (!is_page_template('template-dashboard.php')) {
            ?>
            <!--==================================Footer Open=================================-->
            <?php if ($footer_style == 'footer1') { ?>
                <!-- Footer style 1 -->
                <?php get_template_part('templates/footers/footer-style1'); ?>
            <?php } elseif ($footer_style == 'footer2') { ?>
                <!-- Footer style 2 -->
                <?php get_template_part('templates/footers/footer-style2'); ?>
            <?php } elseif ($footer_style == 'footer3') { ?>
                <!-- Footer style 3 -->
                <?php get_template_part('templates/footers/footer-style3'); ?>
            <?php } elseif ($footer_style == 'footer4') {
                // Footer Style 4
                get_template_part('templates/footers/footer-style4');
            } elseif ($footer_style == 'footer5') {
                // Footer Style 5
                get_template_part('templates/footers/footer-style5');
            } elseif ($footer_style == 'footer6') {
                // Footer Style 6
                get_template_part('templates/footers/footer-style6');
            } elseif ($footer_style == 'footer7') {
                // Footer Style 7
                get_template_part('templates/footers/footer-style7');
            } elseif ($footer_style == 'footer8') {
                // Footer Style 8
                get_template_part('templates/footers/footer-style8');
            } elseif ($footer_style == 'footer9') {
                // Footer Style 9
                get_template_part('templates/footers/footer-style9');
            } elseif ($footer_style == 'footer10') {
                // Footer Style 10
                get_template_part('templates/footers/footer-style10');
            } elseif ($footer_style == 'footer11') {
                // Footer Style 11
                get_template_part('templates/footers/footer-style11');
            }elseif ($footer_style == 'footer_classic') {
				//new classic style
				wp_enqueue_style('lp-classic-footer');
				get_template_part('templates/footers/footer-classic'); 

			}elseif ($footer_style == 'mp_footer1') {
                // Footer Style Medicalpro 1
                if(class_exists('MedicalPro')){
                    mp_get_template_part('templates/footers/mp-footer-style1');
                }else{
                    get_template_part('templates/footers/footer-style4');
                }
            }// WeddingPro
            elseif ($footer_style == 'wpro_footerStyle_1') {
                // Footer Style Medicalpro 1
                if(class_exists('WeddingPro')){
                    wpro_get_template_part('templates/footers/wpro_footerStyle_1');
                }else{
                    get_template_part('templates/footers/footer-style4');
                }
            }// BlackPro
            elseif ($footer_style == 'bpro_footerStyle_1') {
                if(class_exists('BlackPro')){
                    bpro_get_template_part('templates/footers/bpro_footerStyle_1');
                }else{
                    get_template_part('templates/footers/footer-style4');
                }
            }// PetPro
            elseif ($footer_style == 'ppro_footerStyle_1') {
                if(class_exists('PetFinder')){
                    ppro_get_template_part('templates/footers/ppro_footerStyle_1');
                }else{
                    get_template_part('templates/footers/footer-style4');
                }
            }// LawyerPro
            elseif ($footer_style == 'lpro_footerStyle_1') {
                // Footer Style Medicalpro 1
                if(class_exists('LawyerPro')){
                    lpro_get_template_part('templates/footers/lpro_footerStyle_1');
                }else{
                    get_template_part('templates/footers/footer-style4');
                }
            }else{
                do_action('listingpro_addon_footer');
            }

        }
    }
}

?>

<?php wp_footer(); ?>
<script>

    var videoID = '';
    jQuery(document).ready(function () {
        videoID =   jQuery('.video-lp').data('videoid');
    });

    var tag = document.createElement('script');

    tag.src = "https://www.youtube.com/iframe_api";
    var firstScriptTag = document.getElementsByTagName('script')[0];
    firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    var player;
    function onYouTubeIframeAPIReady() {
        player = new YT.Player('player', {
            height: '390',
            width: '640',
            videoId: videoID,
            playerVars: { 'mute': 1, 'showinfo': 0, 'rel': 0, 'loop': 1, 'controls': 0 },
            events: {
                'onReady': onPlayerReady,
                'onStateChange': onPlayerStateChange
            }
        });
    }

    function onPlayerReady(event) {
        event.target.playVideo();
    }
    var done = false;
    function onPlayerStateChange(event) {
        if (event.data == YT.PlayerState.PLAYING && !done) {
            done = true;
        }
        if (event.data === YT.PlayerState.ENDED) {
            player.playVideo();
        }
    }
</script>

</body>
</html>