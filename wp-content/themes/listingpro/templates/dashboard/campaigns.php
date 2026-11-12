<?php
if( !empty($_POST['lp_ads_for_listing']) && isset($_POST['lp_ads_for_listing']) && !empty($_POST['method']) && isset($_POST['method']  ) && is_array($_POST['lpadsoftype']) ){
    $queryVar = array();
    lp_form_handler2($_POST, '');
}elseif ( isset($_GET['token']) && isset($_GET['PayerID']) ) {
    $queryVar = array();
    $queryVar = array();
    $queryVar['func'] = 'confirm';
    $queryVar['token'] = $_GET['token'];
    $queryVar['PayerID'] = $_GET['PayerID'];
    lp_form_handler2('', $queryVar);
}
?>
<div id="ads" class="tab-pane fade active in lp-compaign-outer">
	<div class="tab-header">
		<h3><?php esc_html_e('Ads - Promote your Listings','listingpro'); ?></h3>
	</div>
	<?php
		global $listingpro_options, $paged, $wp_query;
		$paypalStatus = false;
		$stripeStatus = false;
		$wireStatus = false;
		$showRandomsAds = true;
		$showSearchAds = true;
		$showDetailpageAds = true;
		
		if( isset($listingpro_options['lp_random_ads_switch']) ){
			if( !empty($listingpro_options['lp_random_ads_switch'])!="1" ){
				$showRandomsAds = false;
			}
		}
		
		
		if( isset($listingpro_options['lp_detail_page_ads_switch']) ){
			if( !empty($listingpro_options['lp_detail_page_ads_switch'])!="1" ){
				$showDetailpageAds = false;
			}
		}
		
		
		if( isset($listingpro_options['lp_top_in_search_page_ads_switch']) ){
			if( !empty($listingpro_options['lp_top_in_search_page_ads_switch'])!="1" ){
				$showSearchAds = false;
			}
		}
		
		$deafaultFeatImg = lp_default_featured_image_listing();
		
		
		$checkout2Status = false;
		if($listingpro_options['enable_paypal']=="1"){
			$paypalStatus = true;
		}
		if($listingpro_options['enable_stripe']=="1"){
			$stripeStatus = true;
		}
		if($listingpro_options['enable_wireTransfer']=="1"){
			$wireStatus = true;
		}
		if($listingpro_options['enable_2checkout']=="1"){
			$checkout2Status = true;
		}
		
		$current_user = wp_get_current_user();
		$user_id = $current_user->ID;
		$lp_random_ads = $listingpro_options['lp_random_ads'];
		$lp_detail_page_ads = $listingpro_options['lp_detail_page_ads'];
		$lp_top_in_search_page_ads = $listingpro_options['lp_top_in_search_page_ads'];
		$currencyprice = $listingpro_options['currency_paid_submission'];
		$ads_durations = '';

		$lp_promotion_title = $listingpro_options['lp_pro_title'];
		$lp_promotion_text = $listingpro_options['lp_pro_text'];
		$lp_promotion_img = $listingpro_options['lp_pro_img']['url'];

		$levels;

		if( !empty($lp_random_ads) ){
			$levels[$lp_random_ads]= $lp_random_ads.$currencyprice;
		}
		if( !empty($lp_detail_page_ads) ){
			$levels[$lp_detail_page_ads]= $lp_detail_page_ads.$currencyprice;
		}
		if( !empty($lp_top_in_search_page_ads) ){
			$levels[$lp_top_in_search_page_ads]= $lp_top_in_search_page_ads.$currencyprice;
		}
		
		$argsss=array(
			'post_type' => 'listing',
			'post_status' => 'publish',
			'posts_per_page' => 12,
			'author' => $user_id,
			'paged' => $paged,
			'meta_query'=> array(
				'relation' => 'OR',
				array(
					'key' => 'campaign_status',
					'compare' => 'NOT EXISTS',
				),
				array(
					'key' => 'campaign_status',
					'value' => array('active', 'in progress'),
					'compare' => 'NOT IN',
				),
				
			),
		);
		$campaings_query = null;
		$campaings_query = new WP_Query($argsss);
		
		$user_ID = '';
		$user_ID = get_current_user_id();
		
		$publisedPosts = count_user_posts_by_status($post_type = 'listing',$post_status = 'publish',$user_ID);
		
		?>
	<div class="aligncenter">
		<div class="lp-flip">
			<div class="lp-card">
				
					<?php if( (!empty($publisedPosts))&& ($campaings_query->have_posts()) ){ ?>
						<div class="promotional-section padding">
							<div class="promotiona-text">
								<h3><?php echo esc_attr($lp_promotion_title); ?></h3>
								<p><?php echo esc_attr($lp_promotion_text); ?></p>
							</div>
							<img src="<?php echo esc_url($lp_promotion_img); ?>" alt="image">
							<a href="#" class="lp-submit-btn"><?php esc_html_e('Get Started Now!','listingpro'); ?></a>
						</div>
					<?php } else{ ?>
						<div class="text-center no-result-found col-md-12 col-sm-6 col-xs-12 margin-bottom-30">
							<h1><?php esc_html_e('Ooops!','listingpro'); ?></h1>
							<p><?php esc_html_e('Sorry ! You have no Published listings yet!','listingpro'); ?></p>
						</div>
					<?php } ?>
				
				<?php
					$ads_promo_url = '';
				?>
				<form id="ads_promotion" name="ads_promotion" action="<?php echo esc_url($ads_promo_url); ?>" method="POST">
					<div class="promotional-section lp-promote-listing-margin">
						<div class="lp-face lp-front lp-pay-options margin-bottom-30 lp-dash-sec">
							<ul class="lp-inner">
								<?php
									if ( $campaings_query->have_posts() ) {
										while ( $campaings_query->have_posts() ) {
											$campaings_query->the_post();
											$listingcurrency = listing_get_metabox_by_ID('listingcurrency', get_the_ID());
											$listingprice = listing_get_metabox_by_ID('listingprice', get_the_ID());
											$listingtitle = get_the_title();
											$curlistingid = get_the_ID();
											echo '
											<li class="col-md-1 col-xs-12 lp-promote-ad-image">
												<div class="lp-list-view-thumb">
													<div class="lp-list-view-thumb-inner">';
														if ( has_post_thumbnail()) {
															$imageAlt = lp_get_the_post_thumbnail_alt(get_the_ID());
															$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID()), 'thumbnail' );
															if(!empty($image[0])){
																echo "<a href='".get_the_permalink()."' >
																		<img src='" . $image[0] . "' alt='".$imageAlt."' />
																	</a>";
															}else {
																echo "<a href='".get_the_permalink()."' >
																		<img alt='image' src='".esc_url('https://placehold.co/150x150')."' />
																	</a>";
															}
														}elseif(!empty($deafaultFeatImg)){
												
															echo "<a href='".get_the_permalink()."' >
																	<img alt='image' src='".$deafaultFeatImg."' />
																</a>";
															
														}else {
															echo "<a href='".get_the_permalink()."' >
																	<img alt='image' src='".esc_url('https://placehold.co/150x150')."' />
																</a>";
														}
														echo '
													</div>
												</div>
												<div class="lp-list-view-content-upper lp-list-view-content-bottom">
													<a href="'. get_the_permalink().'"><h4>'.get_the_title().'</h4></a>
													<ul class="lp-grid-box-price list-style-none list-pt-display">';
														$cats = get_the_terms( get_the_ID(), 'listing-category' );
														if(!empty($cats)){
															foreach ( $cats as $cat ) { 
																$category_image = listing_get_tax_meta($cat->term_id,'category','image');
																echo '
																<li class="">';
																	if (!empty($category_image)) {
																		if( hasFontAwesomeIconClass($category_image) ){
																			echo '
																			<a class="category-cion" href="'. get_term_link($cat).'">
																				<i class="icon icons-search-cat '.$category_image.'"></i>
																			</a>';
																		}else{
																			echo '
																			<a class="category-cion" href="'. get_term_link($cat).'">
																				<img class="icon icons8-Food" src="'. esc_attr($category_image).'" alt="cat-icon">
																			</a>';
																		}
																	}
																	echo '
																	<a href="'. get_term_link($cat).'">
																		'. $cat->name .'
																	</a>
																</li>';
															}
														}
														echo '
														<li>
															<span>'. esc_html($listingcurrency.$listingprice).'</span>
														</li>
														<li>
															<span class="lp-list-sp-icon">
																<i class="fa fa-calendar"></i>
															</span>
															<span class="lp-list-sp-text">
																'. get_the_time( get_option( 'date_format' ) ).'
															</span>
														</li>
													</ul>
													<div class="promote-btn pull-right">
														<input type="submit" name="promote_submit" class="lp-frontBtn lp-promotebtn btn btn-first-hover" data-listingtitle="'.$listingtitle.'" data-listingnid="'.$curlistingid.'"  value="'.esc_html__('Promote','listingpro').'"/>
														<input type="hidden" name="post_id" value="">
													</div>
												</div>
											</li>';
											wp_reset_postdata();
										}
										
										echo listingpro_pagination();
									} else {
									}
								?>
							</ul>
						</div>
					</div>
					<div class="promotional-section">
						<div class="lp-face lp-back1 lp-pay-options margin-bottom-30 lp-dash-sec"> 
							<h4><?php echo esc_html__('Where you want to show your ad', 'listingpro'); ?></h4>
							<div class="availableprice_options  padding-top-30">
								<?php if( is_array($levels) && count($levels)>0 ){ ?>
								
								<?php
									$randAdTip = esc_html__('These ads will appear on various location through out the site where ever the site-admin has defined. Usually on NON-Directory listing pages such as Home, About Us, Contact us, Blog posts etc.', 'listingpro');
									
									$detailPagTip = esc_html__('These ads will show in the bottom right of individual listings in a unique design. ', 'listingpro');
									
									$topsearchTip = esc_html__('These ads will appear on the search results or category listing page.', 'listingpro');
								?>
								
								<?php if($showRandomsAds==true){ ?>
										<div class="checkboxx">
											<div class="plan-img">
												<img src="<?php echo get_template_directory_uri(); ?>/assets/images/plan.png" alt="image">
											</div>
											
											<div class="checkbox pad-bottom-10">
												<input class="checked_class" data-package="lp_random_ads" type="checkbox" name="package_level[lp_random_ads]" value="<?php echo esc_attr($lp_random_ads); ?>">
												<label><?php esc_html_e('Show Random Ads  ','listingpro'); ?>(<?php echo esc_attr($lp_random_ads).' '.$currencyprice; ?>)</label>
											</div>
											<div class="help-text">
												<a href="#" class="help"><i class="fa fa-question"></i></a>
												<div class="help-tooltip">
													<p><?php echo wp_kses_post($randAdTip); ?></p>
												</div>
											</div>
											
										</div>
								<?php } ?>
								<?php if($showDetailpageAds==true){ ?>
										<div class="checkboxx">
											<div class="plan-img">
												<img src="<?php echo get_template_directory_uri(); ?>/assets/images/plan1.png" alt="image">
											</div>
											<div class="checkbox pad-bottom-10">
												<input class="checked_class" data-package="lp_detail_page_ads" type="checkbox" name="package_level[lp_detail_page_ads]" value="<?php echo esc_attr($lp_detail_page_ads); ?>">
												<label><?php esc_html_e('Show Detail Page Ads ','listingpro'); ?>(<?php echo esc_attr($lp_detail_page_ads).' '.$currencyprice; ?>)</label>
											</div>
											<div class="help-text">
												<a href="#" class="help"><i class="fa fa-question"></i></a>
												<div class="help-tooltip">
													<p><?php echo wp_kses_post($detailPagTip); ?></p>
												</div>
											</div>
										</div>
								<?php } ?>
								<?php if($showSearchAds==true){ ?>
										<div class="checkboxx">
											<div class="plan-img">
												<img src="<?php echo get_template_directory_uri(); ?>/assets/images/plan2.png" alt="image">
											</div>
											<div class="checkbox pad-bottom-10">
												<input class="checked_class" data-package="lp_top_in_search_page_ads" type="checkbox" name="package_level[lp_top_in_search_page_ads]" value="<?php echo esc_attr($lp_top_in_search_page_ads); ?>">
												<label><?php esc_html_e('Show Ads in search and taxonomy  ','listingpro'); ?>(<?php echo esc_attr($lp_top_in_search_page_ads).' '.$currencyprice; ?>)</label>
											</div>
											<div class="help-text">
												<a href="#" class="help"><i class="fa fa-question"></i></a>
												<div class="help-tooltip">
													<p><?php echo wp_kses_post($topsearchTip); ?></p>
												</div>
											</div>
										</div>
								<?php } ?>
									<input id="totalprice" type="hidden" name="total" value="0" />
								<?php } ?>
						   	</div>
							<div class="form-group">
								<div class="row">
									<div class="col-md-6 text-left">
										<?php 
											if(!empty($ads_durations)){
												if($ads_durations=="1"){
													echo '<strong>'.esc_html__( 'Ad duration', 'listingpro' ).' : </strong><strong><span>'.$ads_durations.' '.esc_html__( 'Day', 'listingpro' ).'</span></strong>';
												}
												else{
													echo '<strong>'.esc_html__( 'Ad duration', 'listingpro' ).' : </strong><strong><span>'.$ads_durations.' '.esc_html__( 'Days', 'listingpro' ).'</span></strong>';
												}
											}
										?>
									</div>
									<div class="col-md-6 text-right">
										<span class="pricetotal">
											<strong><?php esc_html_e('Total : ','listingpro'); ?></strong><strong><span id="price">0</span>
											<?php echo ' '.$currencyprice; ?></strong>
										</span>
										<?php
										$enableTax = false;
										$Taxrate='';
										$Taxtype='';
										if($listingpro_options['lp_tax_swtich']=="1"){
											$enableTax = true;
											$Taxrate = $listingpro_options['lp_tax_amount'];
											$Taxtype = $listingpro_options['lp_tax_label'];
										}
										
										if($enableTax==true && !empty($Taxrate)){
										?>
										<span class="pricetax" data-taxprice = "<?php echo esc_attr($Taxrate); ?>">
											<strong>( <?php esc_html_e('inc.tax ','listingpro'); ?> )</strong>
										</span>
										
										<?php
										}
										?>
									</div>
								</div>
							</div>
							<a href="#" id="lp-next" class=" hide lp-next1 promotebtn btn btn-first-hover"><?php echo esc_html__('next', 'listingpro'); ?> <i class="fa fa-angle-double-right"></i></a>
							<span class="show"><?php echo esc_html__('next', 'listingpro'); ?>	<i class="fa fa-angle-double-right"></i></span>
							<button id="lp-back1" class="promotebtn btn btn-first-hover" style="float:left;"><i class="fa fa-angle-double-left"></i> <?php echo esc_html__('Back', 'listingpro'); ?></button>
						</div>
					</div>
					<div class="promotional-section">
						<div class="lp-face lp-back2 lp-pay-options margin-bottom-30 lp-dash-sec"> 
							<?php if($wireStatus==true){?>
								<div class="lp-method-wrap lp-listing-form pos-relative">
									<label>
										<img class="" src="<?php echo get_template_directory_uri() ?>/assets/images/wire.png" alt="image" />
										<div class="radio radio-danger">
											<input class="radio_checked" type="radio" name="method" id="rd1" value="wire">
											<label for="rd1">
											</label>
										</div>
									</label>
								</div>
							<?php } ?>
							<?php if($paypalStatus==true){?>
								<div class="lp-method-wrap lp-listing-form pos-relative">
									<label>
										<img class="" src="<?php echo get_template_directory_uri() ?>/assets/images/paypal.png" alt="image" />
										<div class="radio radio-danger">
											<input class="radio_checked" type="radio" name="method" id="rd2" value="paypal">
											<label for="rd2">
											</label>
										</div>
									</label>
								</div>
							<?php } ?>
							<?php if($stripeStatus==true){  ?>
								<div class="lp-method-wrap lp-listing-form pos-relative">
									<label>
										<img class="" src="<?php echo get_template_directory_uri() ?>/assets/images/stripe.png" alt="image" />
										<div class="radio radio-danger">
											<input class="radio_checked" type="radio" name="method" id="rd3" value="stripe">
											<label for="rd3">
											</label>
										</div>
									</label>
								</div>
							<?php } ?>
							
							<?php if($checkout2Status==true){  ?>
								<div class="lp-method-wrap lp-listing-form pos-relative">
									<label>
										<img class="" src="<?php echo get_template_directory_uri() ?>/assets/images/2checkout-logo.png" alt="image" />
										<div class="radio radio-danger">
											<input class="radio_checked" type="radio" name="method" id="rd4" value="2checkout">
											<label for="rd4">
											</label>
										</div>
									</label>
								</div>
							<?php
								
							} 
							?>
							
							
							
							
							<?php if($wireStatus==false && $paypalStatus==false && $stripeStatus==false){?>
								<div class="text-center no-result-found col-md-12 col-sm-6 col-xs-12 margin-bottom-30">
									<h1> <?php esc_html_e('Ooops!','listingpro'); ?></h1>
									<p> <?php esc_html_e('Sorry ! You have no checkout payment method','listingpro'); ?></p>
								</div>
							<?php } ?>
							
							<div class="clearfix"></div>
							<input type="hidden" name="listing_id" value="" />
							<input type="hidden" name="cur_listing_title" value="" />
							<input type="hidden" name="lp_total_price" value="0" />
							<input type="hidden" name="func" value="start ads" />
							<input type="hidden" name="taxprice" value="0" />
							<input type="hidden" id="ad-blank-errorMsg" value="<?php echo esc_html__('Please select form fields', 'listingpro'); ?>" />
							<input type="submit" name="submit_ads_prom" class="hide lp-next2 promotebtn btn btn-first-hover" value="<?php echo esc_html__('Proceed', 'listingpro'); ?>" />
							<span class="proceed-btn show"><?php echo esc_html__('Proceed', 'listingpro'); ?> <i class="fa fa-send"></i></span>
							<i class="lp-after-token fa fa-spinner fa-spin"></i>
							<button id="lp-back2" class="promotebtn btn btn-first-hover" style="float:left;"><i class="fa fa-angle-double-left"></i> <?php echo esc_html__('Back', 'listingpro'); ?></button>
							<input type="hidden" name="currency" value="<?php echo esc_attr($currencyprice); ?>">
							
							
						</div>
					</div>
				</form>
				<?php
					if($checkout2Status==true){
						get_template_part( 'templates/popups/checkout2', 'popup' );
					}
				?>
			</div>	 
		</div>
	</div>
