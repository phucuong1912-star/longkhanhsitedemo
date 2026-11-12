
                    <?php
                    $plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
                    if(!empty($plan_id)){
                        $plan_id = $plan_id;
                    }else{
                        $plan_id = 'none';
                    }
                    $faqs_show = get_post_meta( $plan_id, 'listingproc_faq', true );
                    if($plan_id=="none"){
                        $faqs_show = 'true';
                    }

                    $faqs = listing_get_metabox('faqs');
                    if( $faqs_show == true && count( $faqs )> 0 && !empty( $faqs ) ){

                        $faq        =   $faqs['faq'];
                        $faq        =   array_filter($faq);
                        $faqans     =   $faqs['faqans'];

                        for ($i = 1; $i <= (count($faq)); $i++):
                            if( !empty( $faq[$i] ) ):
                                if( $i == 1 )
                                {
                                    ?>
                <div class="container">
                    <div class="lp-listing-detail-page-content margin-bottom-70">
                        <div class="row">
                            <div class="pull-left lp-left-title">
                                <h2><?php echo esc_html__('FAQs', 'listingpro'); ?></h2>
                            </div>
                            <div class="col-md-12 lp-right-content-box">
                                    <?php
                                    echo '<div class="lp-listing-faqs" id="detail5-faq" role="tablist" aria-multiselectable="true">';
                                }
                                ?>
                                <div class="lp-listing-faq <?php if( $i == count( $faq ) ){echo 'last';} ?> ">
                                    <div class="faq-heading" role="tab">
                                        <h4 class="faq-title">
                                            <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo esc_attr( $i ); ?>" aria-expanded="true" aria-controls="collapse<?php echo esc_attr( $i ); ?>">
                                                <span><?php echo esc_html__('Q', 'listingpro'); ?></span> <?php echo esc_html($faq[$i]); ?>
                                                <i class="more-less glyphicon <?php if( $i == 1 ){ echo 'glyphicon-minus'; }else{ echo 'glyphicon-plus'; } ?>"></i>
                                            </a>
                                        </h4>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div id="collapse<?php echo esc_attr( $i ); ?>" class="collapse <?php if( $i == 1 ){ echo 'in'; } ?> faq-answer" role="tabpanel" aria-labelledby="heading<?php echo esc_attr( $i ); ?>">
                                        <div class="answer-body">
                                            <p><?php echo nl2br(do_shortcode($faqans[$i]), false); ?></p>
                                        </div>
                                    </div>
                                </div>
                                <?php
                                if( count($faq) == $i )
                                {
                                    echo '</div>';
                                    echo '</div>
        </div>
    </div>
</div>';
                                }
                            endif;
                        endfor;
                    }
                    ?>
