(function ($, elementor) {

	'use strict';

	var widgetCamuxSlider = function ($scope, $) {

		var $carousel = $scope.find('.upk-camux-slider');
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

            var $mainWrapper = $scope.find('.upk-camux-slider-wrap'),
            $thumbs          = $mainWrapper.find('.upk-camux-thumbs');

            var sliderThumbs = await new Swiper($thumbs, {
                spaceBetween: 10,
                slidesPerView: 4,
                touchRatio: 0.2,
                slideToClickedSlide: true,
                loop: ($settings.loop) ? $settings.loop : false,
                speed: ($settings.speed) ? $settings.speed : 500,
                loopedSlides: 4,
                breakpoints: {
                    0: {
                        slidesPerView: 2,
                    },
                    768: {
                        slidesPerView: 3,
                    },
                    1024: {
                        slidesPerView: 4,
                    },
                },
            });
    
            mainSlider.controller.control = sliderThumbs;
            sliderThumbs.controller.control = mainSlider;
        };
	};


	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/upk-camux-slider.default', widgetCamuxSlider);
	});

}(jQuery, window.elementorFrontend));