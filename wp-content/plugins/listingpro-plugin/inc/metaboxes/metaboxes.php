<?php
if (!function_exists('get_tabs_labels')) {
    function get_tabs_labels($settings)
    {
        $output = null;
        $class = null;

        if (isset($settings['match']) && !empty($settings['match'])) {
            if (is_array($settings['match'])) {
                $matches = $settings['match'];
                foreach ($matches as $match) {
                    if (!empty($settings['child_of'])) {
                        $class .= 'child-meta ' . $settings['child_of'] . '-for-' . $match;
                        $class .= ' ';
                    } else {
                        echo $class .= '';
                    }
                }
            } else {
                if (!empty($settings['child_of']) && !empty($settings['match'])) {
                    $class .= 'child-meta ' . $settings['child_of'] . '-for-' . $settings['match'];
                } else {
                    $class .= '';
                }
            }
            $output .= 'class="' . $class . '" ';
            echo $output;
        }
    }
}
/* increament */
if (!function_exists('settings_increment')) {
    function settings_increment($settings)
    {

?>
        <tr id="lp_field_<?php echo wp_kses_post($settings['id']); ?>" class="type_inrement" data-id="<?php echo wp_kses_post($settings['id']); ?>" data-name="<?php echo wp_kses_post($settings['name']); ?>" data-value="<?php //echo wp_kses_post($settings['value']); 
                                                                                                                                                                                                                            ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td class="<?php echo wp_kses_post($settings['id']); ?>">
                <div class="btn-container clearfix">
                    <a data-remove='<?php echo esc_attr__('Remove', 'listingpro-plugin') ?>' class="metaincbtn button button-primary button-large lp-secondary-btn btn-first-hover"><?php echo esc_html__('+ Add More', 'listingpro-plugin'); ?></a>

                    <?php
                    $valuecount = 0;
                    if (!empty($settings['value'])) {
                        foreach ($settings['value'] as $option) {
                            if (!empty($option)) {
                                $valuecount++;
                                print '<div class="lp-addmore-wrap">';
                                print '<input type="text" name="' . $settings['id'] . '[' . $valuecount . ']" value="' . $option . '" ';

                                print '>';

                                print '<a href="" class="lp-remove-more">' . esc_html__('Remove', 'listingpro-plugin') . '</a>';

                                print '</div>';
                            }
                        }
                    } else { ?>
                        <div class="lp-addmore-wrap">
                            <input type="text" name="<?php echo wp_kses_post($settings['id']); ?>[1]" id="<?php echo wp_kses_post($settings['id']); ?>" value="" />
                            <a href="" class="lp-remove-more"><?php echo esc_html__('Remove', 'listingpro-plugin'); ?></a>
                        </div>
                    <?php } ?>
                </div>
            </td>
        </tr><?php
            }
        }
        /* faqs */
        if (!function_exists('settings_faqsd')) {
            function settings_faqsd($settings)
            { ?>

        <?php
                if (count($settings['value']) > 0) {
                    $faqs = listing_get_metabox('faqs');


                    if (!empty($faqs) && count($faqs) > 0) {
                        $faq = '';
                        $faqans = '';
                        if (isset($settings['value']['faq']) && !empty($settings['value']['faq']) && isset($settings['value']['faqans']) && !empty($settings['value']['faqans'])) {
                            $faq = $settings['value']['faq'];
                            $faqans = $settings['value']['faqans'];


        ?>

                    <div class="post-row faq-section padding-top-40 clearfix">
                        <div class="post-row-header clearfix margin-bottom-15">
                            <h3><?php echo esc_html__('Quick questions', 'listingpro-plugin'); ?></h3>
                        </div>
                        <div class="post-row-accordion">
                            <div id="accordion">

                                <?php for ($i = 1; $i <= (count($faq)); $i++) { ?>
                                    <h5>
                                        <span>
                                            Q
                                        </span>
                                        <span class="accordion-title"><input type="text" class="form-control" name="faqs[0][<?php echo $i; ?>]" id="inputLinkedIn" value="<?php echo esc_html($faq[$i]); ?>"></span>
                                    </h5>
                                    <div>
                                        <p>
                                            <textarea style="width:100%" rows="3" class="form-control" name="faqs[1][<?php echo $i; ?>]" rows="8" id="inputDescription"><?php echo esc_html($faqans[$i]); ?></textarea>
                                        </p>
                                    </div><!-- accordion tab -->
                                <?php } ?>

                            </div>
                        </div>
                    </div>

        <?php
                        }
                    }
                }
        ?>



    <?php
            }
        }

        /* by zaheer on 23 feb */
        if (!function_exists('settings_static')) {
            function settings_static($settings)
            { ?>
        <tr id="lp_field_<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="text" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo wp_kses_post($settings['value']); ?>" />
            </td>
        </tr><?php
            }
        }
        /* by zaheer on 23 feb */


        if (!function_exists('settings_displayimage')) {
            function settings_displayimage($settings)
            {
                //$src = $settings['src'];
                ?>

        <label for="<?php echo wp_kses_post($settings['id']); ?>">
            <strong><?php echo wp_kses_post($settings['std']); ?></strong><br>
            <img src="<?php echo esc_url($settings['src']); ?>" alt="<?php echo wp_kses_post($settings['id']); ?>" />
        </label>
    <?php

            }
        }

        /* array */
        if (!function_exists('settings_array')) {
            function settings_array($settings)
            { ?>
        <tr style="display:none" id="lp_field_<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="hidden" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php print_r($settings['value']); ?>" />
            </td>
        </tr><?php
            }
        }
        /* TIMING FUNCTION */
        if (!function_exists('settings_timings')) {
            function settings_timings($settings)
            {
                if (isset($_GET['post']) && isset($_GET['action']) && $_GET['action'] == 'edit') {
                    $fakeID    = $_GET['post'];
                    $edit = true;
                } else {
                    $fakeID    = '';
                    $edit = false;
                }
                ?>
        <tr id="field_<?php echo wp_kses_post($settings['id']); ?>">
            <th><?php echo esc_html__('Add Your Business Hours.', 'listingpro-plugin'); ?></th>
            <td>
                <?php
                $output = LP_operational_hours_form($fakeID, $edit);
                echo $output;
                ?>
            </td>
        </tr><?php
            }
        }

        /* faqs */
        if (!function_exists('settings_faqs')) {
            function settings_faqs($settings)
            {
                global $listingpro_options;
                $faqTab = $listingpro_options['listing_faq_tabs_text'];
                $faqTitle = $listingpro_options['listing_faq_text'];
                $faqTab = preg_replace("/[^A-Za-z]+/", "", $faqTab);
                $ansString = esc_html__('Answer', 'listingpro-plugin');
                $faqs = $settings['value'];
                if (!empty($faqs) && count($faqs) > 0) {
                    $faq = $faqs['faq'];
                    $faqans = $faqs['faqans'];
                }

                ?>
        <tr id="field_<?php echo wp_kses_post($settings['id']); ?>">
            <th><?php echo esc_html__('FAQ helps to understand.', 'listingpro-plugin'); ?></th>
            <td>
                <div id="tabs" data-qstring="<?php echo $faqTab; ?>" data-ansstring="<?php echo $ansString; ?>" data-faqtitle="<?php echo $faqTitle; ?>">
                    <div class="btn-container clearfix">
                        <ul>
                            <?php
                            if (!empty($faq) && count($faq) > 0) {
                                foreach ($faq as $key => $sfaq) {
                                    echo '<li><a href="#tabs-' . $key . '">' . $faqTab . ' ' . $key . '</a></li>';
                                }
                            } else {
                            ?>
                                <li><a href="#tabs-1"><?php echo $faqTab; ?> 1</a></li>
                            <?php } ?>

                        </ul>
                        <a id="tabsbtn" class="lp-secondary-btn btn-first-hover"><?php echo esc_html__('+ Add New', 'listingpro-plugin'); ?></a>
                    </div>
                    <div class="clear"></div>
                    <?php
                    if (!empty($faq) && count($faq) > 0) {
                        foreach ($faq as $key => $sfaq) {
                            echo '<div id="tabs-' . $key . '">
									<div class="form-group">
											<label for="inputLinkedIn">' . $faqTitle . ' ' . $key . '</label>
											<input type="text" class="form-control" name="faqs[faq][' . $key . ']" id="inputLinkedIn" placeholder="' . $faqTitle . ' ' . $key . '" value="' . $sfaq . '">
									</div>
									<div class="form-group">
											<label for="inputDescription">' . $ansString . ' ' . $key . '</label>
											<textarea class="form-control" name="faqs[faqans][' . $key . ']" rows="8" id="inputDescription">' . $faqans[$key] . '</textarea>
									</div>
								</div>';
                        }
                    } else {
                    ?>
                        <div id="tabs-1">
                            <div class="form-group">
                                <label for="inputLinkedIn"><?php echo $faqTitle; ?> 1</label>
                                <input type="text" class="form-control" name="faqs[faq][1]" id="inputLinkedIn" placeholder="<?php echo esc_html__('FAQ 1', 'listingpro-plugin'); ?>">
                            </div>
                            <div class="form-group">
                                <label for="inputDescription"><?php echo $ansString; ?> 1</label>
                                <textarea class="form-control" name="faqs[faqans][1]" rows="8" id="inputDescription"></textarea>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </td>
        </tr>
    <?php

            }
        }

        if (!function_exists('settings_check')) {
            function settings_check($settings)
            {
                $default = $settings['value'];
    ?>
        <tr id="field_<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="checkbox" class="yesno" id="<?php echo wp_kses_post($settings['id']); ?>" name="<?php echo wp_kses_post($settings['id']); ?>" value="Yes" <?php echo checked($default, esc_html__('Yes', 'listingpro-plugin'), false); ?> />

            </td>

        </tr><?php
            }
        }



        if (!function_exists('settings_checkbox')) {
            function settings_checkbox($settings)
            {
                $default = $settings['value'];
                $datashow = $datahide = $klass = "";
                if (!empty($settings['hide'])) {
                    $klass = " check-show-hide";
                    $datahide = $settings['hide'];
                }
                if (!empty($settings['show'])) {
                    $klass = " check-show-hide";
                    $datashow = $settings['show'];
                } ?>
        <tr id="field_<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <label class="switch">
                    <input type="hidden" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="0" />
                    <input type="checkbox" class="yesno<?php echo wp_kses_post($klass); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" data-show="<?php echo wp_kses_post($datashow); ?>" data-hide="<?php echo wp_kses_post($datahide); ?>" name="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo esc_html__('Yes', 'listingpro-plugin'); ?>" <?php echo checked($default, esc_html__('Yes', 'listingpro-plugin'), false); ?> />

                    <div class="slider round"></div>
                </label>
            </td>

        </tr><?php
            }
        }

        if (!function_exists('settings_wysiwyg')) {
            function settings_wysiwyg($settings)
            {
                ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>" <?php echo get_tabs_labels($settings); ?>>
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <?php
                $settings = array(

                    'wpautop' => true,

                    'media_buttons' => true,

                    'tinymce' => array(

                        'theme_advanced_buttons1' => 'bold,italic,underline,blockquote,separator,strikethrough,bullist,numlist,justifyleft,justifycenter,justifyright,undo,redo,link,unlink,fullscreen',

                        'theme_advanced_buttons2' => 'pastetext,pasteword,removeformat,|,charmap,|,outdent,indent,|,undo,redo',

                        'theme_advanced_buttons3' => '',

                        'theme_advanced_buttons4' => ''

                    ),

                    'quicktags' => array(

                        'buttons' => 'b,i,ul,ol,li,link,close'

                    )

                );
                wp_editor($settings['value'], $settings['id'], $settings);
                ?>
            </td>
        </tr><?php
            }
        }


        if (!function_exists('settings_checkboxes')) {
            function settings_checkboxes($settings)
            {
                $cheched = '';

                ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>"><?php echo wp_kses_post($settings['name']); ?></label>
            </th>
            <?php if (!empty($settings['options'])) { ?>
                <td data-id="<?php echo wp_kses_post($settings['id']); ?>">
                    <input type="button" class="check button check-all-btn" value="Select all" />
                    <?php
                    foreach ($settings['options'] as $key => $value) {
                        echo '<div class="clear single-check" >';
                        if (!empty($settings['value'])) {
                            if (in_array($key, $settings['value'])) {
                                $cheched =  "checked";
                            } else {
                                $cheched = '';
                            }
                        }
                        print '<input type="checkbox" style="margin-right:5px;" name="' . $settings['id'] . '[]" value="' . $key . '" ' . $cheched . ' />';

                        print '<span style="margin-right:20px;"><strong>' . $value . '</strong></span>';

                        echo '</div>';
                    } ?>

                </td>

                <td>
                    <span>
                        <?php echo wp_kses_post($settings['desc']); ?>
                    </span>
                </td>
            <?php } else {
            ?>
                <td>
                    <p>There is no Option available</p>
                </td>
            <?php
                }
            ?>
        </tr><?php

            }
        }
        if (!function_exists('settings_textarea')) {
            function settings_textarea($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>" <?php echo get_tabs_labels($settings); ?>>
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <?php if (is_array($settings['value'])) {
                    if ($count($settings['value'])) {
                        $value = $settings['value'];
                    } else {
                        $value = '';
                    }
                } else {
                    $value = $settings['value'];
                }
                ?>
                <textarea rows="5" name="<?php echo esc_attr($settings['id']); ?>"><?php echo $value; ?></textarea>
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_text')) {
            function settings_text($settings)
            { ?>
        <tr id="lp_field_<?php echo wp_kses_post($settings['id']); ?>" <?php echo get_tabs_labels($settings); ?>>
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="text" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo wp_kses_post($settings['value']); ?>" />
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_hidden')) {
            function settings_hidden($settings)
            {
                ?>
        <input type="hidden" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo is_array($settings['value']) ? esc_attr(implode(',', $settings['value'])) : esc_attr($settings['value']); ?>" />
    <?php
            }
        }
        if (!function_exists('settings_file')) {
            function settings_file($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="text" id="<?php echo wp_kses_post($settings['id']); ?>" name="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo wp_kses_post($settings['value']); ?>" placeholder="Image URL" size="" />
                <a href="#" class="button insert-images theme_button format" onclick="browseimage('<?php echo wp_kses_post($settings['id']); ?>')"><?php esc_html_e('Insert image', "cland"); ?></a>
                <p class="description"><?php if (isset($settings['std'])) {
                                            echo wp_kses_post($settings['std']);
                                        } ?></p>
            </td>

        </tr><?php
            }
        }
        if (!function_exists('settings_selectbox')) {
            function settings_selectbox($settings)
            {
                $settings['options'] = array('true', 'default', 'false'); ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <select class="selectbox" name="<?php echo wp_kses_post($settings['id']); ?>" data-value="<?php print $settings['value']; ?>"><?php
                                                                                                                                                foreach ($settings['options'] as $meta) {
                                                                                                                                                    echo '<option value="' . $meta . '" ';
                                                                                                                                                    echo wp_kses_post($meta) == $settings['value'] ? 'selected ' : '';
                                                                                                                                                    echo '>' . $meta . '</option>';
                                                                                                                                                } ?>
                </select>
            </td>
        </tr><?php
            }
        }


        if (!function_exists('settings_mselect')) {
            function settings_mselect($settings)
            {
                $selected = '';
                //$settings['options'] = array('top', 'slider_bottom', 'top_transparent'); 
                ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div class="type_select add_item_medium">
                    <select class="medium multiple-select-options" name="<?php echo wp_kses_post($settings['id']); ?>[]" data-value="<?php //print $settings['value'];
                                                                                                                                        ?>" multiple="multiple"><?php

                                                                                                                                                                                                if (!empty($settings['value'])) {
                                                                                                                                                                                                    foreach ($settings['value'] as $skey => $sval) {
                                                                                                                                                                                                        foreach ($settings['options'] as $key => $value) {
                                                                                                                                                                                                            if ($sval == $key) {
                                                                                                                                                                                                                echo '<option selected value="' . $key . '">' . $value . '</option>';
                                                                                                                                                                                                            }
                                                                                                                                                                                                        }
                                                                                                                                                                                                    }
                                                                                                                                                                                                }

                                                                                                                                                                                                foreach ($settings['options'] as $key => $value) {
                                                                                                                                                                                                    if (!empty($settings['value'])) {
                                                                                                                                                                                                        if (in_array($key, $settings['value'])) {
                                                                                                                                                                                                        } else {
                                                                                                                                                                                                            echo '<option value="' . $key . '">' . $value . '</option>';
                                                                                                                                                                                                        }
                                                                                                                                                                                                    } else {
                                                                                                                                                                                                        echo '<option value="' . $key . '">' . $value . '</option>';
                                                                                                                                                                                                    }
                                                                                                                                                                                                } ?>
                    </select>
                </div>
            </td>
        </tr><?php
            }
        }

        if (!function_exists('settings_mselectbox')) {
            function settings_mselectbox($settings)
            {
                $settings['options'] = array('top', 'slider_bottom', 'top_transparent'); ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <select class="selectbox" name="<?php echo wp_kses_post($settings['id']); ?>" data-value="<?php print $settings['value']; ?>"><?php
                                                                                                                                                foreach ($settings['options'] as $meta) {
                                                                                                                                                    echo '<option value="' . $meta . '" ';
                                                                                                                                                    echo wp_kses_post($meta) == $settings['value'] ? 'selected ' : '';
                                                                                                                                                    echo '>' . $meta . '</option>';
                                                                                                                                                } ?>
                </select>
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_layoutpage')) {
            function settings_layoutpage($settings) {}
        }
        if (!function_exists('settings_layout')) {
            function settings_layout($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div class="type_layout">
                    <a href="javascript:;" data-value="left" class="left-sidebar"></a>
                    <a href="javascript:;" data-value="right" class="right-sidebar"></a>
                    <a href="javascript:;" data-value="full" class="without-sidebar"></a>
                    <input name="<?php echo wp_kses_post($settings['id']); ?>" type="hidden" value="<?php echo wp_kses_post($settings['value']); ?>" />
                </div>
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_radio')) {
            function settings_radio($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>"><?php echo wp_kses_post($settings['name']); ?></label>

            </th>
            <td>
                <div class="type_radio"><?php
                                        foreach ($settings['options'] as $option) {
                                            print '<input type="radio" style="margin-right:5px;" name="' . $settings['id'] . '" value="' . $option . '" ';
                                            print $option == $settings['value'] ? 'checked ' : '';
                                            print '><span style="margin-right:20px;">' . $option . '</span><br />';
                                        } ?>
                </div>
                <span>
                    <?php echo wp_kses_post($settings['desc']); ?>
                </span>
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_color')) {
            function settings_color($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div class="color_selector">
                    <div class="color_picker">
                        <div style="background-color: <?php echo wp_kses_post($settings['value']); ?>;" class="color_picker_inner"></div>
                    </div>
                    <input type="text" class="color_picker_value" id="<?php echo wp_kses_post($settings['id']); ?>" name="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo wp_kses_post($settings['value']); ?>" />
                </div>
            </td>
        </tr><?php
            }
        }



        if (!function_exists('settings_select')) {
            function settings_select($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div class="type_select add_item_medium">
                    <select class="medium" name="<?php echo wp_kses_post($settings['id']); ?>" data-value="<?php print $settings['value']; ?>"><?php
                                                                                                                                                if (!empty($settings['options'])) {
                                                                                                                                                    foreach ($settings['options'] as $key => $value) {
                                                                                                                                                        if ($key == $settings['value']) {
                                                                                                                                                            $selected =  "selected";
                                                                                                                                                        } else {
                                                                                                                                                            $selected = '';
                                                                                                                                                        }
                                                                                                                                                        echo '<option ' . $selected . ' value="' . $key . '">' . $value . '</option>';
                                                                                                                                                    }
                                                                                                                                                } ?>
                    </select>
                    <?php
                    if ($settings['id'] == "field-type") {
                        echo '<input type="hidden" name="lp_field_filter_type"  id="lp_field_filter_type" value="" />';
                    }
                    ?>
                </div>
            </td>
        </tr><?php
            }
        }

        if (!function_exists('settings_listing')) {
            function settings_listing($settings)
            {
                ?>

        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div class="type_listing add_item_medium">
                    <input type="text" name="s" class="form-control search-autocomplete lpautocomplete" placeholder="Search" autocomplete="off">
                    <input type="hidden" name="<?php echo wp_kses_post($settings['id']); ?>" id="lpautocompletSelec" value="<?php echo $settings['value']; ?>">
                    <i class="lp-listing-sping fa-li fa fa-spinner fa-spin"></i>
                    <?php
                    if (!empty($settings['value'])) {
                    ?>
                        <div class="lp-selected-listing">
                            <span class="lp-current-listing"><?php echo '<strong>' . esc_html__('Current : ', 'listingpro-plugin') . '</strong>';
                                                                echo get_the_title($settings['value']);  ?></span>
                        </div>
                    <?php } ?>
                    <div class="lpsuggesstion-box"></div>
                </div>
            </td>
        </tr><?php
            }
        }
        if (!function_exists('settings_gallery')) {
            function settings_gallery($settings)
            {
                global $post;
                $meta = get_post_meta($post->ID, 'gallery_image_ids', true);
                $gallery_thumbs = '';
                $button_text = ($meta) ? esc_html__('Edit Gallery', 'cland') : esc_html__('Upload Images', 'cland');
                if ($meta) {
                    $thumbs = explode(',', $meta);
                    foreach ($thumbs as $thumb) {
                        $gallery_thumbs .= '<li>' . wp_get_attachment_image($thumb, array(32, 32)) . '</li>';
                    }
                } ?>
        <tr id="">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="button" class="button" name="<?php echo wp_kses_post($settings['id']); ?>" id="gallery_images_upload" value="<?php echo wp_kses_post($button_text); ?>" />
                <input type="hidden" name="gallery_image_ids" id="gallery_image_ids" value="<?php echo wp_kses_post($meta) ? $meta : 'false'; ?>" />
                <ul class="gallery-thumbs"><?php echo wp_kses_post($gallery_thumbs); ?></ul>
            </td>
        </tr><?php
            }
        }

        if (!function_exists('settings_menu')) {
            function settings_menu($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <?php $menus = get_terms('nav_menu', array('hide_empty' => false));
                if (!$menus) {
                    echo '<p>' . sprintf(esc_html__('No menus have been created yet. <a href="%s">Create some</a>.', 'cland'), admin_url('nav-menus.php')) . '</p>';
                } else {
                    echo '<select name="' . $settings['id'] . '" data-value="' . $settings['value'] . '">';
                    echo '<option value="">' . esc_html__('Default', 'cland') . '</option>';
                    foreach ($menus as $menu) {
                        echo '<option value="' . $menu->term_id . '">' . $menu->name . '</option>';
                    }
                    echo '</select>';
                } ?>
            </td>
        </tr><?php
            }
        }

        /* for drop pin button */
        if (!function_exists('settings_mappinbuton')) {
            function settings_mappinbuton($settings)
            { ?>
        <?php add_thickbox(); ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <div id="modal-doppin" class="modal fade" data-lploctitlemap='<?php echo esc_html__("Your Location", "listingpro"); ?>'>
                    <div id="lp-custom-latlong" style="width:90%; height:250px"></div>
                </div>
            </td>
            <!-- Overlay for Popup -->
        </tr><?php
            }
        }
        /* for button */
        if (!function_exists('settings_custombutton')) {
            function settings_custombutton($settings)
            { ?>
        <tr id="<?php echo wp_kses_post($settings['id']); ?>">
            <td>
                <button id="<?php echo wp_kses_post($settings['id']); ?>" type="button"><?php echo wp_kses_post($settings['name']); ?></button>
            </td>
            <!-- Overlay for Popup -->
        </tr><?php
            }
        }
        if (!function_exists('settings_text_author')) {
            function settings_text_author($settings)
            { ?>
        <tr id="lp_field_<?php echo wp_kses_post($settings['id']); ?>" <?php echo get_tabs_labels($settings); ?>>

            <?php
                $uid = null;
                $unicename = null;
                if (isset($settings['value'])) {
                    $uid = $settings['value'];
                    $user = get_user_by('id', $uid);
                    if (!is_wp_error($user)) {
                        $unicename = $user->user_login;
                    }
                } ?>
            <th>
                <label for="<?php echo wp_kses_post($settings['id']); ?>">
                    <strong><?php echo wp_kses_post($settings['name']); ?></strong>
                    <span><?php echo wp_kses_post($settings['desc']); ?></span>
                </label>
            </th>
            <td>
                <input type="hidden" name="<?php echo wp_kses_post($settings['id']); ?>" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo wp_kses_post($settings['value']); ?>" />
                <input type="text" name="" id="<?php echo wp_kses_post($settings['id']); ?>" value="<?php echo $unicename; ?>" />
            </td>
        </tr><?php
            }
        }
