<?php

/**
 * The template for displaying Search Page.
 */

get_header();

// store the post type from the URL string
if (isset($_GET['post_type'])) {
	$post_type = sanitize_file_name(wp_unslash($_GET['post_type']));
}
// check to see if there was a post type in the
// URL string and if a results template for that
// post type actually exists
if (isset($post_type) && locate_template('search-' . $post_type . '.php')) {
	// if so, load that template
	get_template_part('search', $post_type);

	// and then exit out
	exit;
} else {
?>
	<!--==================================Section Open=================================-->
	<section>
		<div class="container page-container-five">
			<div class="row">
				<?php get_template_part('loop', 'archive'); ?>
			</div>
		</div>
	</section>
	<!--==================================Section Close=================================-->
<?php } ?>
<?php get_footer(); ?>