(function ($, elementor) {
  "use strict";

  var widgetCarbonSlider = function ($scope, $) {
    var $carousel = $scope.find(".upk-carbon-main");
    if (!$carousel.length) {
      return;
    }

    var $carouselContainer = $carousel.find(".swiper-carousel"),
      $settings = $carousel.data("settings");

    const Swiper = elementorFrontend.utils.swiper;
    initSwiper();
    async function initSwiper() {
      var mainSwiper = await new Swiper($carouselContainer, $settings);
      if ($settings.pauseOnHover) {
        $($carouselContainer).hover(
          function () {
            this.swiper.autoplay.stop();
          },
          function () {
            this.swiper.autoplay.start();
          },
        );
      }

      var $mainWrapper = $scope.find(".upk-carbon-slider-wrap"),
        $thumbs = $mainWrapper.find(".upk-carbon-thumbs");
      var slideCount = $thumbs.find(".swiper-wrapper > .swiper-slide").length;
      var loopedSlides = Math.max(1, Math.min(slideCount || 1, 4));
      var useLoop = !!$settings.loop;

      var thumbSwiper = await new Swiper($thumbs, {
        spaceBetween: 0,
        slidesPerView: 1,
        touchRatio: 0.2,
        slideToClickedSlide: true,
        centeredSlides: true,
        loop: useLoop,
        speed: $settings.speed ? $settings.speed : 500,
        loopedSlides: loopedSlides,
        breakpoints: {
          768: {
            slidesPerView: 3,
            spaceBetween: 20,
          },
        },
      });

      // Mutual controller caused drift with fade + loop; keep main → thumbs only.
      mainSwiper.controller.control = thumbSwiper;

      function alignThumbsToMain() {
        if (mainSwiper.destroyed || thumbSwiper.destroyed) return;
        if (thumbSwiper.realIndex === mainSwiper.realIndex) return;
        if (useLoop) {
          thumbSwiper.slideToLoop(mainSwiper.realIndex);
        } else {
          thumbSwiper.slideTo(mainSwiper.realIndex);
        }
      }

      function alignMainToThumbs() {
        if (mainSwiper.destroyed || thumbSwiper.destroyed) return;
        if (thumbSwiper.realIndex === mainSwiper.realIndex) return;
        if (useLoop) {
          mainSwiper.slideToLoop(thumbSwiper.realIndex);
        } else {
          mainSwiper.slideTo(thumbSwiper.realIndex);
        }
      }

      mainSwiper.on("slideChangeTransitionEnd", alignThumbsToMain);
      thumbSwiper.on("slideChangeTransitionEnd", alignMainToThumbs);

      alignThumbsToMain();
    }
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/upk-carbon-slider.default",
      widgetCarbonSlider,
    );
  });
})(jQuery, window.elementorFrontend);
