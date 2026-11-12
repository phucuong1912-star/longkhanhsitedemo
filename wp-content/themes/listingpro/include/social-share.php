<?php

/**
 * Social Shares Butoons
 *
 */
if (!function_exists('listingpro_social_sharing_buttons')) {
	function listingpro_social_sharing_buttons($name)
	{
		global $post;
		if (is_singular('listing') || is_singular('post') || is_singular('events') || is_home()) {
			$listingURL = urlencode(get_permalink());
			$listingTitle = str_replace(' ', '%20', get_the_title());
			$listingThumbnail = wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'full');
			$img_url = '';
			if (isset($listingThumbnail[0])) {
				$img_url = urlencode($listingThumbnail[0]);
			}
			$twitterURL = 'https://x.com/intent/post?text=' . rawurlencode(html_entity_decode(get_the_title(), ENT_QUOTES, 'UTF-8') . ' ' . get_permalink());
			$facebookURL = 'https://www.facebook.com/sharer/sharer.php?u=' . $listingURL;
			$pinterest = 'https://pinterest.com/pin/create/button/?url=' . $listingURL . '&media=' . $img_url . '&description=' . $listingTitle;
			$linkedin = 'http://www.linkedin.com/shareArticle?mini=true&url=' . $listingURL;
			$reddit = 'https://www.reddit.com/login?dest=https%3A%2F%2Fwww.reddit.com%2Fsubmit%3Ftitle%3D' . $listingTitle . '%26url%3D' . $listingURL;
			$stumbleupon = 'https://www.stumbleupon.com/submit?title=' . $listingTitle . '&url=' . $listingURL;
			if ($name == 'facebook') {
				return $facebookURL;
			}
			if ($name == 'twitter') {
				return $twitterURL;
			}
			if ($name == 'pinterest') {
				return $pinterest;
			}
			if ($name == 'linkedin') {
				return $linkedin;
			}
			if ($name == 'reddit') {
				return $reddit;
			}
			if ($name == 'stumbleupon') {
				return $stumbleupon;
			}
		}
	}
}
