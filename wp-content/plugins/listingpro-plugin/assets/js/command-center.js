// command center js
jQuery(document).on('click', '.lp-cc-addons-actions', function (e) {
    e.preventDefault();

    var $this = jQuery(this),
        ccAction = $this.attr('data-action'),
        ccDestin = $this.attr('data-destination'),
        ccFile = $this.attr('data-file'),
        cardCont = $this.closest('.card-container'),
        footerCont = cardCont.find('.footer-status');

    if (jQuery('.lp-cc-addons-actions').hasClass('active-ajax')) {

    } else {
        jQuery('.lp-cc-addons-actions').addClass('active-ajax');
        $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeIn();

        if (ccDestin == 'own') {
            jQuery.ajax({
                type: 'POST',
                dataType: 'json',
                url: ajaxurl,
                data: {
                    'action': 'lp_cc_addons_actions',
                    'ccAction': ccAction,
                    'ccDestin': ccDestin,
                    'ccFile': ccFile,
                    'cc_nonce': cc_vars.cc_nonce,
                },
                success: function (res) {
                    jQuery('.lp-cc-addons-actions').removeClass('active-ajax');
                    $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                    if ((ccAction == 'activate' || ccAction == 'install') && res.status == 'success') {
                        footerCont.removeClass('installed');
                        footerCont.removeClass('inactive');
                        footerCont.addClass('active');

                        $this.text('Deactivate');
                        $this.attr('data-action', 'deactivate');
                        footerCont.text('Active');
                    }
                    if (ccAction == 'deactivate' && res.status == 'success') {
                        footerCont.removeClass('inactive');
                        footerCont.removeClass('active');
                        footerCont.addClass('installed');

                        footerCont.text('Installed');
                        $this.text('Activate');
                        $this.attr('data-action', 'activate');
                    }
                },
            });
        } else {
            if (ccAction == 'update') {

                var purchase_key = jQuery('#active-pruchase-key').val();

                if (purchase_key != '') {
                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: ajaxurl,
                        data: {
                            'action': 'lp_cc_addons_actions',
                            'ccAction': ccAction,
                            'ccDestin': ccDestin,
                            'ccFile': ccFile,
                            'cc_nonce': cc_vars.cc_nonce
                        },
                        success: function (ress) {
                            jQuery('.lp-cc-addons-actions').removeClass('active-ajax');
                            $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                            if (ccAction == 'update' && ress.status == 'success') {
                                $this.remove();
                                footerCont.text('Updated');
                            }
                        }
                    });
                } else {
                    $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                    footerCont.text('License Activation Required');
                }
            } else if (ccAction == 'install') {
                var purchase_key = jQuery('#active-pruchase-key').val(),
                    cridio_api_url = jQuery('#cridio-api-url').val() + 'addon/key/extension/';
                if (purchase_key != '') {
                    jQuery.ajax({
                        type: 'POST',
                        dataType: 'json',
                        url: cridio_api_url,
                        data: {
                            'key': purchase_key,
                            'extension': ccFile,
                        },
                        success: function (res) {
                            jQuery('.lp-cc-addons-actions').removeClass('active-ajax');
                            if (res.permission == 'yes') {
                                jQuery.ajax({
                                    type: 'POST',
                                    dataType: 'json',
                                    url: ajaxurl,
                                    data: {
                                        'action': 'lp_cc_addons_actions',
                                        'ccAction': ccAction,
                                        'ccDestin': ccDestin,
                                        'ccFileUrl': res.file_url,
                                        'ccFile': ccFile,
                                        'cc_nonce': cc_vars.cc_nonce,
                                    },
                                    success: function (ress) {
                                        jQuery('.lp-cc-addons-actions').removeClass('active-ajax');
                                        $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                                        if ((ccAction == 'activate' || ccAction == 'install') && ress.status == 'success') {
                                            footerCont.removeClass('installed');
                                            footerCont.removeClass('inactive');
                                            footerCont.addClass('active');

                                            $this.text('Deactivate');
                                            $this.attr('data-action', 'deactivate');
                                            footerCont.text('Active');
                                        }
                                    }
                                });
                            } else {
                                $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                                footerCont.text('License Activation Required');
                            }
                        }
                    });
                } else {
                    $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                    footerCont.text('License Activation Required');
                }
            } else {
                jQuery.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: ajaxurl,
                    data: {
                        'action': 'lp_cc_addons_actions',
                        'ccAction': ccAction,
                        'ccDestin': ccDestin,
                        'ccFile': ccFile,
                        'cc_nonce': cc_vars.cc_nonce,
                    },
                    success: function (res) {
                        jQuery('.lp-cc-addons-actions').removeClass('active-ajax');
                        $this.closest('.lp-cc-dashboard-content-plugin-card').find('.preloader').fadeOut();
                        if ((ccAction == 'activate' || ccAction == 'install') && res.status == 'success') {
                            footerCont.removeClass('installed');
                            footerCont.removeClass('inactive');
                            footerCont.addClass('active');

                            $this.text('Deactivate');
                            $this.attr('data-action', 'deactivate');
                            footerCont.text('Active');
                        }
                        if (ccAction == 'deactivate' && res.status == 'success') {
                            footerCont.removeClass('inactive');
                            footerCont.removeClass('active');
                            footerCont.addClass('installed');

                            footerCont.text('Installed');
                            $this.text('Activate');
                            $this.attr('data-action', 'activate');
                        }
                    },
                });
            }
        }
    }
});
jQuery(document).ready(function () {


    jQuery('.hide-license-error').click(function (e) {
        e.preventDefault();
        jQuery('.license-activation-failed-box-content').hide();
        jQuery('.license-verification-form-box-content').show();
    });
    var cur_active_Lic = jQuery('#cur_active_lic_g').val();
    var ftrue = jQuery('#txtPhoneNo').hasClass('show-in_che');
    if (ftrue == true) {
        var sllice = cur_active_Lic.substr(cur_active_Lic.length - 4);
        var combine = "********-****-****-****-********" + sllice;
        jQuery("#txtPhoneNo").html("" + combine);
        jQuery(".input-visibility-toggler").click(function () {

            jQuery(this).toggleClass("fa-eye fa-eye-slash");

            if (jQuery(this).hasClass("fa-eye")) {
                jQuery("#txtPhoneNo").html("" + combine);
            }
            else if (jQuery(this).hasClass("fa-eye-slash")) {
                jQuery("#txtPhoneNo").html("" + cur_active_Lic);
            }
        });
    } else { }
});
jQuery(document).ready(function () {
    jQuery('#site-table-switch-1').click(function () {
        if (jQuery(this).prop("checked") == true) {
            jQuery('.license-page-alert-overlay').find('.for-change').html("activate");
            jQuery(document).find('.license-page-alert-overlay').show();
        }
        else if (jQuery(this).prop("checked") == false) {
            jQuery(document).find('.license-page-alert-overlay').show();
            jQuery('.license-page-alert-overlay').find('.for-change').html("deactivate");
            jQuery(document).find('.license-page-alert-overlay input[name="env"]').val('live');
        }
    });
    jQuery('#site-table-switch-2').click(function () {
        if (jQuery(this).prop("checked") == true) {
            jQuery('.license-page-alert-overlay').find('.for-change').html("activate");
            jQuery(document).find('.license-page-alert-overlay').show();
        }
        else if (jQuery(this).prop("checked") == false) {
            jQuery(document).find('.license-page-alert-overlay').show();
            jQuery('.license-page-alert-overlay').find('.for-change').html("deactivate");
            jQuery(document).find('.license-page-alert-overlay input[name="env"]').val('sandbox');
        }
    });
    jQuery('.alert-area-dismiss').click(function () {
        jQuery(document).find('.license-page-alert-overlay').hide();
        if (jQuery('.license-page-alert-overlay').find('.for-change').html() == 'deactivate') {
            jQuery("#site-table-switch-1").prop("checked", true);
            jQuery("#site-table-switch-2").prop("checked", true);
        } else if (jQuery('.license-page-alert-overlay').find('.for-change').html() == 'activate') {
            jQuery("#site-table-switch-1").prop("checked", false);
            jQuery("#site-table-switch-2").prop("checked", false);
        }
    });
    jQuery('.license-page-alert-overlay .btn.btn-no').click(function () {
        jQuery(document).find('.license-page-alert-overlay').hide();
        if (jQuery('.license-page-alert-overlay').find('.for-change').html() == 'deactivate') {
            jQuery("#site-table-switch-1").prop("checked", true);
        } else if (jQuery('.license-page-alert-overlay').find('.for-change').html() == 'activate') {
            jQuery("#site-table-switch-1").prop("checked", false);
        }
        return false;
    });
    jQuery('.license-page-alert-overlay .btn.btn-yes').click(function () {
        jQuery(document).find('.license-page-alert-overlay').hide();
    });
});
jQuery(document).ready(function () {
    jQuery(".key-enter-bar").keyup(function () {
        var clenght = jQuery('.key-enter-bar').val();
        if (clenght.length >= 36 && jQuery('.activation-option').val() != "Select") {
            jQuery(".button-success").removeAttr("disabled");
        } else {
            jQuery(".button-success").attr("disabled", "");
        }
    });
    jQuery(".activation-option").change(function () {
        var clenght = jQuery('.key-enter-bar').val();
        if (clenght.length >= 36 && jQuery('.activation-option').val() != "Select") {
            jQuery(".button-success").removeAttr("disabled");
        } else {
            jQuery(".button-success").attr("disabled", "");
        }
    });
});
jQuery(document).ready(function () {

    var height = jQuery(".lp-cc-dashboard-user-activity-card-body").height();
    var heightoftimeline = jQuery(".lp-cc-dashboard-user-activity-card-timeline").height();

    if (height > heightoftimeline) {
        jQuery(".lp-cc-dashboard-user-activity-card-timeline").css('min-height', 'calc(' + height + 'px - 50px');
    } else if (height < heightoftimeline) {
        jQuery(".lp-cc-dashboard-user-activity-card-body").css('min-height', 'calc(' + heightoftimeline + 'px - 50px');

    }

    jQuery(".lp-cc-dashboard-time-range-bar-time-dropdown").change(function () {
        if (jQuery(".lp-cc-dashboard-time-range-bar-time-dropdown").val() == 'Last 30 Days') {
            jQuery('.days-count-7').hide();
            jQuery('.days-count-15').hide();
            jQuery('.days-count-3').hide();
            jQuery('.days-count-30').show();
        } else if (jQuery(".lp-cc-dashboard-time-range-bar-time-dropdown").val() == 'Last 15 Days') {
            jQuery('.days-count-7').hide();
            jQuery('.days-count-3').hide();
            jQuery('.days-count-30').hide();
            jQuery('.days-count-15').show();
        } else if (jQuery(".lp-cc-dashboard-time-range-bar-time-dropdown").val() == 'Last 7 Days') {
            jQuery('.days-count-3').hide();
            jQuery('.days-count-15').hide();
            jQuery('.days-count-30').hide();
            jQuery('.days-count-7').show();
        } else if (jQuery(".lp-cc-dashboard-time-range-bar-time-dropdown").val() == 'Last 3 Days') {
            jQuery('.days-count-7').hide();
            jQuery('.days-count-15').hide();
            jQuery('.days-count-30').hide();
            jQuery('.days-count-3').show();
        }
    });

});

