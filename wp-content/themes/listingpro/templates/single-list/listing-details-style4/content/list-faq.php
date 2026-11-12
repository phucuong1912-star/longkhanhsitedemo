<?php
global $post;
$plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);
if(!empty($plan_id)){
    $plan_id = $plan_id;
}else{
    $plan_id = 'none';
}
$faqs_show = get_post_meta( $plan_id, 'listingproc_faq', true );
if($plan_id=="none"){
    $faqs_show = 'true';
}


$faqs = listing_get_metabox_by_ID('faqs', $post->ID);

if($faqs_show=="true"){
    if( !empty($faqs) && count($faqs)>0 ){
        $faq = $faqs['faq'];
        $faqans = $faqs['faqans'];
        if( !empty($faq[1])){
            ?>
            <div class="post-row faq-section margin-bottom-30 clearfix">

                <div class="post-row-accordion">
                    <h4 class="lp-detail-section-title"><?php echo esc_html__( 'FAQs', 'listingpro' ); ?></h4>
                    <div id="accordion" class="lp-listing-faq">
                        <?php for ($i = 1; $i <= (count($faq)); $i++) { ?>
                            <?php if( !empty($faq[$i])) { ?>
                                <h4 class="faq-title">
                                    <a>
                                        <span class="question-icon"><?php echo esc_html__('Q', 'listingpro'); ?></span>
                                        <?php echo esc_html($faq[$i]); ?>
                                    </a>
                                </h4>
                                <div>
                                    <p>
                                        <?php //echo do_shortcode($faqans[$i]); ?>
                                        <?php echo  isset( $faqans[$i] ) ?  nl2br(do_shortcode($faqans[$i]), false) : ''; ?>
                                    </p>
                                </div><!-- accordion tab -->
                            <?php } ?>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}
?>