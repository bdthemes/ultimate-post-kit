(function ($, elementor) {
  "use strict";

  var widgetSnogSlider = function ($scope, $) {
    var $carousel = $scope.find(".upk-snog-slider");
    if (!$carousel.length) {
      return;
    }

    var $carouselContainer = $carousel.find(".swiper-container"),
      $settings = $carousel.data("settings"),
      $widgetSettings = $carousel.data("widget-settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var mainSlider = await new Swiper($carouselContainer, $settings);

      if ($settings.pauseOnHover) {
        $($carouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          }
        );
      }

      var $mainWrapper = $scope.find(".upk-snog-slider-wrap"),
      $thumbs = $mainWrapper.find(".upk-snog-thumbs");

      var sliderThumbs = await new Swiper($thumbs, {
        spaceBetween: 0,
        effect: "slide",

        lazy: true,
        slidesPerView: 2,
        touchRatio: 0.2,
        loop: $settings.loop ? $settings.loop : false,
        speed: $settings.speed ? $settings.speed : 800,
        loopedSlides: 4,
        // allowTouchMove: false,
      });

      var contentslide = await new Swiper( $($widgetSettings.id).find(".upk-content-slider"), {
        parallax: true,
        effect: "fade",
        slidesPerView: 1,
        loop: $settings.loop ? $settings.loop : false,
        speed: $settings.speed ? $settings.speed : 800,
        loopedSlides: 4,
        allowTouchMove: false,
        pagination: {
          el: $widgetSettings.id + " .upk-pagination",
          clickable: true,
        },

        // navigation: {
        //   nextEl: ".upk-navigation-next",
        //   prevEl: ".upk-navigation-prev",
        // },
      });

      // mainSlider.controller.control = sliderThumbs;
      // sliderThumbs.controller.control = mainSlider;
      // contentslide.controller.control = sliderThumbs;
      // contentslide.controller.control = mainSlider;

      mainSlider.controller.control = sliderThumbs;
      sliderThumbs.controller.control = mainSlider;

      $(document).ready(function () {
        setTimeout(() => {
            var mainSwiperInstance = $($widgetSettings.id).find('.upk-main-slider')[0].swiper;

            var $slideActive = $($widgetSettings.id).find('.upk-main-slider .swiper-slide-active');
            var realIndex = $slideActive.data('swiper-slide-index');

            if (typeof realIndex === 'undefined') {
                realIndex = $slideActive.index();
            }

            $($widgetSettings.id).find('.upk-navigation-prev').on("click", function () {
                mainSwiperInstance.slidePrev();
            });

            $($widgetSettings.id).find('.upk-navigation-next').on("click", function () {
                mainSwiperInstance.slideNext();
            });

            $($widgetSettings.id).find('.swiper-pagination-bullet').on("click", function () {
              var index = $(this).index();

              if ($settings.loop) {
                  mainSwiperInstance.slideToLoop(index);
              } else {
                  mainSwiperInstance.slideTo(index);
              }

              $($widgetSettings.id).addClass('wait--'); //todo future

          });

          mainSwiperInstance.on('slideChange', function (e) {
            contentslide.slideToLoop(mainSwiperInstance.realIndex);
          });

        }, 3000);

      });
      
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/upk-snog-slider.default",
      widgetSnogSlider
    );
  });
})(jQuery, window.elementorFrontend);
