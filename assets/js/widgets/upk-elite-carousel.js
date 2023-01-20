(function ($, elementor) {

    'use strict';

    var widgetEliteCarousel = function ($scope, $) {

        var $carousel = $scope.find('.upk-elite-carousel');

        if (!$carousel.length) {
            return;
        }

        //console.log(JSON.parse(JSON.stringify($settings)));

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
        elementorFrontend.hooks.addAction('frontend/element_ready/upk-elite-carousel.default', widgetEliteCarousel);
    });

}(jQuery, window.elementorFrontend));