<?php
	$existingCoupons = lp_get_coupon_status_counter('active', true);
	if(!empty($existingCoupons)){
?>
	<table class="table wp-list-table widefat fixed striped posts">
										<thead>
											<tr>
												<!-- <th><?php echo esc_html__('No.', 'listingpro-plugin'); ?></th> -->

												<td id="cb" class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-1">Select All</label><input id="cb-select-all-1" type="checkbox"></td>


													<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
													<a><span><?php echo esc_html__('Code', 'listingpro-plugin'); ?></span><span class="sorting-indicator"></span></a>
													</th>

													<th class="manage-column column-tags"><?php echo esc_html__('Discount value', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Total', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Available', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Start Date', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Ends Date', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Status', 'listingpro-plugin'); ?></th>
													<th class="manage-column column-tags"><?php echo esc_html__('Delete', 'listingpro-plugin'); ?></th>
												</tr>
											</thead>
											<tbody>
													
														<?php
														$counter = 1;
														
														foreach($existingCoupons as $copIndex=>$signleCoupon){
															$couponStatus = lp_check_coupon_status($signleCoupon['code']);
															
															$coupontype = false;
															$couponText = esc_html__('(%)', 'listingpro-plugin');
															if(isset($signleCoupon['copontype'])){
																$coupontype = $signleCoupon['copontype'];
																if(!empty($coupontype)){
																	$couponText = esc_html__('(Fix)', 'listingpro-plugin');
																}
															}
															
															?>
															
															<?php
																$available = $signleCoupon['coponlimit'];
																if(isset($signleCoupon['used'])){
																	$available = $signleCoupon['coponlimit'] - $signleCoupon['used'];
																}
															?>

															<tr>
																<th scope="row" class="check-column">			
																
																</th>


																<!-- <td><?php echo $counter; ?></td> -->
																<td class="author column-author">

																	<?php echo $signleCoupon['code']; ?></td>
																	<td class="manage-column column-categories"><?php echo $signleCoupon['discount'].$couponText; ?></td>
																	<td class="manage-column column-categories"><?php echo $signleCoupon['coponlimit']; ?></td>
																	<td class="manage-column column-categories"><?php echo $available; ?></td>
																	<td class="manage-column column-categories"><?php echo date(get_option("date_format"), strtotime($signleCoupon['starts'])); ?></td>
																	<td class="manage-column column-categories"><?php echo date(get_option("date_format"), strtotime($signleCoupon['ends'])); ?></td>


																	<td class="manage-column column-categories">
																		<?php
																				if(!empty($couponStatus)){
																					if($couponStatus=="active"){ ?>
																						<input class="alert alert-success" type="button" value="<?php echo esc_html__('Active', 'listingpro-plugin'); ?>" >
																					<?php
																					}elseif($couponStatus=="expired"){ ?>
																						<input class="alert alert-danger" type="button" value="<?php echo esc_html__('Expired', 'listingpro-plugin'); ?>" >
																					<?php 
																					}elseif($couponStatus=="pending"){ ?>
																						<input class="alert alert-info" type="button" value="<?php echo esc_html__('Pending', 'listingpro-plugin'); ?>" >
																					<?php 
																					} ?>

																				
																		<?php
																			}
																		?>
																	</td>
																	
																	<td class="manage-column column-categories">
																		<form class="active_listing" name="lp_delte_this_coupon" method="POST" action="">
																			<input type="hidden" value="<?php echo $copIndex; ?>" name="couponinxed">
																			<input class="alert alert-danger" type="submit" name="lp_delte_coupon_submit" value="<?php echo esc_html__('Delete', 'listingpro-plugin'); ?>" >
																		</form>
																	</td>
																	
																	
																</tr>

																<?php
																$counter++;
															}
															?>
														
														
												</tbody>

												<tfoot>
													<tr>
														<td class="manage-column column-cb check-column"><label class="screen-reader-text" for="cb-select-all-2">Select All</label><input id="cb-select-all-2" type="checkbox"></td>

														<th class="manage-column column-title column-primary sortable desc">
															<a><span>Code</span>
																<span class="sorting-indicator"></span></a></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Discount value', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Total', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Available', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Start Date', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Ends Date', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Status', 'listingpro-plugin'); ?></th>
																<th class="manage-column column-tags"><?php echo esc_html__('Delete', 'listingpro-plugin'); ?></th>	
															</tr>
														</tfoot>
													</table>
													
	<?php } ?>