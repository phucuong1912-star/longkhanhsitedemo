<?php

/**
 * Template part for footer style 2.
 * templates/footers/footer-style2
 * @version 2.0
*/
?>
<div class="clearfix"></div>
<footer class="footer-style2 padding-top-60 padding-bottom-60">
		 <div class="container">
	            <div class="row">
	                <?php
					global $listingpro_options;
	                    $grid = $listingpro_options['footer_layout'] ? $listingpro_options['footer_layout'] : '12';

	                    $i = 1;
	                    foreach (explode('-', $grid) as $g) {
	                        echo '<div class="clearfix col-md-' . $g . ' col-' . $i . '">';
	                        dynamic_sidebar("footer-sidebar-$i");
	                        echo '</div>';
	                        $i++;
	                    }

	                ?>
	            </div>
	        </div>

</footer>