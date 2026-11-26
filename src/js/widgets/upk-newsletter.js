/**
 * Start mailchimp widget script
 */
(function ($) {
  function gernerateHtml(type, className, text) {
    return [
      '<div class="upk-newsletter-' + type + " " + className + '">',
      '<div class="upk-alert-box">',
      text,
      "</div>",
      "</div>",
    ].join("");
  }

  $.alert = function (text) {
    $.alert.hide();
    var options = {
      text: text,
    };
    $(gernerateHtml("alert", "", options.text)).appendTo(document.body);
  };
  $.alert.hide = function () {
    $(".upk-newsletter-alert").slideUp(800).remove();
  };
})(jQuery);
/**
 * Start mailchimp widget script
 */

(function ($, elementor) {
  "use strict";

  var widgetMailChimp = function ($scope, $) {
    var $mailChimp = $scope.find(".upk-newsletter");

    if (!$mailChimp.length) {
      return;
    }

    var langStr = window.UltimatePostKitConfig.mailchimp;

    $mailChimp.on("submit", function () {
      var mailchimpform = $(this);

      $.alert(
        '<span class="uwk-newsletter-loader"></span>' + langStr.subscribing,
      );

      // The reply is shown as HTML, so it must come from this site's own
      // subscribe handler: the form's action and its "action" field are page
      // markup, which a comment or post can imitate.
      $.ajax({
        url: window.UltimatePostKitConfig.ajaxurl,
        type: "POST",
        data: mailchimpform.serialize() + "&action=ultimate_post_kit_mailchimp_subscribe",
        success: function (data) {
          setTimeout(function () {
            $.alert(data);
            setTimeout(function () {
              $.alert.hide();
            }, 5000);
          }, 2000);
        },
      });
      return false;
    });

    return false;
  };

  jQuery(window).on("elementor/frontend/init", function () {
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/upk-newsletter.default",
      widgetMailChimp,
    );
  });
})(jQuery, window.elementorFrontend);

/**
 * End mailchimp widget script
 */
