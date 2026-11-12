jQuery(document).ready(function (event) {
    var detailEventSlider = jQuery('.events-content-area-wrap'),
        detailGallerySlider = jQuery('.listing-slide'),
        detailMenuSlider = jQuery('.lp-listing-menuu-slider'),
        detailCouponCountdown = jQuery('.lp-countdown'),
        detailGallerySliderCon = jQuery('.single-page-slider-container'),
        detailGallerySliderImg = detailGallerySlider.data('images-num'),
        detailGallerySliderV3 = jQuery('.lp-listing-slider'),
        detailGallerySliderV3Slides = detailGallerySliderV3.attr('data-totalSlides'),
        center_mode = true,
        variable = true,
        slidesToShow = 3;

    if (detailGallerySliderV3.length > 0) {
        if (detailGallerySliderV3.hasClass('slick-initialized')) detailGallerySliderV3.slick('destroy');
        if (detailGallerySliderV3Slides === 1) {
            slidesToShow = 1;
        }
        if (detailGallerySliderV3Slides === 2) {
            slidesToShow = 2;
        }
        detailGallerySliderV3.slick({
            rtl: true,
            infinite: true,
            slidesToShow: slidesToShow,
            slidesToScroll: 1,
            prevArrow: "<i class=\"fa fa-angle-right arrow-left\" aria-hidden=\"true\"></i>",
            nextArrow: "<i class=\"fa fa-angle-left arrow-right\" aria-hidden=\"true\"></i>",
            responsive: [{
                breakpoint: 480,
                settings: {
                    arrows: true,
                    centerMode: false,
                    centerPadding: '0px',
                    slidesToShow: 2
                }
            }]
        });
    }

    if( detailCouponCountdown.length > 0 ){
        detailCouponCountdown.each(function (i, obj) {
            var selector    =   '#'+jQuery(this).attr('id');
            init_countdown(selector);
        });
    }

    if (detailEventSlider.length > 0) {
        if (detailEventSlider.hasClass('slick-initialized')) detailEventSlider.slick('destroy');
        detailEventSlider.slick({
            rtl: true,
            infinite: true,
            slidesToShow: 2,
            slidesToScroll: 1,
            prevArrow: "<i class=\"fa fa-angle-right arrow-left\" aria-hidden=\"true\"></i>",
            nextArrow: "<i class=\"fa fa-angle-left arrow-right\" aria-hidden=\"true\"></i>"
        });
    }
    if (detailMenuSlider.length > 0) {
        if (detailMenuSlider.hasClass('slick-initialized')) detailMenuSlider.slick('destroy');
        detailMenuSlider.slick({
            rtl: true,
            infinite: true,
            slidesToShow: 1,
            slidesToScroll: 1,
            adaptiveHeight: true,
            nextArrow: "<i class=\"fa fa-angle-right arrow-left\" aria-hidden=\"true\"></i>",
            prevArrow: "<i class=\"fa fa-angle-left arrow-right\" aria-hidden=\"true\"></i>"
        });
    }
    if (detailGallerySlider.length > 0) {

        if (detailGallerySliderImg > 3) {
            center_mode = false;
        } else if (detailGallerySliderImg === 3) {
            detailGallerySliderCon.addClass('three-imgs');
            variable = false;
        } else if (detailGallerySliderImg === 2) {
            detailGallerySliderCon.addClass('new-cls');
        } else if (detailGallerySliderImg === 1) {
            detailGallerySliderCon.addClass('one-img');
        } else {
            center_mode = false;
            variable = false;
        }

        if (detailGallerySlider.hasClass('slick-initialized')) detailGallerySlider.slick('destroy');
        detailGallerySlider.slick({
            rtl: true,
            slidesToShow: 2,
            autoplay: true,
            draggable: false,
            autoplaySpeed: 5000,
            centerMode: center_mode,
            variableWidth: variable,
            focusOnSelect: true,
            adaptiveHeight: true,
            responsive: [
                {
                    breakpoint: 768,
                    settings: {
                        arrows: true,
                        centerMode: false,
                        centerPadding: '0px'
                    }
                },
                {
                    breakpoint: 480,
                    settings: {
                        arrows: true,
                        centerMode: false,
                        centerPadding: '0px',
                        slidesToShow: 1
                    }
                }
            ]
        });
    }
    slickINIT();
});