jQuery(document).ready(function () {
    jQuery('.lp-dashboard-custom-select-dropdown-pending').on('click', '.lp-dashboard-custom-select-dropdown-placeholder', function () {
        var parent = jQuery(this).closest('.lp-dashboard-custom-select-dropdown-pending');
        if (!parent.hasClass('is-open')) {
            parent.addClass('is-open');
            jQuery('.lp-dashboard-custom-select-dropdown.is-open').not(parent).removeClass('is-open');
        } else {
            parent.removeClass('is-open');
        }
    }).on('click', 'ul>li', function () {
        var parent = jQuery(this).closest('.lp-dashboard-custom-select-dropdown-pending');
        parent.removeClass('is-open').find('.lp-dashboard-custom-select-dropdown-placeholder').text(jQuery(this).text());


        jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-custom-preloader').fadeIn();
        jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-custom-preloader-white-space').fadeIn();
        var selectedoptionjq = jQuery(this).attr('data-value');

        jQuery.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                'action': 'lp_cc_pending_ajax_request',
                'selectedoptionr': selectedoptionjq,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-dashboard-user-activity-card-content').html(data);
                jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-dashboard-user-activity-card-content').append('<div class="lp-cc-custom-preloader-white-space"></div>');
                jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-custom-preloader-white-space').fadeOut();
                jQuery('.lp-cc-dashboard-user-activity-task .lp-cc-custom-preloader').fadeOut();
            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });


    });
});

