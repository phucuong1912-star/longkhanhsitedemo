<?php
class listingpro_socialinfo extends WP_Widget
{

	function __construct()
	{
		$widget_ops = array(
			'classname' => 'social_widget',
			'description' => 'Social info.'
		);
		$control_ops = array('width' => 80, 'height' => 80);
		parent::__construct(false, 'listingpro social info', $widget_ops, $control_ops);
	}

	function form($instance)
	{
	}

	function update($new_instance, $old_instance)
	{
		$instance = $old_instance;

		return $instance;
	}

	function widget($args, $instance)
	{
		extract($args);
		$class = apply_filters('widget_class', empty($instance['class']) ? '' : $instance['class'], $instance);

		echo wp_kses_post($before_widget);


		global $listingpro_options;

		$fb = $listingpro_options['fb'];
		$tw = $listingpro_options['tw'];
		$insta = $listingpro_options['insta'];
		$tumb = $listingpro_options['tumb'];

		$fyout = $listingpro_options['f-yout'];
		$flinked = $listingpro_options['f-linked'];
		$fpintereset = $listingpro_options['f-pintereset'];
		$fvk = $listingpro_options['f-vk'];



		echo '<ul class="social-icons lp-new-social-widget">';
		if (!empty($fb)) {
			echo '<li>
								<a href="' . $fb . '" target="_blank">
									<i class="fa fa-facebook" aria-hidden="true"></i>
								</a>
							</li>';
		}
		if (!empty($tw)) {
			echo '<li>
								<a href="' . $tw . '" target="_blank">
									<i class="fa-brands fa-x-twitter" aria-hidden="true"></i>
								</a>
							</li>';
		}

		if (!empty($insta)) {
			echo '<li>
								<a href="' . $insta . '" target="_blank">
									
									<i class="fa fa-instagram" aria-hidden="true"></i>
								</a>
							</li>';
		}

		if (!empty($tumb)) {
			echo '<li>
								<a href="' . $tumb . '" target="_blank">
									<i class="fa fa-tumblr" aria-hidden="true"></i>
								</a>
							</li>';
		}

		if (!empty($fyout)) {
			echo '<li>
								<a href="' . $fyout . '" target="_blank">
									<i class="fa fa-youtube-play" aria-hidden="true"></i>
								</a>
							</li>';
		}

		if (!empty($flinked)) {
			echo '<li>
								<a href="' . $flinked . '" target="_blank">
									
									<i class="fa fa-linkedin" aria-hidden="true"></i>
								</a>
							</li>';
		}


		if (!empty($fpintereset)) {
			echo '<li>
								<a href="' . $fpintereset . '" target="_blank">
									
									<i class="fa fa-pinterest-p" aria-hidden="true"></i>
								</a>
							</li>';
		}
		if (!empty($fvk)) {
			echo '<li>
								<a href="' . $fvk . '" target="_blank">
									
									<i class="fa fa-vk" aria-hidden="true"></i>
								</a>
							</li>';
		}


		echo '</ul>';

?>

        <?php
		echo wp_kses_post($after_widget);
	}
}

add_action('widgets_init', 'listingpro_socialinfo_cb');
if (!function_exists('listingpro_socialinfo_cb')) {
	function listingpro_socialinfo_cb()
	{
		register_widget('listingpro_socialinfo');
	}
}

		?>