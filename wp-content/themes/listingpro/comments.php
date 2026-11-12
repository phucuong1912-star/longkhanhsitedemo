<?php
/**
 * The template for displaying comments.
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package _s
 */

if (comments_open ()) { ?>
									<div class="comments-container margin-top-50">
<?php									
			if (is_singular('listing')){
			
			
												
												if (post_password_required ()) { ?>
													<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'listingpro'); ?></p><?php
													return;
												}
												
												if (have_comments ()) { ?>
													
													<div class="comments-container padding-left-40 padding-right-40 clearfix margin-top-50">
														<div class="comments-header clearfix">
															<h3>
																<?php printf(_n(__('1 Review', 'listingpro') . ' %2$s', '%1$s ' . __('Reviews', 'listingpro') . ' %2$s', get_comments_number()), number_format_i18n(get_comments_number()), '' . ''); ?>
															</h3>
														</div>
														<div class="comments-posts">
															<?php wp_list_comments(array('callback' => 'listingpro_comments')); ?>
														</div>
													</div>
													<?php
												}

													$fields[ 'comment_notes_before' ]= '
													<div class="form-group margin-bottom-40">
														<p class="padding-bottom-15">'.__('Your Rating for this listing', 'listingpro').'</p>
														<div class="list-style-none form-review-stars">
															<input type="hidden" name="rate" class="rating-tooltip" data-filled="fa fa-star fa-2x" data-empty="fa fa-star-o fa-2x" />
														</div>
													</div>';

												$fields[ 'comment_notes_after' ] = '';
											
												$fields[ 'title_reply' ] = __('Rate us and Write a Review', 'listingpro');
												
												$fields[ 'title_reply_to' ] = __('Leave a Reply to %s', 'listingpro');
												
												$fields[ 'class_submit' ] = 'lp-review-btn btn-second-hover';
												
												?>
												<div class="comments-form padding-left-40 padding-right-40 padding-bottom-40 clearfix" id="submitreview">
												
												<div class="comments-inner-container margin-top-10">
												
													<?php
														comment_form($fields);  
														?>
												</div>
												</div>

			<?php 
		}else{
		

												
												if (post_password_required ()) { ?>
													<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'listingpro'); ?></p><?php
													return;
												}
												
												if (have_comments ()) { ?>
													
													<div class="comments-container clearfix">
														<div class="comments-header clearfix">
															<h3>
																<?php printf(_n(__('1 Comment', 'listingpro') . ' %2$s', '%1$s ' . __('Comments', 'listingpro') . ' %2$s', get_comments_number()), number_format_i18n(get_comments_number()), '' . ''); ?>
															</h3>
														</div>
														<div class="comments-posts">
															<?php wp_list_comments(array('callback' => 'listingpro_comments')); ?>
														</div>
													</div>
													<?php
												}


												$fields[ 'comment_notes_before' ]=$fields[ 'comment_notes_after' ] = '';
											
												$fields[ 'title_reply' ] = __('Leave a Comment', 'listingpro');
												
												$fields[ 'title_reply_to' ] = __('Leave a Reply to %s', 'listingpro');
												
												$fields[ 'class_submit' ] = 'lp-review-btn btn-second-hover';
												$fields[ 'label_submit' ] = __('Post a comment', 'listingpro');
												
												?>
												<div class="comments-form">
													<div class="comments-inner-container">
														<?php
														comment_form($fields);  
														?>
													</div>
												</div>

	<?php } ?>
		</div>
<?php	} 
 echo paginate_comments_links()
?>