jQuery(document).ready(function () {
    jQuery('.lp-dashboard-custom-select-dropdown-pay').on('click', '.lp-dashboard-custom-select-dropdown-placeholder', function () {
        var parent = jQuery(this).closest('.lp-dashboard-custom-select-dropdown-pay');
        if (!parent.hasClass('is-open')) {
            parent.addClass('is-open');
            jQuery('.lp-dashboard-custom-select-dropdown.is-open').not(parent).removeClass('is-open');
        } else {
            parent.removeClass('is-open');
        }
    }).on('click', 'ul>li', function () {
        var parent = jQuery(this).closest('.lp-dashboard-custom-select-dropdown-pay');
        parent.removeClass('is-open').find('.lp-dashboard-custom-select-dropdown-placeholder').text(jQuery(this).text());

        jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-custom-preloader-white-space').fadeIn();
        jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-custom-preloader').fadeIn();



        var selectedoptionjqpay = jQuery(this).attr('data-value');
        jQuery.ajax({
            url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
            data: {
                'action': 'lp_cc_pay_ajax_request',
                'selectedoptionrpay': selectedoptionjqpay,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-dashboard-user-activity-card-content').html(data);
                jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-dashboard-user-activity-card-content').append('<div class="lp-cc-custom-preloader-white-space"></div>');
                jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-custom-preloader-white-space').fadeOut();
                jQuery('.lp-cc-dashboard-user-activity-payment .lp-cc-custom-preloader').fadeOut();

            },
            error: function (errorThrown) {
                console.log(errorThrown);
            }
        });


    });
});


