(function ($, elementor) {
	"use strict";

	var openLinkNewTab = function ($scope, $) {
		var $widgetWrapper = $scope.find(".elementor-widget");

		if (!$widgetWrapper.length) {
			return;
		}

		if (
			jQuery($widgetWrapper).data("settings") !== undefined &&
			jQuery($widgetWrapper).data("settings").upk_link_new_tab === "yes"
		) {
			$widgetWrapper.find('.upk-title a, .upk-readmore').attr("target", "_blank");
		}

	};

	jQuery(window).on("elementor/frontend/init", function () {
		elementorFrontend.hooks.addAction(
			"frontend/element_ready/global",
			openLinkNewTab,
		);
	});
})(jQuery, window.elementorFrontend);
