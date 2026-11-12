<div class="tab-pane" id="video">
	<?php
	$plan_id = listing_get_metabox_by_ID('Plan_id',get_the_ID());
		if(!empty($plan_id)){
			$plan_id = $plan_id;
		}else{
			$plan_id = 'none';
		}
		$video_show = get_post_meta( $plan_id, 'video_show', true );
		
		if($plan_id=="none"){
			$video_show = 'true';
		}
		
	
	
	$video = listing_get_metabox_by_ID('video', $post->ID);
	if(!empty($video))
	{
		if($video_show=="true")
		{
	?>
				<div class="widget-video widget-box widget-bg-color lp-border-radius-5">
			
				<?php 
					$htmlvideocode = wp_oembed_get($video);
					echo $htmlvideocode;
				?>
				</div> 
		<?php } ?>
<?php } ?>
</div>