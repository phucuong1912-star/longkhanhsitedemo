jQuery(document).ready(function ($) {

    jQuery('#select-group-cat').select2({ placeholder: "choose categories" });

});

jQuery(document).on('click', '.edit-rating-group', function (e) {
    e.preventDefault();
    var $this = jQuery(this),
        $thisWrap = $this.closest('.ratings-fields-group');

    if ($thisWrap.hasClass('active-editing')) {
        jQuery('.rating-fields-group-fields').slideUp(500);
        jQuery('.active-editing').removeClass('active-editing');
    } else {
        $thisWrap.find('.rating-fields-group-fields').slideDown(500, function () {
            $thisWrap.addClass('active-editing');
        });
    }
});
jQuery(document).on('click', '.remove-rating-field, .remove-rating-group', function (e) {
    e.preventDefault();
    var $this = jQuery(this),
        target = $this.data('target'),
        removeType = 'group',
        removeDefault = '';

    if ($this.hasClass('processing')) {
        return false;
    }
    else {

        if ($this.hasClass('remove-rating-field')) {
            removeType = 'field';
        }
        if ($this.hasClass('remove-default-field')) {
            removeDefault = 'yes';
        }
        $this.html('<i class="fa fa-spinner fa-spin"></i>');
        if (target || target == 0) {
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    'action': 'listingpro_reviews_settings_remove',
                    'target': target,
                    'removeType': removeType,
                    'removeDefault': removeDefault,
                    _ajax_nonce: lp_admin_vars.admin_nonce
                },
                success: function (res) {
                    if (res.status == 'success') {
                        $this.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                        location.reload();
                    } else {
                        $this.find('i').remove();
                        $this.text('remove');
                        alert(res.message);
                    }
                }
            });
        }
        else {
            $this.closest('.rating-group-field').remove();
        }
    }
});
jQuery(document).on('click', '.cancel-field', function (e) {
    e.preventDefault();
    jQuery('.active-editing').removeClass('active-editing');
    jQuery('.rating-fields-group-fields').slideUp(500);
});
jQuery(document).on('click', '.ad-new-group', function (e) {
    e.preventDefault();
    jQuery('.add-new-group-form').slideToggle(500);
});
jQuery(document).on('click', '.cancel-new-group', function (e) {
    e.preventDefault();
    jQuery('.add-new-group-form').slideUp(500);
});
jQuery(document).on('click', '.save-new-group', function (e) {
    e.preventDefault();
    var groupCats = jQuery('#select-group-cat').val();



    if (groupCats == null) {
        alert('Please Select Categories');
    }
    else {
        var groupMarkup = '<div class="categories-for-inner-wrap"><div class="ratings-fields-group" data-groupid="' + groupCats + '">' +
            '   <div class="rating-fields-group-header">' +
            '       <strong>Group for ' + groupCats + '</strong>' +
            '       <span class="edit-rating-group">edit</span>' +
            '       <span class="remove-rating-group">remove</span>' +
            '       <div class="clearfix"></div>' +
            '   </div>' +
            '   <div class="rating-fields-group-fields">' +
            '       <div class="rating-fields-group-inner"></div>' +
            '       <div class="add-field-wrap">' +
            '           <input type="text" class="field-label" value=""  placeholder="eg.cleanliness">' +
            '           <button class="button save-field">Add</button>' +
            '       </div>' +
            '   </div>' +
            '</div></div>';

        jQuery('.settings-for-categories-wrap').append(groupMarkup);
        jQuery('#select-group-cat').val('');
    }

    jQuery('#select-group-cat').trigger("change");

});

