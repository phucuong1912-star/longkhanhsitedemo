<?php
global $listingpro_options;
$enableTax = false;
$Taxrate='';
$Taxtype='';
if($listingpro_options['lp_tax_swtich']=="1"){
	$enableTax = true;
	$Taxrate = $listingpro_options['lp_tax_amount'];
	$Taxtype = $listingpro_options['lp_tax_label'];
}
$currency_symbol = listingpro_currency_sign();
$currency_position = '';
$currency_position = $listingpro_options['pricingplan_currency_position'];

$output .='<div class="lp_billing_total" data-currencypos="'.$currency_position.'">';
						$output .='
						<table class="table table-condensed table-responsive">
							<thead>
							  <tr>
								<th>'.esc_html__('Item', 'listingpro-plugin').'</th>
								<th>'.esc_html__('Total', 'listingpro-plugin').'</th>
							  </tr>
							</thead>
							<tbody>
							  <tr>
								<td><span id="lp_price_plan"></span></td>';
								if(!empty($currency_position)){
									if($currency_position=="left"){
										$output .='<td>'.$currency_symbol.'<span id="lp_price_plan_price"></span></td>';
									}
									else{
										$output .='<td><span id="lp_price_plan_price"></span>'.$currency_symbol.'</td>';
									}
								}
								else{
									$output .='<td>'.$currency_symbol.'<span id="lp_price_plan_price"></span></td>';
								}
								
								
							  $output .='
							  </tr>';
							  if($enableTax==true && !empty($Taxrate)){
								$output .='	
							  <tr>
								<td><span id="lp_price_tax">'.esc_html__("Tax ", "listingpro-plugin").'<span id="lp_tax_type">( '.$Taxtype.' )</span></span></td>';
								
								if(!empty($currency_position)){
									
									if($currency_position=="left"){
										$output .='<td>'.$currency_symbol.'<span id="lp_tax_price"></span></td>';
									}
									else{
										$output .='<td><span id="lp_tax_price"></span>'.$currency_symbol.'</td>';
									}
								}
								else{
									$output .='<td>'.$currency_symbol.'<span id="lp_tax_price"></span></td>';
								}
								
								
								$output .='
							  </tr>
							  ';}
							  $output .='
							  <tr>
								<td><strong>'.esc_html__('Subtotal', 'listingpro-plugin').'</strong></td>';
								if(!empty($currency_position)){
									if($currency_position=="left"){
										$output .='<td><strong>'.$currency_symbol.'<span id="lp_price_subtotal"></span></strong></td>';
									}
									else{
										$output .='<td><strong><span id="lp_price_subtotal"></span>'.$currency_symbol.'</strong></td>';
									}
								}
								else{
									$output .='<td><strong>'.$currency_symbol.'<span id="lp_price_subtotal"></span></strong></td>';
								}
								
							 $output .=' </tr>
							  
							</tbody>
						 </table>
						';
						$output .='</div>';