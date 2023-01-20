(function ($, elementor) {

    'use strict';

    var widgetPholoxSlider = function ($scope, $) {

        var $slider = $scope.find('.upk-pholox-slider'),
            $settings = $slider.data('settings'),
            $widgetSettings = $slider.data('widget');

        if (!$slider.length) {
            return;
        }

 
        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {

            var $thumbs = $slider.find('.upk-thumbs-slider .swiper-container');
            var $preview = $slider.find('.upk-main-slider .swiper-container');

            var mainSlider = await new Swiper($preview, $settings);

            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 15,
                slidesPerView: 2,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    768: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    },
                    1440: {
                        slidesPerView: 4,
                        spaceBetween: 30,
                    }
                }
            });

            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;

            mainSlider.on('slideChange', function () {
                stopVideos();

            });

            var stopVideos = function () {
                $slider.find('.upk-video-wrap').css('z-index', -1);
                var videos = $slider.find('.upk-video-iframe');
                Array.prototype.forEach.call(videos, function (video) {
                    var src = video.src;
                    video.src = src.replace("autoplay=1", "");
                    $slider.find('.upk-video-iframe').prop("src", "");
                });
            };

            $slider.find('.upk-pholox-video-trigger').on('click', function () {
                console.log('s');
                var videoURL = $(this).data('src').split('?')[0],
                    sliderWrapper = $slider.find('.upk-main-slider .swiper-slide-active .upk-img-wrap');

                    $slider.find('.upk-img-wrap').removeClass('upk-width-100');
                    $(sliderWrapper).addClass('upk-width-100');

                sliderWrapper.find('.upk-video-iframe').attr("src", videoURL + "?autoplay=1&modestbranding=1&showinfo=0&rel=0&controls=0&loop=1");
                sliderWrapper.find('.upk-video-wrap').css('z-index', 10);

            });

        }


    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-pholox-slider.default', widgetPholoxSlider);
    });

}(jQuery, window.elementorFrontend));