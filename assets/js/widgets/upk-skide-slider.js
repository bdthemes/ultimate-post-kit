(function ($, elementor) {

    'use strict';

    var widgetSkideSlider = function ($scope, $) {

        var $carousel = $scope.find('.upk-skide-slider');
        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {

            var mainSlider = await new Swiper($carouselContainer, $settings);
            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }

            var $mainWrapper = $scope.find('.upk-skide-slider-wrap'),
                $thumbs = $mainWrapper.find('.upk-skide-thumbs');
            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 10,
                slidesPerView: 2,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 20,
                    }
                }
            });

            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
        }

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-skide-slider.default', widgetSkideSlider);
    });

}(jQuery, window.elementorFrontend));