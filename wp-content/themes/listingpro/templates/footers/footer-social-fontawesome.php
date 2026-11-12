<?php

/**
 * Template part for footer social.
 * templates/footers/footer-social
 * @version 2.0
 */

$fb = lp_theme_option('fb');
$tw = lp_theme_option('tw');
$insta = lp_theme_option('insta');
$tumb = lp_theme_option('tumb');
$fyout = lp_theme_option('f-yout');
$flinked = lp_theme_option('f-linked');
$fpintereset = lp_theme_option('f-pintereset');
$fvk = lp_theme_option('f-vk');
?>

<?php if (!empty($tw) || !empty($fb) || !empty($insta) || !empty($tumb) || !empty($fpintereset) || !empty($flinked) || !empty($fyout) || !empty($fvk)) { ?>
	<ul class="social-icons footer-social-icons font-awesome-icons">
		<?php if (!empty($fb)) { ?>
			<li>
				<a href="<?php echo esc_url($fb); ?>" target="_blank">
					<i class="fa-brands fa-square-facebook"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($tw)) { ?>
			<li>
				<a href="<?php echo esc_url($tw); ?>" target="_blank">
					<i class="fa-brands fa-square-x-twitter"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($insta)) { ?>
			<li>
				<a href="<?php echo esc_url($insta); ?>" target="_blank">
					<i class="fa-brands fa-square-instagram"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($fyout)) { ?>
			<li>
				<a href="<?php echo esc_url($fyout); ?>" target="_blank">
					<i class="fa-brands fa-youtube"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($flinked)) { ?>
			<li>
				<a href="<?php echo esc_url($flinked); ?>" target="_blank">
					<i class="fa-brands fa-linkedin"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($fpintereset)) { ?>
			<li>
				<a href="<?php echo esc_url($fpintereset); ?>" target="_blank">
					<i class="fa-brands fa-pinterest"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($tumb)) { ?>
			<li>
				<a href="<?php echo esc_url($tumb); ?>" target="_blank">
					<i class="fa-solid fa-whiskey-glass"></i>
				</a>
			</li>
		<?php } ?>
		<?php if (!empty($fvk)) { ?>
			<li>
				<a href="<?php echo esc_url($fvk); ?>" target="_blank">
					<i class="fa-brands fa-vk"></i>
				</a>
			</li>
		<?php } ?>

	</ul>
<?php } ?>