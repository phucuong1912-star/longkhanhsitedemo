<?php

		$plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);
		if(!empty($plan_id)){
			$plan_id = $plan_id;
		}else{
			$plan_id = 'none';
		}
		
		$tags_show = 'true';
		if($plan_id=="none"){
			
			$tags_show = 'true';
			
		}

?>

<div class="tab-pane" id="adinfo">
		<?php 
			$listingContent = get_the_content($post->ID);
			if ( $listingContent!=="" ) {
		?>
			<div class="post-row">
				<div class="post-detail-content">
					<?php the_content(); ?>
					
				</div>
				
			</div>
		<?php
			}
		?>
		<?php 
		$tags = get_the_terms( $post->ID ,'features');
		if(!empty($tags)){ 
			if($tags_show=="true"){?>
			<div class="post-feature-box margin-bottom-30">
				<h3><?php echo esc_html__('Features', 'listingpro'); ?></h3>
				<div class="post-row">
					
					<ul class="features list-style-none clearfix">
						<?php 
							foreach($tags as $tag) {
								$icon = listingpro_get_term_meta( $tag->term_id ,'lp_features_icon');
								?>								
							<li>
								<a href="<?php echo get_term_link($tag); ?>" class="parimary-link">
									<span class="tick-icon">
										<?php if(!empty($icon)) { ?>
											<i class="fa <?php echo esc_attr($icon); ?>"></i>
										<?php }else { ?>
											<i class="fa fa-check"></i>
										<?php } ?>
									</span>
									<?php echo esc_html($tag->name); ?>
								</a>
							</li>
						<?php } ?>
					</ul>
				</div>
			</div>	
			<?php } ?>
		<?php } ?>
	
	<?php
    global $listingpro_options;
    $pagecontentOption = $listingpro_options['lp-detail-page-layout2-content']['general'];
    if ($pagecontentOption):
        foreach ($pagecontentOption as $key => $value) {
            switch ($key) {
                case 'lp_additional_section':
                    echo listing_all_extra_fields($post->ID);
            }
        }
    endif;
    ?>
	
</div>