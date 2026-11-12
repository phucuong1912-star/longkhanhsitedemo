<?php 

	$timekit = false;
	$timekit_booking = get_post_meta($post->ID, 'timekit_bookings', true);
	
	if(!empty($timekit_booking)){
		$timekit = true;
	}

	if(!empty($timekit_booking) && $timekit == true){ ?>
		<div class="widget-box">
<?php echo $timekit_booking; ?>						
		</div>
	
	
<?php } ?>

<?php

	$plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);
	if(!empty($plan_id)){
		$plan_id = $plan_id;
	}else{
		$plan_id = 'none';
	}
	
	$hours_show = get_post_meta( $plan_id, 'listingproc_bhours', true );
	if($plan_id=="none"){
		$hours_show = 'true';
	}
 
	$buisness_hours = listing_get_metabox('business_hours');
	if(!empty($buisness_hours)){
		if($hours_show=="true"){
?>
			<div class="widget-box">
				<?php get_template_part( 'include/timings' ); ?>									
			</div>
<?php
	
		}
	}
?>