jQuery(document).ready(function () {
    jQuery(document).on('change', '.lp-cc-dashboard-user-activity-card-content-action', function () {
        $this = jQuery(this);
        if (jQuery($this).val() == 'Delete') {
            jQuery('.lp-cc-post-del-alert').show();
            jQuery('.model-action .btn.no').click(function () {
                jQuery('.lp-cc-post-del-alert').hide();
            });
            jQuery(document).on('click', '.model-action .btn.yes', function () {
                jQuery('.model-action .btn.yes').hide();
                jQuery('.model-action .btn.no').hide();
                jQuery('.model-action img').show();
                var post_id = jQuery($this).attr('data-id');
                jQuery.ajax({
                    url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                    data: {
                        'action': 'lp_cc_post_delete',
                        'post_id': post_id
                    },
                    success: function (data) {
                        // This outputs the result of the ajax request
                        jQuery($this).closest('.lp-cc-dashboard-user-activity-card-content-body').remove();
                        // console.log(data);
                        jQuery('.model-action .btn.yes').show();
                        jQuery('.model-action .btn.no').show();
                        jQuery('.model-action img').hide();
                        jQuery('.lp-cc-post-del-alert').hide();
                    },
                    error: function (errorThrown) {
                        console.log(errorThrown);
                    }
                });
            });
        }
        if (jQuery($this).val() == 'Edit') {
            var post_id = jQuery($this).attr('data-id');
            jQuery.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'lp_cc_post_edit',
                    'post_id': post_id
                },
                success: function (data) {
                    // This outputs the result of the ajax request
                    // console.log(data);
                    window.open(data, '_blank');
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        }
        if (jQuery($this).val() == 'Pending' || jQuery($this).val() == 'Approved') {
            var post_id = jQuery($this).attr('data-id');
            jQuery.ajax({
                url: ajaxurl, // or example_ajax_obj.ajaxurl if using on frontend
                data: {
                    'action': 'lp_cc_post_penandappr',
                    'post_id': post_id
                },
                success: function (data) {
                    // This outputs the result of the ajax request
                    // console.log(data);
                    window.open(data, '_blank');
                },
                error: function (errorThrown) {
                    console.log(errorThrown);
                }
            });
        }




    });


    jQuery('.switch-cc-form-builder').click(function (e) {
        e.preventDefault();
        var $this = jQuery(this),
            enable_data = null;

        $this.closest('.lp-cc-visualizer-content-card').append('<div class="lp-cc-visualizer-content-card-loading"><i class="fa fa-spinner fa-spin"></i></div>');

        if ($this.hasClass('active')) {
            enable_data = 0;
            $this.removeClass('active');
            jQuery('.form-builder-switch').prop('checked', false);
            jQuery('.visualizer-form-builder-button').text('Disabled');
            jQuery('.visualizer-form-builder-button').removeClass('active');
        } else {
            enable_data = 1;
            $this.addClass('active');
            jQuery('.form-builder-switch').prop('checked', true);
            jQuery('.visualizer-form-builder-button').text('Edit');
            jQuery('.lp-cc-visualizer-form-builder-active-popup').fadeIn();
            jQuery('.visualizer-form-builder-button').addClass('active');
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'enable_form_builder',
                'enable_data': enable_data,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                $this.closest('.lp-cc-visualizer-content-card').find('.lp-cc-visualizer-content-card-loading').remove();
            }
        });
    });


    jQuery('.switch-cc-multi-rating').click(function (e) {
        e.preventDefault();
        var $this = jQuery(this),
            enable_data = null;

        $this.closest('.lp-cc-visualizer-content-card').append('<div class="lp-cc-visualizer-content-card-loading"><i class="fa fa-spinner fa-spin"></i></div>');

        if ($this.hasClass('active')) {
            enable_data = 0;
            $this.removeClass('active');
            jQuery('.cc-multi-rating-switch').prop('checked', false);
            jQuery('.visualizer-reviews-button').text('Disabled');
            jQuery('.visualizer-reviews-button').removeClass('active');
        } else {
            enable_data = 1;
            $this.addClass('active');
            jQuery('.cc-multi-rating-switch').prop('checked', true);
            jQuery('.visualizer-reviews-button').text('Edit');
            jQuery('.visualizer-reviews-button').addClass('active');
        }
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'enable_multi_rating',
                'enable_data': enable_data,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                $this.closest('.lp-cc-visualizer-content-card').find('.lp-cc-visualizer-content-card-loading').remove();
            }
        });
    });


});
jQuery(document).ready(function () {
    jQuery('.lp-cc-visualizer-close_form_builder').click(function (e) {
        e.preventDefault();
        jQuery('.lp-cc-visualizer-form-builder-active-popup').fadeOut();
    });

    jQuery('.switch-cc-lead-form').click(function (e) {
        e.preventDefault();
        e.stopPropagation();
        var $this = jQuery(this),
            enable_data = null;

        $this.closest('.lp-cc-visualizer-content-card').append('<div class="lp-cc-visualizer-content-card-loading"><i class="fa fa-spinner fa-spin"></i></div>');

        if ($this.hasClass('active')) {
            enable_data = 0;

            jQuery('.visualizer-lead-form-button').removeClass('active');
            jQuery('.cc-lead-form-switch').prop('checked', false);
            jQuery('.visualizer-lead-form-button').text('Disabled');

            $this.removeClass('active');
        } else {
            enable_data = 1;

            jQuery('.visualizer-lead-form-button').addClass('active');
            jQuery('.visualizer-lead-form-button').text('Edit');
            jQuery('.cc-lead-form-switch').prop('checked', true);

            $this.addClass('active');
        }
        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'enable_lead_form',
                'enable_data': enable_data,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                $this.closest('.lp-cc-visualizer-content-card').find('.lp-cc-visualizer-content-card-loading').remove();
            }
        });

    });
    jQuery('.switch-submit-form-builder').click(function (e) {
        e.stopPropagation();
        var $this = jQuery(this),
            enable_data = null;
        if (jQuery(this).hasClass('active')) {
            enable_data = 0;

            jQuery('.visualizer-form-builder-button').removeClass('active');
            jQuery('.form-builder-switch').prop('checked', false);
            jQuery('.visualizer-form-builder-button').text('Disabled');

            jQuery(this).removeClass('active');
        } else {
            enable_data = 1;

            jQuery('.visualizer-form-builder-button').addClass('active');
            jQuery('.visualizer-form-builder-button').text('Edit');
            jQuery('.lp-cc-visualizer-form-builder-active-popup').fadeIn();
            jQuery('.form-builder-switch').prop('checked', true);

            jQuery(this).addClass('active');
        }

        jQuery.ajax({
            type: 'POST',
            url: ajaxurl,
            data: {
                'action': 'enable_form_builder',
                'enable_data': enable_data,
                'cc_nonce': cc_vars.cc_nonce
            },
            success: function (data) {
                //console.log(data);
            }
        });
    });

    jQuery(document).mouseup(function (e) {
        var container = jQuery(".lp-dashboard-custom-select-dropdown.is-open ul ,.lp-dashboard-custom-select-dropdown-placeholder");
        // if the target of the click isn't the container nor a descendant of the container
        if (!container.is(e.target) && container.has(e.target).length === 0) {
            // container.hide();
            var parent = jQuery(this).closest('.lp-dashboard-custom-select-dropdown-pending');
            if (!parent.hasClass('is-open')) {
                parent.addClass('is-open');
                jQuery('.lp-dashboard-custom-select-dropdown.is-open').not(parent).removeClass('is-open');
            } else {
                parent.removeClass('is-open');
            }
        }
    });
});
jQuery(document).on('click', '.visualizer-form-builder-button.active', function () {
    window.location.href = jQuery(this).attr('data-target');
});
jQuery(document).on('click', '.visualizer-reviews-button.active', function () {
    window.location.href = jQuery(this).attr('data-target');
});
jQuery(document).on('click', '.visualizer-lead-form-button.active', function () {
    window.location.href = jQuery(this).attr('data-target');
});