jQuery(document).on('click', '.add-new-field', function (e) {
    e.preventDefault();
    jQuery(this).prev('.add-field-wrap').slideToggle(500);
});
jQuery(document).on('click', '.cancel-new-field', function (e) {
    e.preventDefault();
    jQuery('.add-field-wrap').slideUp(500);
});
jQuery(document).on('click', '.save-field', function (e) {
    e.preventDefault();
    var field_label = jQuery(this).prev('.field-label').val();
    if (field_label == '') {
        jQuery(this).prev('.field-label').css('border', 'solid 1px #990000');
        return false;
    }
    if (jQuery(this).hasClass('save-field-def')) {
        jQuery('.unsaved-message-def').css('display', 'inline-block');
    } else {
        jQuery('.unsaved-message-cats').css('display', 'inline-block');
    }

    var field_markup = '<div class="rating-group-field" data-label="' + field_label + '">' +
        '                       <span class="rating-field-text">' + field_label + '</span>' +
        '                       <span class="remove-rating-field">remove</span>' +
        '                       <div class="clearfix"></div>' +
        '                     </div>';

    jQuery(this).closest('.rating-fields-group-fields').find('.rating-fields-group-inner').append(field_markup);
    jQuery(this).prev('.field-label').val('');
    jQuery(this).prev('.field-label').css('border-color', '#7e8993');
});

jQuery(document).on('click', '.save-ratings-settings', function (e) {
    e.preventDefault();
    var $this = jQuery(this);

    if ($this.hasClass('processing')) {
        alert('do nothing');
    }
    else {
        $this.addClass('processing');
        $this.append('<i class="fa fa-spinner fa-spin"></i>');
        var ratings_settings_data = get_ratings_settings_data();

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action': 'listingpro_reviews_settings',
                'ratings_settings_data': ratings_settings_data,
                _ajax_nonce: lp_admin_vars.admin_nonce
            },
            success: function (res) {
                if (res.status == 'success') {
                    $this.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                    location.reload();
                }else if(res.status == 'error'){
                    alert(res.message);
                    $this.find('i').removeClass('fa-spinner fa-spin');
                }
            }
        });

    }
});
jQuery(document).on('click', '.save-default-rating-settings', function (e) {
    e.preventDefault();
    var $this = jQuery(this);

    if ($this.hasClass('processing')) {
        alert('do nothing');
    }
    else {
        $this.addClass('processing');
        $this.append('<i class="fa fa-spinner fa-spin"></i>');
        var ratings_default_settings_data = get_ratings_default_settings_data();

        jQuery.ajax({
            type: 'POST',
            dataType: 'json',
            url: ajaxurl,
            data: {
                'action': 'listingpro_reviews_default_settings',
                'ratings_default_settings_data': ratings_default_settings_data,
                _ajax_nonce: lp_admin_vars.admin_nonce
            },
            success: function (res) {
                if (res.status == 'success') {
                    $this.find('i').removeClass('fa-spinner fa-spin').addClass('fa-check');
                    location.reload();
                }else if(res.status == 'error'){
                    alert(res.message);
                    $this.find('i').removeClass('fa-spinner fa-spin');
                }
            }
        });
    }
});

function get_ratings_default_settings_data() {

    var return_dta = {};
    jQuery('.default-fields-group').each(function () {

        var ratingsGroup = jQuery(this),
            groupCatName = ratingsGroup.data('groupid');

        return_dta[groupCatName] = [];
        var groupFields = [];
        ratingsGroup.find('.rating-group-field').each(function () {
            var ratingGroupFields = jQuery(this),
                groupFieldsText = ratingGroupFields.data('label');

            groupFields.push(groupFieldsText);
            return_dta[groupCatName] = groupFields;

        });
    });

    return return_dta;
}
function get_ratings_settings_data() {

    var return_dta = {};
    jQuery('.ratings-fields-group').each(function () {

        var ratingsGroup = jQuery(this);
        if (ratingsGroup.hasClass('default-fields-group')) {

        }
        else {
            var groupCatName = ratingsGroup.data('groupid');

            return_dta[groupCatName] = [];
            var groupFields = [];
            ratingsGroup.find('.rating-group-field').each(function () {
                var ratingGroupFields = jQuery(this),
                    groupFieldsText = ratingGroupFields.data('label');

                groupFields.push(groupFieldsText);
                return_dta[groupCatName] = groupFields;

            });

        }

    });

    return return_dta;

}