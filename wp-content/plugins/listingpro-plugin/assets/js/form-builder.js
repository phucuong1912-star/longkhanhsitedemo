jQuery(document).ready(function () {

    lp_form_builder_check_active_fields('.default-form-fields');

    lp_form_builder_check_active_fields();

    jQuery('.form-builder-fields-tabs li').click(function (e) {
        e.preventDefault();
        var $this = jQuery(this),
            targetID = $this.attr('id');

        jQuery('.form-builder-fields-tabs li.active').removeClass('active');
        $this.addClass('active');
        if (targetID == 'fes-default-fields') {
            jQuery('.fes-custom-fields').fadeOut(500, function () {
                jQuery('.fes-default-fields').fadeIn();
            });
        }
        if (targetID == 'fes-custom-fields') {

            jQuery('.fes-default-fields').fadeOut(500, function () {
                jQuery('.fes-custom-fields').fadeIn();
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: jQuery('#formajaxurl').val(),
                    data: {
                        'action': 'lp_get_extra_form_fields_in_tab',
                    },
                    success: function (data) {
                        jQuery('.fes-custom-fields .form-fields-list').html(data);
                        lp_form_builder_check_active_fields();
                        var clone, before, parent;
                        jQuery('.connected-sortable-inner').sortable({
                            connectWith: ".connected-sortable-inner",
                            helper: "clone",
                            revert: "invalid",
                            start: function (event, ui) {
                                jQuery(ui.item).show();
                                clone = jQuery(ui.item).clone();
                                before = jQuery(ui.item).prev();
                                parent = jQuery(ui.item).parent();
                            },
                            receive: function (event, ui) {
                                var itemIdentifier = jQuery(ui.item).attr('data-name'),
                                    itemLength = jQuery('.form-fields-sorter').find('li[data-name="' + itemIdentifier + '"]').length;
                                if (itemLength > 1) {
                                    jQuery('#lp-submit-builder-ovelay').find('i').hide();
                                    jQuery('#lp-submit-builder-ovelay').find('p').text('Duplicate not allowed').show();
                                    jQuery('#lp-submit-builder-ovelay').fadeIn();
                                    setTimeout(function () {
                                        jQuery('#lp-submit-builder-ovelay').fadeOut();
                                    }, 1500);
                                    ui.item.remove();
                                    if (before.length) before.after(clone);
                                    else parent.prepend(clone);
                                }
                            }
                        }).disableSelection();
                    }
                });
            });
        }
    });

    jQuery('.connected-sortable').sortable({
        connectWith: ".connected-sortable"
    }).disableSelection();
    lp_form_builder_inner_sorter();
    jQuery('.form-fields-sorter ul li input, .form-fields-sorter ul li textarea').keydown(function (e) {
        if (e.keyCode == 65 && e.ctrlKey) {
            e.target.select()
        }
    });
    jQuery('.form-fields-sorter ul li').each(function () {
        var $this = jQuery(this),
            titleT = $this.data('tip-title'),
            descT = $this.data('tip-description'),
            imgT = $this.data('tip-image'),
            placeT = $this.data('placeholder');
        $this.find('.tip-title').val(titleT);
        $this.find('.tip-description').val(descT);
        $this.find('.tip-image').val(imgT);
        $this.find('.field-placeholder').val(placeT);
    });
    jQuery('.listing_submit_form_add-new-section').click(function (e) {
        jQuery('.lp-submit-form-add-section').slideToggle();
    });
    // jQuery('.switch-submit-form-builder').click(function(e) {
    //     e.preventDefault();
    //     var $this = jQuery(this),
    //         enable_data = '';
    //     if (jQuery(this).hasClass('active')) {
    //         enable_data = 0;
    //         jQuery(this).removeClass('active');
    //     } else {
    //         enable_data = 1;
    //         jQuery(this).addClass('active');
    //     }
    //     if (enable_data == 0) {
    //         jQuery('.lp-form-builder-notice').show();
    //         jQuery('.lp-form-builder-inner-wrap').hide();
    //         jQuery('.form-builder-tabs-ul').addClass('hide-form-builder-tabs')
    //     } else {
    //         jQuery('.lp-form-builder-notice').hide();
    //         jQuery('.lp-form-builder-inner-wrap').show();
    //         jQuery('.form-builder-active-popup').fadeIn();
    //         jQuery('.form-builder-tabs-ul').removeClass('hide-form-builder-tabs')
    //     }
    //     jQuery.ajax({
    //         type: 'POST',
    //         url: ajaxurl,
    //         data: {
    //             'action': 'enable_form_builder',
    //             'enable_data': enable_data,
    //         },
    //         success: function(data) {
    //             //console.log(data);
    //         }
    //     });
    // });
    jQuery('.save-submit-form').click(function (e) {
        e.preventDefault();
        jQuery('#lp-submit-builder-ovelay').find('i').show();
        jQuery('#lp-submit-builder-ovelay').find('p').hide();
        jQuery('#lp-submit-builder-ovelay').fadeIn();
        var submitFormS = '[lp-submit-form]shortcode content[/lp-submit-form]',
            fieldShortcodes = '',
            targetInput = '.lp-submit-form-result',
            secWithFieldss = '';
        jQuery('.lp-form-builder-inner-wrap .form-fields-sorter li.form-section-wrapper').each(function () {
            var $this = jQuery(this),
                str = $this.data('id'),
                secID = str.split(':').join('').replace(/[^a-zA-Z]/g, ""),
                secLabel = $this.data('label'),
                secWithFields = '[lp-submit-form-sec id="' + secID + '" label="' + secLabel + '"]section content[/lp-submit-form-sec]',
                fieldsInSec = '';
            $this.find('ul li').each(function () {
                var $thiss = jQuery(this),
                    $thissS = $thiss.data('shortcode'),
                    tTitle = $thiss.attr('data-tip-title'),
                    tImage = $thiss.attr('data-tip-image'),
                    tDesc = $thiss.attr('data-tip-description'),
                    tPlace = $thiss.attr('data-placeholder'),
                    tLabel = $thiss.attr('data-label'),
                    tName = $thiss.attr('data-name'),
                    tRequired = $thiss.attr('data-required');


                if (tName == 'inputCity' || tName == 'inputCategory') {
                    var mulVal = $thiss.attr('data-multi');
                    $thissS = $thissS.replace(']', ' required="' + tRequired + '" multi="' + mulVal + '" label="' + tLabel + '" placeholder="' + tPlace + '" tiptitle="' + tTitle + '" tipimage="' + tImage + '" tipDesc="' + tDesc + '"]');
                } else if (tName == 'priceDetails') {
                    var priceFrom = $thiss.attr('data-pricefrom'),
                        priceTo = $thiss.attr('data-priceto');
                    $thissS = $thissS.replace(']', ' required="' + tRequired + '" priceto="' + priceTo + '" pricefrom="' + priceFrom + '" label="' + tLabel + '" placeholder="' + tPlace + '" tiptitle="' + tTitle + '" tipimage="' + tImage + '" tipDesc="' + tDesc + '"]');
                } else {
                    $thissS = $thissS.replace(']', ' required="' + tRequired + '" label="' + tLabel + '" placeholder="' + tPlace + '" tiptitle="' + tTitle + '" tipimage="' + tImage + '" tipDesc="' + tDesc + '"]');
                }
                fieldsInSec += $thissS;
            });
            secWithFieldss += secWithFields.replace('section content', fieldsInSec);
        });
        submitFormS = submitFormS.replace('shortcode content', secWithFieldss);

        jQuery(targetInput).val(submitFormS);
        jQuery.ajax({
            type: 'POST',
            url: jQuery('#formajaxurl').val(),
            dataType: 'JSON',
            data: {
                'action': 'lp_save_submit_form',
                'submitFormS': submitFormS,
                _ajax_nonce: lp_admin_vars.admin_nonce
            },
            success: function (data) {
                if (data.status == 'error') {
                    alert(data.message);
                } else {
                    jQuery('#lp-submit-builder-ovelay').find('i').hide();
                    jQuery('#lp-submit-builder-ovelay').find('p').text('Form Saved Successfully').show();
                    location.reload();
                }
                setTimeout(function () {
                    jQuery('#lp-submit-builder-ovelay').fadeOut();
                }, 1500);
            }
        });
    });
    jQuery(document).on('click', '.form-fields-sorter .form-section-wrapper .form-section-title .lp-el-edit', function (e) {
        var $this = jQuery(this),
            targetUl = $this.closest('.form-section-wrapper').find('ul');
        targetUl.slideToggle();
        $this.find('i').toggleClass('fa-chevron-down');
    });
});
jQuery(document).on('click', '.lp-form-builder-inner-wrap .lp-el-remove', function (e) {
    e.preventDefault();
    var $this = jQuery(this),
        target = $this.closest('li').attr('data-name');

    jQuery('#lp-submit-builder-ovelay').fadeIn();

    $this.find('i').removeClass('fa-trash-o').addClass('fa-spinner fa-spin');
    jQuery.ajax({
        type: 'POST',
        dataType: 'JSON',
        url: jQuery('#formajaxurl').val(),
        data: {
            'action': 'lp_remove_extra_form_field',
            'target': target,
            _ajax_nonce: lp_admin_vars.admin_nonce
        },
        success: function (data) {
            if (data.status == 'error') {
                alert(data.message);
                jQuery('#lp-submit-builder-ovelay').fadeOut();
            } else {
                jQuery('.save-submit-form').trigger('click');

                jQuery.ajax({
                    type: 'POST',
                    dataType: 'html',
                    url: jQuery('#formajaxurl').val(),
                    data: {
                        'action': 'lp_get_extra_form_fields_in_tab',
                    },
                    success: function (data) {
                        jQuery('.fes-custom-fields .form-fields-list').html(data);
                        lp_form_builder_inner_sorter();
                    }
                });
            }

        }
    });
    $this.closest('li').remove();
});
jQuery(document).on('click', '.add-submit-form-field', function (e) {
    e.preventDefault();

    var $this = jQuery(this),
        $thisWrap = $this.closest('.lp-submit-form-add-field');

    if ($this.hasClass('ajax-active')) {

    } else {

        $this.addClass('ajax-active');
        var field_required = "required='no' ",
            exclusive = "exclusive='yes' ",
            exclusive_val = 'yes',
            exclusive_cats = '',
            field_type = "type='" + $thisWrap.find('#field-type').val() + "' ",
            field_typee = $thisWrap.find('#field-type').val(),
            field_label = $thisWrap.find('#field-label').val();
        var field_name = '';


        if ($thisWrap.find('.switch-checkbox[name="field-exclusive"]').is(':checked')) {
            var exclusive = "exclusive='yes' ";

        } else {
            var assigned_cats = $thisWrap.find('#field-categories').val();
            var exclusive = "exclusive='no' assigned-cats='" + assigned_cats + "' ";
            exclusive_val = 'no';
            exclusive_cats = $thisWrap.find('#field-categories').val()
        }


        if ($thisWrap.find('#field-label').val() == '') {
            $thisWrap.find('#field-label').addClass('error');
        } else {
            $thisWrap.find('#field-label').removeClass('error');
        }
        if ($thisWrap.find('#field-name').val() == '') {
            $thisWrap.find('#field-name').addClass('error');
        } else {
            $thisWrap.find('#field-name').removeClass('error');
        }
        if ($thisWrap.find('#field-label').val() == '' || $thisWrap.find('#field-name').val() == '') {
            return false;
        }
        var field_options = '',
            multiSelect = '',
            palceholder = '';
        var rangeAtts = '';
        if ($thisWrap.find('#field-required').is(':checked')) {
            field_required = "required='yes' ";
        }
        if (field_typee == 'select' || field_typee == 'radio' || field_typee == 'checkboxes') {
            var fieldOptions = $thisWrap.find('#field-options').val();
            fieldOptions = fieldOptions.replace(/\n/g, " ");
            field_options = "options='" + fieldOptions + ",' ";
        }
        if (field_typee == 'select') {
            $thisWrap.find('.multiselect-field').show();
            if ($thisWrap.find('#field-required').is(':checked')) {
                multiSelect = "multiselect='yes' ";
            }
        } else {
            $thisWrap.find('.multiselect-field').hide();
        }
        if (field_typee == 'text' || field_typee == 'textarea' || field_typee == 'email' || field_typee == 'tel' || field_typee == 'url') {
            var placeholderVal = $thisWrap.find('#field-placeholder').val();
            palceholder = "placeholder='" + placeholderVal + "' ";
        }
        if (field_typee == 'range') {
            rangeAtts += "min='" + $thisWrap.find('#min-val').val() + "' ";
            rangeAtts += "max='" + $thisWrap.find('#max-val').val() + "' ";
            rangeAtts += "step='" + $thisWrap.find('#step-val').val() + "' ";
            rangeAtts += "def='" + $thisWrap.find('#def-val').val() + "' ";
        }

        var showInFilter = 'no';
        if ($thisWrap.find('#field-required').is(':checked')) {
            showInFilter = 'yes';
        }

        $this.append('<i class="fa fa-spinner fa-spin" style="margin-top: 7px; margin-left: 5px;"></i>');

        if (exclusive_cats == null) {
            exclusive_val = 'yes';
        }

        jQuery.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: jQuery('#formajaxurl').val(),
            data: {
                'action': 'lp_save_extra_form_field',
                'field_name': $thisWrap.find('#field-name').val(),
                'field_label': $thisWrap.find('#field-label').val(),
                'placeholderVal': $thisWrap.find('#field-placeholder').val(),
                'field_type': $thisWrap.find('#field-type').val(),
                'field_options': $thisWrap.find('#field-options').val(),
                'exclusive_val': exclusive_val,
                'exclusive_cats': exclusive_cats,
                'showInFilter': showInFilter,
                _ajax_nonce: lp_admin_vars.admin_nonce
            },
            success: function (data) {
                $this.removeClass('ajax-active');
                if (data.status == 'success') {
                    field_name = "name='" + data.field_slug + "' ";

                    var field_label_val = "label='" + field_label + "' ";
                    var fieldShortcode = 'data-shortcode="[lp-submit-form-field ' + exclusive + '' + field_name + ' ' + field_required + '' + field_type + '' + field_options + '' + multiSelect + '' + palceholder + '' + field_label_val + '' + rangeAtts + ']"';

                    var fieldInnerMarkup = '<div class="lp-form-field-options-wrap">' + '<div class="form-group"><label>Placeholder</label><input type="text" class="form-control field-placeholder" value=""></div>' + '<div class="form-group"><label>Quick Tip Title</label><input type="text" class="form-control tip-title" value=""></div>' + '<div class="form-group"><label>Quick Tip Description</label><textarea type="text" class="form-control tip-description"></textarea></div>' + '<div class="form-group"><label>Quick Tip Image Url</label><input type="text" class="form-control tip-image" value=""></div>' + '<div class="form-group"><button class="button button-primary">Save</button></div>' + '</div>';
                    var fieldMarkup = '<li data-placeholder="' + placeholderVal + '" data-' + field_name + ' ' + fieldShortcode + ' class="ui-sortable-handle"><div class="form-field-title">' + field_label + ' <span class="lp-el-edit"><i class="fa fa-pencil"></i></span> <span class="lp-el-remove"><i class="fa fa-trash-o"></i></span></div>' + fieldInnerMarkup + '</li>';
                    $this.closest('.form-section-wrapper').find('ul').append(fieldMarkup);
                    $this.closest('.form-section-wrapper').find('.lp-submit-form-add-field').slideToggle();
                    jQuery('form.lp-submit-form-add-field-wrap').trigger('reset');
                    jQuery('form.lp-submit-form-add-field-wrap .non-exclusive-wrap').show();
                    jQuery('form.lp-submit-form-add-field-wrap .options-field').hide();

                    $this.find('i').remove();

                    jQuery('.save-submit-form').trigger('click');

                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'html',
                        url: jQuery('#formajaxurl').val(),
                        data: {
                            'action': 'lp_get_extra_form_fields_in_tab',
                        },
                        success: function (data) {
                            jQuery('.fes-custom-fields .form-fields-list').html(data);
                            lp_form_builder_inner_sorter();
                        }
                    });
                } else {
                    $this.find('i').remove();
                    alert(data.message);
                }
            }
        });
    }




});
jQuery(document).on('click', '.form-section-wrapper .cancel-new-field', function (e) {
    var $this = jQuery(this),
        targetUI = $this.closest('.form-section-wrapper').find('.lp-submit-form-add-field');
    targetUI.slideToggle();

    jQuery('form.lp-submit-form-add-field-wrap').trigger('reset');
    jQuery('form.lp-submit-form-add-field-wrap .non-exclusive-wrap').show();
    jQuery('form.lp-submit-form-add-field-wrap .options-field').hide();
    jQuery('form.lp-submit-form-add-field-wrap .field-placeholder').show();

});
jQuery(document).on('change', '.submit-field-type', function (e) {
    e.preventDefault();
    var field_type = jQuery(this).val();
    if (field_type == 'select' || field_type == 'radio' || field_type == 'checkboxes') {
        jQuery('.options-field').show();
    } else {
        jQuery('.options-field').hide();
    }
    // if (field_type == 'dropdown') {
    //     jQuery('.multiselect-field').show();
    // } else {
    //     jQuery('.multiselect-field').hide();
    // }
    if (field_type == 'text' || field_type == 'textarea' || field_type == 'email' || field_type == 'tel' || field_type == 'url') {
        jQuery('.field-placeholder').show();
    } else {
        jQuery('.field-placeholder').hide();
    }
    if (field_type == 'range') {
        jQuery('.range-extra').show();
    } else {
        jQuery('.range-extra').hide();
    }
});
jQuery(document).on('click', '.form-section-wrapper li .lp-el-edit, .form-section-wrapper li .form-field-title', function (e) {
    e.preventDefault();
    jQuery('.lp-form-field-options-wrap').hide();

    var targetWrap = jQuery(this).closest('li');

    // var defaultFieldsArr    =   [
    //     "postTitle",
    //     "gAddress",
    //     "inputCity",
    //     "inputPhone",
    //     "inputWebsite",
    //     "inputCategory",
    //     "priceDetails",
    //     "businessHours",
    //     "socialMedia",
    //     "inputDescription",
    //     "inputTags",
    //     "postVideo",
    //     "postGallery",
    //     "featuredimage",
    //     "businessLogo",
    //     "faqs",
    // ];
    // var targetWrapType  =   targetWrap.attr('data-name');

    if (jQuery(this).hasClass('active-options')) {
        jQuery(this).removeClass('active-options');
    } else {
        targetWrap.find('.lp-form-field-options-wrap').show();
        jQuery(this).addClass('active-options');
    }
    var $this = jQuery(this),
        wrapLI = $this.closest('li'),
        titleT = wrapLI.data('tip-title'),
        descT = wrapLI.data('tip-description'),
        imgT = wrapLI.data('tip-image'),
        placeT = wrapLI.data('placeholder'),
        lableT = wrapLI.data('label'),
        requiredT = wrapLI.data('required'),
        nameT = wrapLI.attr('data-name');

    if (nameT == 'inputCity' || nameT == 'inputCategory') {
        var mulitCheckVal = wrapLI.attr('data-multi');
        if (mulitCheckVal == 'yes') {
            wrapLI.find('.switch-checkbox[name="taxonomy-multi"]').prop('checked', true);
        } else {
            wrapLI.find('.switch-checkbox[name="taxonomy-multi"]').prop('checked', false);
        }

    }

    if (nameT == 'priceDetails') {
        var priceFromVal = wrapLI.attr('data-pricefrom'),
            priceToVal = wrapLI.attr('data-priceto');

        if (priceFromVal == 'yes') {
            wrapLI.find('.switch-checkbox[name="price-from"]').prop('checked', true);
        } else {
            wrapLI.find('.switch-checkbox[name="price-from"]').prop('checked', false);
        }
        if (priceToVal == 'yes') {
            wrapLI.find('.switch-checkbox[name="price-to"]').prop('checked', true);
        } else {
            wrapLI.find('.switch-checkbox[name="price-to"]').prop('checked', false);
        }
    }
    if (wrapLI.find('#field-required').length) {
        if (requiredT == 'required') {
            wrapLI.find('#field-required').prop('checked', true);
        } else {
            wrapLI.find('#field-required').prop('checked', false);
        }
    }

    wrapLI.find('.tip-title').val(titleT);
    wrapLI.find('.tip-description').val(descT);
    wrapLI.find('.tip-image').val(imgT);
    wrapLI.find('.field-placeholder').val(placeT);
    wrapLI.find('.field-label').val(lableT);
});
jQuery(document).on('click', '.form-fields-sorter ul li button', function (e) {

    e.preventDefault();

    var $this = jQuery(this);
    if ($this.hasClass('add-submit-form-field')) {

    } else {
        var wrapLI = $this.closest('li'),
            tTitle = wrapLI.find('input.tip-title').val(),
            tImage = wrapLI.find('input.tip-image').val(),
            tDesc = wrapLI.find('textarea.tip-description').val(),
            tPlace = wrapLI.find('input.field-placeholder').val(),
            tlabel = wrapLI.find('input.field-label').val(),
            tRequired = '',
            tName = wrapLI.attr('data-name');

        if (wrapLI.find('#field-required').length) {
            if (wrapLI.find('#field-required').is(':checked')) {
                tRequired = 'required';
            }
        }

        if (tName == 'inputCity' || tName == 'inputCategory') {
            var multiCheckVal = 'no';
            if (wrapLI.find('.switch-checkbox[name="taxonomy-multi"]').is(':checked')) {
                multiCheckVal = 'yes';
            }
            wrapLI.attr('data-multi', multiCheckVal);
        }

        if (tName == 'priceDetails') {
            var priceFromVal = 'no',
                priceToVal = 'no';

            if (wrapLI.find('.switch-checkbox[name="price-from"]').is(':checked')) {
                priceFromVal = 'yes';
            }
            if (wrapLI.find('.switch-checkbox[name="price-to"]').is(':checked')) {
                priceToVal = 'yes';
            }
            wrapLI.attr('data-pricefrom', priceFromVal);
            wrapLI.attr('data-priceto', priceToVal);
        }


        //wrapLI.find('.form-field-title').text(tlabel);

        wrapLI.attr('data-tip-title', tTitle);
        wrapLI.attr('data-tip-image', tImage);
        wrapLI.attr('data-tip-description', tDesc);
        wrapLI.attr('data-placeholder', tPlace);
        wrapLI.attr('data-label', tlabel);
        wrapLI.attr('data-required', tRequired);
    }

});
jQuery(document).on('click', '.add-submit-form-section', function (e) {
    var sectionLabel = jQuery('#section-label').val(),
        sectionMarkup = '';
    if (sectionLabel == '') {
        alert('Please enter label for section');
        return false;
    } else {

        var addFieldMarkup = jQuery('#custom-add-new-field-markup').html();

        addFieldMarkup += '<div class="form-section-actions">\n' +
            '<span class="remove-section">Remove Section</span>\n' +
            '<span class="listing_submit_form_add-new-field">+ add new field</span>\n' +
            '</div>';

        var sectionID = sectionLabel.replace(/[^a-zA-Z]/g, "");
        sectionMarkup = '<li id="' + sectionID + '-' + lp_make_randomStr(9, '123456789') + '" class="form-section-wrapper" data-id="' + sectionID + '" data-label="' + sectionLabel + '"><div class="form-section-title"><span class="title">' + sectionLabel + '</span> <span class="lp-el-edit"><i class="fa fa-chevron-down"></i></span><a href="javascript:void(0);" class="lp-edit-form-section"><i class="fa fa-pencil"></i></a></div><ul class="connected-sortable-inner"></ul>' + addFieldMarkup + '</li>';
        jQuery('.lp-form-builder-inner-wrap .form-fields-sorter > ul').append(sectionMarkup);
        jQuery('#section-label').val('');
        jQuery('.lp-submit-form-add-section').slideToggle();
        var clone, before, parent;
        jQuery('.connected-sortable-inner').sortable({
            connectWith: ".connected-sortable-inner",
            helper: "clone",
            revert: "invalid",
            start: function (event, ui) {
                jQuery(ui.item).show();
                clone = jQuery(ui.item).clone();
                before = jQuery(ui.item).prev();
                parent = jQuery(ui.item).parent();
            },
            receive: function (event, ui) {
                var itemIdentifier = jQuery(ui.item).attr('data-name'),
                    itemLength = jQuery('.form-fields-sorter').find('li[data-name="' + itemIdentifier + '"]').length;
                if (itemLength > 1) {
                    jQuery('#lp-submit-builder-ovelay').find('i').hide();
                    jQuery('#lp-submit-builder-ovelay').find('p').text('Duplicate not allowed').show();
                    jQuery('#lp-submit-builder-ovelay').fadeIn();
                    setTimeout(function () {
                        jQuery('#lp-submit-builder-ovelay').fadeOut();
                        jQuery('#lp-submit-builder-ovelay').find('p').text('Form Saved Successfully');
                    }, 1500);
                    ui.item.remove();
                    if (before.length) before.after(clone);
                    else parent.prepend(clone);
                }
            }
        }).disableSelection();
    }
});
//jQuery(document).on('click', '.form-fields-sorter .form-section-wrapper .form-section-title', function(e) {
//    var $this = jQuery(this),
//        targetUl = $this.closest('.form-section-wrapper').find('ul'),
//        targetActions = $this.closest('.form-section-wrapper').find('.form-section-actions');
//    targetUl.slideToggle();
//    targetActions.slideToggle();
//    $this.find('i').toggleClass('fa-chevron-down');
//});
jQuery(document).on('click', '.form-section-actions .remove-section', function (e) {
    jQuery(this).closest('.form-section-wrapper').remove();
});
jQuery(document).on('click', '.listing_submit_form_add-new-field', function (e) {
    e.preventDefault();
    var $this = jQuery(this),
        targetUI = $this.closest('.form-section-wrapper').find('.lp-submit-form-add-field');
    targetUI.find('input[name="field-name"]').closest('.form-group').remove();
    targetUI.slideToggle();
});
jQuery(document).on('click', '.lp-form-field-options-wrap button.button', function (e) {
    e.preventDefault();

    var $this = jQuery(this).closest('.form-group'),
        $thisBtn = jQuery(this),
        $thisForm = jQuery(this).closest('.lp-form-field-options-wrap');
    if ($this.hasClass('active-ajax')) {

    } else {

        if ($thisBtn.hasClass('update-custom-form-field')) {
            $this.addClass('active-ajax');
            $this.append('<i class="fa fa-spinner fa-spin"></i>');
            $this.find('.settings-saved-btn').remove();

            var field_id = $thisBtn.attr('data-cfid'),
                exclusive = 'no',
                options = $thisForm.find('#field-options').val(),
                selectedCats = $thisForm.find('#field-categories').val(),
                fieldType = $thisForm.find('#field-type').val(),
                fieldPlaceholder = $thisForm.find('#field-placeholder').val(),
                showInFilter = 'no';

            if ($thisForm.find('#field-exclusive').is(':checked')) {
                exclusive = 'yes';
            }
            if ($thisForm.find('#field-required').is(':checked')) {
                showInFilter = 'yes';
            }

            if (selectedCats === null) {
                exclusive = 'yes'
            }

            lielement = $this.closest('li');
            field_name = "name='" + lielement.data('name') + "'";
            field_required = "required='" + lielement.data('required') + "'";
            field_type = "type='" + fieldType + "'";
            palceholder = "placeholder='" + lielement.data('placeholder') + "'";
            field_label = lielement.find('#field-label').val();
            field_label_val = "label='" + field_label + "' ";
            exxclusive = '';
            if (exclusive == 'yes') {
                exxclusive = 'exclusive=' + exclusive;
            }
            var fieldShortcode = '[lp-submit-form-field ' + exxclusive + ' ' + field_name + ' ' + field_required + ' ' + field_type + ' ' + palceholder + ' ' + field_label_val + ' ]';
            lielement.attr('data-shortcode', fieldShortcode);

            jQuery.ajax({
                type: 'POST',
                dataType: 'JSON',
                url: jQuery('#formajaxurl').val(),
                data: {
                    'action': 'lp_update_extra_form_builder_fields',
                    'exclusive': exclusive,
                    'options': options,
                    'field_id': field_id,
                    'selectedCats': selectedCats,
                    'fieldType': fieldType,
                    'fieldPlaceholder': fieldPlaceholder,
                    'showInFilter': showInFilter,
                    _ajax_nonce: lp_admin_vars.admin_nonce
                },
                success: function (data) {

                    $this.removeClass('active-ajax');
                    $this.find('i.c').remove();
                    if (data.status == 'error') {
                        alert(data.message);
                    } else {
                        $this.append('<span class="settings-saved-btn">settings saved</span>');
                        jQuery('.save-submit-form').trigger('click');
                    }
                }
            });


        } else {
            $this.addClass('active-ajax');
            $this.append('<i class="fa fa-spinner fa-spin"></i>');
            setTimeout(function () {
                $this.find('i').remove();
                $this.append('<span class="settings-saved-btn">settings saved</span>');
                jQuery('.save-submit-form').trigger('click');
            }, 1000);
            setTimeout(function () {
                $this.removeClass('active-ajax');
                $this.find('.settings-saved-btn').remove();
            }, 2000);
        }
    }

});
jQuery(document).on('change', '.switch-checkbox[name="field-exclusive"]', function () {
    var $this = jQuery(this);

    if ($this.closest('.lp-submit-form-add-field').length) {
        var formWrap = $this.closest('.lp-submit-form-add-field');
    } else {
        var formWrap = $this.closest('.lp-form-field-options-wrap');
    }

    if ($this.is(':checked')) {
        formWrap.find('.non-exclusive-wrap').fadeOut();
    } else {
        formWrap.find('.non-exclusive-wrap').fadeIn();
    }
});
jQuery(document).on('click', '.cancel-new-section', function () {
    var $this = jQuery(this);

    $this.closest('.lp-submit-form-add-section').find('#section-label').val('');
    $this.closest('.lp-submit-form-add-section').slideUp();
});
jQuery(document).on('click', '.close_form_builder', function () {
    jQuery('.form-builder-active-popup').fadeOut();
});
jQuery(document).on('click', '.close_reset_popu_p', function () {
    jQuery('.form-builder-reset-popup').fadeOut();
});
jQuery(document).on('click', '.reset-form-builder', function (e) {
    e.preventDefault();
    jQuery('.form-builder-reset-popup').fadeIn();
});
jQuery(document).on('click', '.form-builder-confirm-reset', function (x) {
    x.preventDefault();
    $thisreset = jQuery(this);
    $thisreset.append('<i class="fa fa-spinner fa-spin form-builder-confirm-reset-loader"></i>');
    $thisreset.attr('disabled', true);
    $this = jQuery('.reset-form-builder');
    if ($this.hasClass('active-ajax')) { }
    else {
        $this.addClass('active-ajax');
        jQuery.ajax({
            type: 'POST',
            dataType: 'JSON',
            url: jQuery('#formajaxurl').val(),
            data: {
                'action': 'lp_reset_form_builder',
                _ajax_nonce: lp_admin_vars.admin_nonce
            },
            success: function (data) {
                if (data.status == 'success') {
                    location.reload();
                } else {
                    alert(data.message);
                }
                $thisreset.attr('disabled', false);
                $thisreset.find('i').remove();
            }
        });
    }
});

