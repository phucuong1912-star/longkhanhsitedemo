<div class="tab-pane" id="faqs">
	<?php 
		
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
				<div class="post-row faq-section clearfix">
					<!-- <div class="post-row-header clearfix margin-bottom-15">
						<h3><?php echo esc_html__('Quick questions', 'listingpro'); ?></h3>
					</div> -->
					<div class="post-row-accordion">
						<div id="accordion">
							<?php for ($i = 1; $i <= (count($faq)); $i++) { ?>
								<?php if( !empty($faq[$i])) { ?>
									<h5 class="margin-top-0">
									  <span class="question-icon"><?php echo esc_html__('Q', 'listingpro'); ?></span>
									  <span class="accordion-title"><?php echo esc_html($faq[$i]); ?></span>
									</h5>
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
</div>