<?php
	do_action('lp_enqueue_print_script');
	$currencyCode = listingpro_currency_sign();
	$planID = '';
?>
<div id="invoices" class="tab-pane fade in">
	<div class="tab-header">
		<h3><?php esc_html_e('Invoices for Listing','listingpro'); ?></h3>
	</div>
	<div class="aligncenter">
		<div class="lp-invoice-table">
			<?php 
				global $user_id, $listingpro_options;
				$currency_position = $listingpro_options['pricingplan_currency_position'];
				$results = get_invoices_list($user_id, '', 'success');
				if(empty($results)){
					$results = get_invoices_list($user_id, '', 'expired');
				}
			?>
			<?php if( count($results) > 0 ){  ?>
				<?php $n=1; ?>
				<?php $reqs_url = get_template_directory_uri().'/include/invoices/invoice-modal.php'; ?>
				<?php foreach( $results as $data ){ ?>
					<div class="invoice-section">
						<div class="top-section">
							<h3><a href="<?php echo get_the_permalink($data->post_id); ?>"><?php echo get_the_title($data->post_id); ?></a><span><?php echo esc_html__(' - Purchased Listing', 'listingpro'); ?></span></h3>
							<a href="#" class="btn btn-first-hover pull-right showme" data-url="<?php echo esc_url($reqs_url); ?>" data-id="<?php echo esc_attr($data->main_id); ?>" data-lpinvoice="listinvoice"><?php echo esc_html__( 'View Detail', 'listingpro' ); ?></a>
						</div>
						<?php
							
							$plan_price = get_post_meta($data->plan_id, 'plan_price', true);
							$price_purchased = $data->price;
							$istax = false;
							$taxPrice = '';
							/* if(!empty($plan_price) && !empty($price_purchased)){
								if($plan_price!=$price_purchased){
									$istax = true;
									$taxPrice = (float)$price_purchased - (float)$plan_price;
									$taxPrice = round($taxPrice,2);
								}
							} */
							
							$taxtotalPrice;
							$resultantPrice;
							$price_purchasedInt;
							$resultantPriceInt;
							$lp_tax_rate= $listingpro_options['lp_tax_amount'];
							if(!empty($lp_tax_rate)){
								$taxtotalPrice = ($lp_tax_rate/100)*$plan_price;
								$resultantPrice = (float)$plan_price + (float)$taxtotalPrice;
								$resultantPriceInt = $resultantPrice;
								$price_purchasedInt = $price_purchased;
								if($resultantPriceInt==$price_purchasedInt){
									$istax = true;
									$taxPrice = round($taxtotalPrice,2);
								}
							}
						
							
						?>
						
						<table class="wp-list-table widefat fixed striped posts">
							<thead>
								<tr>
									<th><?php esc_html_e('No.','listingpro'); ?></th>
									<th><?php esc_html_e('Order#','listingpro'); ?></th>
									<th><?php esc_html_e('Method','listingpro'); ?></th>
									<th><?php esc_html_e('Plan','listingpro'); ?></th>
									<th><?php esc_html_e('Price','listingpro'); ?></th>
									<?php if($istax==true){ ?>
									<th><?php esc_html_e('Tax','listingpro'); ?></th>
									<?php } ?>
									<th><?php esc_html_e('Date','listingpro'); ?></th>
									<th><?php esc_html_e('Days','listingpro'); ?></th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<?php
										$pmethod = '';
										if($data->payment_method=="wire"){
											$pmethod = esc_html__('wire', 'listingpro');
										}
										else{
											$pmethod = $data->payment_method;
										}
									?>
									<td><?php echo wp_kses_post($n); ?></td>
									<td><?php echo wp_kses_post($data->order_id); ?></td>
									<td><?php echo wp_kses_post($pmethod); ?></td>
									<td><?php echo wp_kses_post($data->plan_name); ?></td>
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
									
									
									<td><?php echo date("M j, Y", strtotime($data->date)); ?></td>
									<?php
										if(!empty($data->days)){
									?>
										<td><?php echo wp_kses_post($data->days); ?></td>
									<?php
										}else{
									?>
										<td><?php echo esc_html__('Unlimited', 'listingpro'); ?></td>
									<?php
										}
									?>
									
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
					<?php $n++; ?>
				<?php } ?>
			<?php  }  ?>
			
			
			<?php if(empty($results) || count($results) <= 0){
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