</div>

<?php
	$pubilshableKey = '';
	$secritKey = '';
	
	$pubilshableKey = $listingpro_options['stripe_pubishable_key'];
	$secritKey = $listingpro_options['stripe_secrit_key'];
	
	$ajaxURL = '';
	$ajaxURL = admin_url( 'admin-ajax.php' );
	
	?>
	<script>
	var listing_id;
	var packages = [];
	var taxprice = '';
	
	jQuery('input.checked_class[type="checkbox"]').click(function(){
		if(jQuery(this).attr('checked')){
			packages.push(jQuery(this).data('package'));
		}
		else{
			var i = packages.indexOf(jQuery(this).data('package'));
			if(i != -1) {
				packages.splice(i, 1);
			}
		}
		
	});
	
	jQuery('#ads_promotion').on('submit', function(e){
		
		$this = jQuery(this);
		listing_id = $this.find('input[name="post_id"]').val();
		taxprice = $this.find('input[name="taxprice"]').val();
		
	});
	
	var handler = StripeCheckout.configure({
	  key: "<?php echo wp_kses_post($pubilshableKey); ?>",
	  image: "https://stripe.com/img/documentation/checkout/marketplace.png",
	  locale: "auto",
	  token: function(token) {
		token_id = token.id;
		token_email = token.email;
		jQuery('body').addClass('listingpro-loading');
		lpTotalPrice = jQuery('input[name="lp_total_price"]').val();
		
		jQuery.ajax({
			type: "POST",
			dataType: "json",
			url: "<?php echo esc_url($ajaxURL); ?>",
			data: { 
				"action": "listingpro_save_package_stripe", 
				"token": token_id, 
				"email": token_email, 
				"listing": listing_id, 
				"packages": packages,
				"lpTOtalprice":lpTotalPrice,
				"taxprice":taxprice
			},   
			success: function(res){
				jQuery('body').removeClass('listingpro-loading');
				if(res.status=="success"){
					redURL = res.redirect;
					window.location.href = redURL;
				}
				else{
					alert(res.status);
					jQuery('body').removeClass('listingpro-loading');
				}
				
			},
			error: function(errorThrown){
				jQuery('body').removeClass('listingpro-loading');
				alert(errorThrown);
			} 
		});
		

	  }
	});

	window.addEventListener("popstate", function() {
	  handler.close();
	});
	</script>
<!--ads-->