function lp_form_builder_inner_sorter() {
    var clone, before, parent;
    jQuery('.connected-sortable-inner').sortable({
        connectWith: ".connected-sortable-inner",
        helper: "clone",
        revert: "invalid",
        start: function (event, ui) {
            jQuery(ui.item).show();
            clone = jQuery(ui.item).clone();
            before = jQuery(ui.item).prev();
            parent = jQuery(ui.item).parent();
        },
        receive: function (event, ui) {
            var itemIdentifier = jQuery(ui.item).attr('data-name'),
                itemLength = jQuery('.form-fields-sorter').find('li[data-name="' + itemIdentifier + '"]').length;
            if (itemLength > 1) {
                jQuery('#lp-submit-builder-ovelay').find('i').hide();
                jQuery('#lp-submit-builder-ovelay').find('p').text('Duplicate not allowed').show();
                jQuery('#lp-submit-builder-ovelay').fadeIn();
                setTimeout(function () {
                    jQuery('#lp-submit-builder-ovelay').fadeOut();
                }, 1500);
                ui.item.remove();
                if (before.length) before.after(clone);
                else parent.prepend(clone);
            }
        }
    }).disableSelection();
}
function lp_form_builder_check_active_fields() {
    jQuery('.form-builder-left .connected-sortable-inner li').each(function (index) {
        var $this = jQuery(this),
            $thisName = $this.attr('data-name');

        if (jQuery('.form-builder-right .connected-sortable-inner li[data-name="' + $thisName + '"]').length) {
            $this.addClass('disabled-form-builder-sorting');
            $this.append('<i class="already-active-field">(Already Active)</i>')
        }
    });
}

