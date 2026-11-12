<?php
/*---------------------------------------------------
				listings reports page
----------------------------------------------------*/

/* post form code */
if (isset($_POST['listing_id']) && isset($_POST['lp_listing_report_submit'])) {

	$listing_id = $_POST['listing_id'];
	if (!empty($listing_id)) {
		$review_post = array(
			'ID'           => $listing_id,
			'post_status' => 'reported',
		);
		wp_update_post($review_post);

		listing_set_metabox('listing_reported', '', $listing_id);
		listing_set_metabox('listing_reported_by', '', $listing_id);

		if (get_option('lp_reported_listings') !== false) {
			$reportedLisingsArray = array();
			$delReview = array();
			$reportedLisings = get_option('lp_reported_listings');
			if (strpos($reportedLisings, ',') !== false) {
				$reportedLisingsArray = explode(",", $reportedLisings);
			} else {
				$reportedLisingsArray[] = $reportedLisings;
			}

			$delReview[] = $listing_id;
			$result = array_diff($reportedLisingsArray, $delReview);
			if (!empty($result)) {
				$newRevieids = implode(",", $result);
				update_option('lp_reported_listings', $newRevieids);
			} else {
				delete_option('lp_reported_listings');
			}
		}
	}
}
/* end post form code */

if (!function_exists('listingpro_flags_page')) {
	function listingpro_flags_page()
	{
		update_option('lp_unread_listing_report', 0);
?>
		<div class="wrap listingpro-coupons listingpro-coupons">
			<h1 class="wp-heading-inline"><?php esc_html_e('Flagged', 'listingpro-plugin'); ?></h1>

			<div class="clearfix"></div>
			<ul class="subsubsu">
				<li class="all"><a href="#" class="current" aria-current="page">All <span class="count"></span></a></li>
			</ul>

			<div id="posts-filter" method="get">

				<div class="listingpro_coupon_table">

					<table class="table wp-list-table widefat fixed striped posts">
						<thead>
							<tr>
								<!-- <th>No.</th> -->

								<td id="cb" class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
									<input id="cb-select-all-1" type="checkbox">
								</td>

								<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
									<a href=""><span>Reported Listing</span><span class="sorting-indicator"></span></a>
								</th>

								<th class="manage-column column-tags"><a href="">User</a></th>
								<th class="manage-column column-tags">Report Reason</th>
								<th class="manage-column column-tags"><a href="">Action</a></th>
							</tr>
						</thead>
						<tbody>
							<?php

							global $paged;
							if (get_option('lp_reported_listings') !== false) {
								$reportedLisings = get_option('lp_reported_listings');
								$ReportedLisints = array();
								if (strpos($reportedLisings, ',') !== false) {
									$ReportedLisints = explode(",", $reportedLisings);
								} else {
									$ReportedLisints[] = $reportedLisings;
								}

								$reports_query = new WP_Query(array(
									'post_type' => 'listing',
									'post__in' => $ReportedLisints,
									'post_status' => 'publish',
									'posts_per_page' => -1,
									'paged' => $paged,
								));
								if ($reports_query->have_posts()) {
									while ($reports_query->have_posts()) {
										$reports_query->the_post();
										$reportedCount = listing_get_metabox_by_ID('listing_reported', get_the_ID());
										$postReportedreason = listing_get_metabox_by_ID('listing_reported_reason', get_the_ID());

										if (!empty($reportedCount)) {
											if ($reportedCount > 1) {
												$reportedCount .= ' ' . esc_html__('Times', 'listingpro-plugin');
											} else {
												$reportedCount .= ' ' . esc_html__('Time', 'listingpro-plugin');
											}
										}
							?>

										<?php
										//new code 2.6.15
										if (!empty($postReportedreason)) {
											foreach ($postReportedreason as $getreason) {
										?>
												<tr>
													<th scope="row" class="check-column">
														<input id="cb-select-1" type="checkbox" name="post[]" value="1">
													</th>

													<!-- <td>1</td> -->
													<td class="author column-author"><?php echo get_the_title(); ?></td>
													<td class="manage-column column-categories"><?php echo get_the_author(); ?></td>
													<td class="manage-column column-categories"><?php echo $getreason; ?></td>
													<td class="manage-column column-categories">

														<form class="wp-core-ui" method="post">
															<input type="submit" name="lp_listing_report_submit" class="button action" value="<?php echo esc_html__('Confirm', 'listingpro-plugin'); ?>">
															<input type="hidden" name="listing_id" value="<?php echo get_the_ID(); ?>">
														</form>
													</td>
												</tr>
											<?php } ?>
										<?php } ?>
										<!-- new code 2.6.15 -->
								<?php
									}
									/* endwhile */
								}
								/* endif */
							} else { ?>
								<tr>
									<td class="colspanchange" colspan="9">
										<?php esc_html_e('Sorry! There is no report found', 'listingpro-plugin'); ?>
									</td>
								</tr>
							<?php } ?>

						</tbody>

						<tfoot>
							<tr>
								<th class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-2">Select All</label>
									<input id="cb-select-all-2" type="checkbox">
								</th>
								<th scope="col" id="title" class="manage-column column-title column-primary sortable desc">
									<a href=""><span>Reported Listing</span><span class="sorting-indicator"></span></a>
								</th>

								<th class="manage-column column-tags"><a href="">User</a></th>
								<th class="manage-column column-tags">Total Reports</th>
								<th class="manage-column column-tags"><a href="">Action</a></th>
							</tr>
						</tfoot>
					</table>

				</div>
			</div>
		</div>

<?php
	}
}
?>