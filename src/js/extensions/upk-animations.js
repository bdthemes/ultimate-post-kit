(function ($, elementor) {
  "use strict";

  function postKitObserveTarget(target, callback) {
    var options = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
    // Set the rootMargin to trigger when the target is 10% past the viewport
    options.rootMargin = options.rootMargin || '10% 0px 0px 0px';
    var observer = new IntersectionObserver(function (entries, observer) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          callback(entry);

          if (!options.loop)
            observer.unobserve(entry.target); // Unobserve after the first intersection
        }
      });
    }, options);
    observer.observe(target);
  }

  var extensionAnimations = function ($scope, $) {
    var $animations = $scope.find(".upk-in-animation");

    if (!$animations.length) {
      return;
    }

    var itemQueue = [];
    var delay = $animations.data("in-animation-delay")
      ? $animations.data("in-animation-delay")
      : 200;
    var queueTimer;

    function processItemQueue() {
      if (queueTimer) return; // We're already processing the queue

      queueTimer = window.setInterval(function () {
        if (itemQueue.length) {
          jQuery(itemQueue.shift()).addClass("is-inview");
          processItemQueue();
        } else {
          window.clearInterval(queueTimer);
          queueTimer = null;
        }
      }, delay);
    }

    postKitObserveTarget($($animations[0]).find('.upk-item')[0], function () {
      itemQueue.push($($animations[0]).find('.upk-item'));
      processItemQueue();
    }, {
      root: null,
      rootMargin: '0px',
      threshold: 0.8
    });
  };

  jQuery(window).on("elementor/frontend/init", function () {
    var $widgets = [
      "alex-grid",
      "alice-grid",
      "alter-grid",
      "amox-grid",
      "buzz-list",
      "classic-list",
      "elite-grid",
      "fanel-list",
      "featured-list",
      "harold-list",
      "hazel-grid",
      "kalon-grid",
      "maple-grid",
      "ramble-grid",
      "recent-comments",
      "scott-list",
      "tiny-list",
      "welsh-list",
      "wixer-grid",
    ];

    $.each($widgets, function (index, value) {
      elementorFrontend.hooks.addAction(
        "frontend/element_ready/upk-" + value + ".default",
        extensionAnimations,
      );
    });
  });
})(jQuery, window.elementorFrontend);
