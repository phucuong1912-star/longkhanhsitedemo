<?php
	do_action('lp_enqueue_print_script');
	global $listingpro_options;
	$currencyCode = listingpro_currency_sign();
	$currency_position = $listingpro_options['pricingplan_currency_position'];
?>
<div id="invoices" class="tab-pane fade in">
	<div class="tab-header">
		<h3><?php esc_html_e('Invoices for Ads','listingpro'); ?></h3>
	</div>
	<div class="aligncenter">
		<div class="lp-invoice-table">
			<?php 
				global $user_id;
				$n = 1;
				$resultsforads = get_ads_invoices_list($user_id, '', 'success');
			?>
			<?php if(!empty($resultsforads)){ ?>
				<?php if( count($resultsforads) > 0 ){  ?>
				
					<?php $reqs_url = get_template_directory_uri().'/include/invoices/invoice-modal.php'; ?>
					<?php foreach( $resultsforads as $data ){ ?>
					
						<?php
							$adID = $data->post_id;
							$listID = listing_get_metabox_by_ID('ads_listing', $adID);
							
							$ad_type = listing_get_metabox_by_ID('ad_type', $adID);
							
							
							
							$ad_price = '';
							$price_purchased = $data->price;
							$istax = false;
							$taxPrice = '';
							if(!empty($ad_type)){
								
								foreach($ad_type as $ads){
									
									$ad_price = (float)$ad_price + (float)$listingpro_options[$ads];
								}
								if($ad_price!=$price_purchased && !empty($price_purchased)){
									$istax = true;
									$taxPrice = $price_purchased - $ad_price;
									$taxPrice = round($taxPrice,2);
								}
							}
							
							$ad_type = listing_get_metabox_by_ID('ad_type', $adID);
							
							$ad_date = listing_get_metabox_by_ID('ad_date', $adID);
							$ad_expiryDate = listing_get_metabox_by_ID('ad_expiryDate', $adID);
							$plansData = '';
							$cnt = 1;
							if(!empty($ad_type)){
								foreach($ad_type as $package){
									
									if($package=="lp_random_ads"){
										$plansData = $plansData.esc_html__('Random Ads', 'listingpro');
									}
									
									if($package=="lp_detail_page_ads"){
										$plansData = $plansData.esc_html__('Detail Page Ads', 'listingpro');
									}
									
									if($package=="lp_top_in_search_page_ads"){
										$plansData = $plansData.esc_html__('Search Page Ads', 'listingpro');
									}
									
									if($cnt< count($ad_type)){
										$plansData = $plansData.' , ';
									}
									$cnt++;
								}
							}
							
						?>
						
						<div class="invoice-section">
							<div class="top-section">
							
								<h3><span><?php echo esc_html__(' - Purchased Ads', 'listingpro'); ?></span></h3>
								<a href="#" class="btn btn-first-hover pull-right showme" data-url="<?php echo esc_url($reqs_url); ?>" data-id="<?php echo esc_attr($data->main_id); ?>" data-lpinvoice="adsinvoice"><?php echo esc_html__( 'View Detail', 'listingpro' ); ?></a>
							</div>
							<table class="wp-list-table widefat fixed striped posts">
								<thead>
									<tr>
										<th><?php esc_html_e('No.','listingpro'); ?></th>
										<th><?php esc_html_e('Order#','listingpro'); ?></th>
										<th><?php esc_html_e('Plan','listingpro'); ?></th>
										<th><?php esc_html_e('Price','listingpro'); ?></th>
										<?php if($istax==true){ ?>
										<th><?php esc_html_e('Tax','listingpro'); ?></th>
										<?php } ?>
										<th><?php esc_html_e('Purchased Date','listingpro'); ?></th>
										<th><?php esc_html_e('Expiry Date','listingpro'); ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<tr>
										<td><?php echo esc_attr($n); ?></td>
										<td><?php echo esc_attr($data->transaction_id); ?></td>
										<td><?php echo wp_kses_post($plansData); ?></td>
										<?php
											if( !empty($currency_position) ){
												if( $currency_position=="left" ){ ?>
													<td><?php echo wp_kses_post($currencyCode).round($data->price,2); ?></td>
													<?php if($istax==true){ ?>
													<td><?php echo wp_kses_post($currencyCode).$taxPrice; ?></td>
													<?php }
												}else{ ?>
													<td><?php echo round($data->price,2).$currencyCode; ?></td>
													<?php if($istax==true){ ?>
													<td><?php echo wp_kses_post($taxPrice).$currencyCode; ?></td>
													<?php }
													
												}
												
											}else{ ?>
												<td><?php echo wp_kses_post($currencyCode).round($data->price,2); ?></td>
												<?php if($istax==true){ ?>
												<td><?php echo wp_kses_post($currencyCode).$taxPrice; ?></td>
												<?php }
											}
										?>
										<td><?php echo date("M j, Y", strtotime($ad_date)); ?></td>
										<td><?php echo date("M j, Y", strtotime($ad_expiryDate)); ?></td>
										<td></td>
									</tr>
								</tbody>
							</table>
						</div>
						<?php $n++; ?>
					<?php } ?>
				<?php } ?>
			<?php } ?>
			<?php if(empty($resultsforads) || count($resultsforads) <= 0){
				?>
				<div class="text-center no-result-found col-md-12 col-sm-6 col-xs-12 margin-bottom-30">
					<h1><?php esc_html_e('Ooops!','listingpro'); ?></h1>
					<p><?php esc_html_e('Sorry ! There is no Invoice generated yet!','listingpro'); ?></p>
				</div>
				<?php
			}
			?>
		</div>
	</div>
</div>
<!--invoices-->