<?php
global $listingpro_options;
$allowedReviews = $listingpro_options['lp_review_switch'];
if(!empty($allowedReviews) && $allowedReviews=="1"){
if(get_post_status($post->ID)=="publish"){
?>
<div class="container">
    <div class="lp-listing-detail-page-content margin-bottom-70">
        <div class="row">
            <div class="pull-left lp-left-title">
                <h2><?php echo esc_html__('Reviews', 'listingpro'); ?></h2>
            </div>
            <div class="col-md-12 lp-right-content-box">
                <div class=" lp-detail-reviews-box" id="detail5-reviews">
                    <?php
                    $key = 'reviews_ids';
                    $review_idss = listing_get_metabox_by_ID($key , $post->ID);
                    if( !empty( $review_idss ) ):
                    ?>
                    <div id="submitreview" class="clearfix">
                        <?php
                        listingpro_get_all_reviews($post->ID);
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php
                        listingpro_get_reviews_form($post->ID);
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
}
}
    ?>