jQuery(document).on('click', '.form-fields-sorter .form-section-wrapper .form-section-title .lp-edit-form-section', function (e) {
    var $this = jQuery(this);
});

jQuery(document).on('click', '.form-fields-sorter .form-section-wrapper .form-section-title .lp-edit-form-section', function (e) {
    var $this = jQuery(this);
    var section_label = $this.closest('li.form-section-wrapper').data('label');

    var section_id = $this.closest('li.form-section-wrapper').attr('id');
    jQuery('#lp-layout-builder-ovelay input[name="section_id"]').val(section_id);
    jQuery('#lp-layout-builder-ovelay #section-label').val(section_label);

    jQuery('#lp-layout-builder-ovelay').show();
});

jQuery(document).on('click', '#lp-layout-builder-ovelay .cancel-new-section', function () {
    var $this = jQuery(this);
    jQuery('#lp-layout-builder-ovelay input[name="section_id"]').val('');
    $this.closest('.lp-submit-form-add-section').find('#section-label').val('');
    jQuery('#lp-layout-builder-ovelay').hide();
});
jQuery(document).on('click', '#lp-layout-builder-ovelay .add-new-section', function () {
    var $this = jQuery(this);
    var $section_label = $this.closest('.lp-submit-form-add-section').find('#section-label').val();
    var $section_id = $this.closest('.lp-submit-form-add-section').find('input[name="section_id"]').val();
    if ($section_label === '') {
        alert('Please enter section title.');
        return false;
    }

    jQuery('li#' + $section_id).data('label', $section_label);
    jQuery('li#' + $section_id).find('span.title').text($section_label);
    jQuery('#lp-layout-builder-ovelay').hide();
});

function lp_make_randomStr(len, arr) {
    var ans = '';
    for (var i = len; i > 0; i--) {
        ans +=
            arr[Math.floor(Math.random() * arr.length)];
    }
    return ans;
} 