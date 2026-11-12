<?php
	global $listingpro_options;

	$plan_id = listing_get_metabox_by_ID('Plan_id',$post->ID);
	if(!empty($plan_id)){
		$plan_id = $plan_id;
	}else{
		$plan_id = 'none';
	}
	
	$price_show = get_post_meta( $plan_id, 'listingproc_price', true );
	
	
	$claimed_section = listing_get_metabox('claimed_section');
	$listingpTo = listing_get_metabox('list_price_to');
	$listingprice = listing_get_metabox_by_ID('list_price', $post->ID);
	$listingpricestatus = listing_get_metabox_by_ID('price_status', $post->ID);
	
	$showClaim = true;
	if(isset($listingpro_options['lp_listing_claim_switch'])){
		if($listingpro_options['lp_listing_claim_switch']==1){
			$showClaim = true;
		}else{
			$showClaim = false;
		}
	}
	else{
		$showClaim = false;
	}
	$showReport = true;
	if( isset($listingpro_options['lp_detail_page_report_button']) ){
		if( $listingpro_options['lp_detail_page_report_button']=='off' ){
			$showReport = false;
		}
	}
	
	
	if($plan_id=="none"){
		$price_show = 'true';
	}
	
	$currentUserId = get_current_user_id();
	
?>


<?php 
	
	$menuOption = false;
	$menuTitle = '';
	$menuImg = '';
	$menuMeta = get_post_meta($post->ID, 'menu_listing', true);
	if(!empty($menuMeta)){
		if(isset($menuMeta['menu-title'])) {
                $menuTitle = $menuMeta['menu-title'];
        }

        $menuImg  =   '';
        if(isset($menuMeta['menu-img'])) {
                $menuImg = $menuMeta['menu-img'];
        }
        $menuOption = true;        
	}
	

	if( (!empty($menuMeta) && $menuOption == true ) || !empty($listingpTo) || !empty($listingprice) ||  ($showClaim==true && $claimed_section != 'claimed') || $listingpricestatus!="notsay" ) { ?>
	
	<div class="widget-box listing-price">
		<?php
			if(!empty($menuMeta) && $menuOption == true){
		?>
			<div class="menu-hotel">
				<a href="#" class="open-modal">
					<?php echo listingpro_icons('resMenu'); ?>
					<span>
						<?php if(!empty($menuTitle)){ echo esc_attr( $menuTitle ); }else{ echo esc_html__('See Full Menu','listingpro'); } ?>
					</span>
				</a>
				<div class="hotel-menu">
					<div class="inner-menu">
						<a href="#" class="close-menu-popup"><i class="fa fa-times"></i></a>
						<?php

						if( strpos( $menuImg, ',' ) )

						{

							$menuImg_arr    =   explode( ',', $menuImg );

							$menuImg_arr    =   array_filter( $menuImg_arr );

							foreach ( $menuImg_arr as $menuImgUrl )

							{

								echo '<img src="'. $menuImgUrl .'" alt="image">';

							}

						}

						else

						{

							echo '<img src="'. $menuImg .'" alt="image">';

						}

						?>
					</div>
				</div>
			</div>
			<?php } ?>
		
		<div class="price-area">
			
			<?php 
				if($price_show=="true"){
					echo listingpro_price_dynesty($post->ID);
				}
			 ?>
			<?php get_template_part('templates/single-list/claimed' ); ?>
			<?php
			if($showReport==true && is_user_logged_in() ){ ?>
				<div id="lp-report-listing" class="claim-area">
					<span class="phone-icon">
						<img class="icon icons8-Flag-2" width="20" height="20" alt="image" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABQAAAAUCAYAAACNiR0NAAABAElEQVQ4T+1T0W2DUBDz3QTpBoyQERiBDZpJkAUDVN2gI6QbNBMkG4QNkgXgIotHlaYkNKjKV56EkLg7n5+xjWQREa8AsogwAEf0JzOzbUSs3f2T5PA9lcdfVpZl4+4Fyd1lC8kMQNF13UrgZvY+1qc5kisRE+BXVVX5zbX9QC5gAEszOwBodAvNRcQLgLWW/xnwfCHJhYAlzzljkZsFeO02T8Cp/zxdf4yG8lvbtlHX9Waa08+OgWFjZvtkTsVreGTaRaopfhuSMvPoSUl5kw937p5fyypJGVgpUZqUkn1CVFRV+06Ku3/MMrZkSaDNJetZgLe0fQLe67zf/f+u4QlxCbp3x7Q50wAAAABJRU5ErkJggg=="> <strong> <?php echo esc_html__('See something wrong? ', 'listingpro'); ?> </strong>
					</span>
					<!-- new code 2.6.15 -->
					<a data-toggle="modal" data-target="#lp_report_listing" class="phone-number" data-postid="<?php echo esc_attr($post->ID); ?>" data-reportedby="<?php echo esc_attr($currentUserId); ?>" data-posttype="listing" href="#" id="lp-report-this-listing-popup"> <?php echo esc_html__('Report Now!', 'listingpro'); ?></a>
					<!-- end new code 2.6.15 -->
				</div>
			<?php } ?>
			
		</div>
		<?php get_template_part('templates/single-list/claim-form' ); ?>
	</div>
<?php } ?>