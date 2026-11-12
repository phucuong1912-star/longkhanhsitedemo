<?php
global $post;
$plan_id = listing_get_metabox_by_ID('Plan_id', $post->ID);
if (!empty($plan_id)) {
	$plan_id = $plan_id;
} else {
	$plan_id = 'none';
}
$faqs_show = get_post_meta($plan_id, 'listingproc_faq', true);
if ($plan_id == "none") {
	$faqs_show = 'true';
}


$faqs = listing_get_metabox_by_ID('faqs', $post->ID);

if ($faqs_show == "true") {
	if (!empty($faqs) && count($faqs) > 0) {
		$faq = $faqs['faq'];
		$faqans = $faqs['faqans'];
		if (!empty($faq[1])) {
?>
			<div class="post-row faq-section margin-bottom-30 clearfix">
				<!-- <div class="post-row-header clearfix margin-bottom-15">
					<h3><?php echo esc_html__('Quick questions', 'listingpro'); ?></h3>
				</div> -->
				<div class="post-row-accordion">
				<h4 class="lp-classic-faq-title"><?php echo esc_html__('Frequently Asked Questions', 'listingpro'); ?></h4>
					<div class="classic-accordion">
						<?php for ($i = 1; $i <= (count($faq)); $i++) { 
							?>
							
							<?php if (!empty($faq[$i])) {
								 ?>
								<div class="accordion-item">
									<button id="accordion-button-<?php echo $i; ?>" aria-expanded="false">
										<h5>
											<span class="question-icon"><?php echo esc_html__('Q', 'listingpro'); ?></span>&nbsp;:&nbsp;
											<span class="accordion-title"><?php echo esc_html($faq[$i]); ?></span>
										</h5>
										<span class="icon" aria-hidden="true"></span>
									</button>
									<div class="accordion-content">
										<p>
											<?php //echo do_shortcode($faqans[$i]); 
											?>
											<?php echo  isset( $faqans[$i] ) ?  nl2br(do_shortcode($faqans[$i]), false) : ''; ?>
										</p>
									</div>
								</div>
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