<?php

if (!function_exists('listing_all_extra_fields')) {
    function listing_all_extra_fields($postid)
    {
        global $listingpro_options;
        $lp_detail_page_styles = $listingpro_options['lp_detail_page_styles'];

        $output = '';
        $count = 0;
        $metaboxes = get_post_meta($postid, 'lp_' . strtolower(THEMENAME) . '_options_fields', true);
        $emptyFields = true;
        if (!empty($metaboxes)) {
            unset($metaboxes['lp_feature']);
            if (!empty($metaboxes)) {
                $numberOF = count($metaboxes);
                $output = null;
                $output .= '<div class="widget-box"><div class="features-listing extra-fields">';
                $output .= '<div class="post-row-header clearfix margin-bottom-15"><h3>' . esc_html__('Additional Details', 'listingpro') . '</h3></div>';
                $output .= '<ul>';

                foreach ($metaboxes as $slug => $value) {
                    if ($count <= 5) {
                        $queried_post = get_page_by_path($slug, OBJECT, 'form-fields');
                        if (!empty($queried_post)) {
                            $dieldsID = $queried_post->ID;

                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }

                            //new update 2.6.15
                            if (!empty($value) && $value == 'Yes' || $value == 'yes') {
                                $value  =   esc_html__('Yes', 'listingpro');
                            }

                            if (!empty($value) && $value == 'No' || $value == 'no') {
                                $value  =   esc_html__('No', 'listingpro');
                            }

                            //end new update 2.6.15
                            if (!empty($value)) {
                                $emptyFields = false;
                                if ($lp_detail_page_styles == 'lp_detail_page_styles5') {
                                    $output .= '<li class="lp-fields-for-details5 clearfix"><div class="lp-first-pull-left pull-left"><strong>' . get_the_title($dieldsID) . ':</strong></div><div class="pull-left"><span>' . $value . '</span></div></li>';
                                } else {
                                    $output .= '<li><strong>' . get_the_title($dieldsID) . ':</strong><span>' . $value . '</span></li>';
                                }
                            }
                        }
                    }
                    $count++;
                }

                $output .= '</ul>';
                if ($numberOF > 5) {
                    $output .= '<a class="show-all-timings" href="#">' . esc_html__('Show all', 'listingpro') . '</a>';
                }
                $output .= '<ul class="hidding-timings">';
                $count = 0;
                foreach ($metaboxes as $slug => $value) {
                    if ($count > 5) {
                        $queried_post = get_page_by_path($slug, OBJECT, 'form-fields');
                        if (!empty($queried_post)) {
                            $dieldsID = $queried_post->ID;
                            if (is_array($value)) {
                                $value = implode(', ', $value);
                            }

                            //new update 2.6.15
                            if (!empty($value) && $value == 'Yes' || $value == 'yes') {
                                $value  =   esc_html__('Yes', 'listingpro');
                            }

                            if (!empty($value) && $value == 'No' || $value == 'no') {
                                $value  =   esc_html__('No', 'listingpro');
                            }

                            //end new update 2.6.15
                            if (!empty($value)) {
                                $emptyFields = false;
                                $emptyFields = false;
                                if ($lp_detail_page_styles == 'lp_detail_page_styles5') {
                                    $output .= '<li class="lp-fields-for-details5 clearfix"><div class="lp-first-pull-left pull-left"><strong>' . get_the_title($dieldsID) . ':</strong></div><div class="pull-left"><span>' . $value . '</span></div></li>';
                                } else {
                                    $output .= '<li><strong>' . get_the_title($dieldsID) . ':</strong><span>' . $value . '</span></li>';
                                }
                            }
                        }
                    }
                    $count++;
                }
                $output .= '</ul></div></div>';
                // closing
            }
            if ($emptyFields) {
                $return = '';
            } else {
                $return = $output;
            }
            return $return;
        }
    }
}
