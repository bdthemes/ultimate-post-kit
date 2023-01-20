(function ($, elementor) {

    'use strict';

    var widgetBuzzListCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-buzz-list-carousel');

        if (!$carousel.length) {
            return;
        }

        var $carouselContainer = $carousel.find('.swiper-container'),
            $settings = $carousel.data('settings');

        const Swiper = elementorFrontend.utils.swiper;
        initSwiper();
        async function initSwiper() {
            var swiper = await new Swiper($carouselContainer, $settings);

            if ($settings.pauseOnHover) {
                $($carouselContainer).hover(function () {
                    (this).swiper.autoplay.stop();
                }, function () {
                    (this).swiper.autoplay.start();
                });
            }
        };

    };


    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-buzz-list-carousel.default', widgetBuzzListCarousel);
    });

}(jQuery, window.elementorFrontend));