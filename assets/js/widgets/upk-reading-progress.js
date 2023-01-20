/**
 * Start news ticker widget script
 */

(function ($, elementor) {

	'use strict';

	var widgetReadingProgress = function ($scope, $) {

		var $scrolline = $scope.find('.upk-reading-progress'),
			$settings = $scrolline.data('settings');

		if (!$scrolline.length) {
			return;
		}
		if ($('.upk-reading-progress-wrap')) {
			$('.upk-reading-progress-wrap').remove();
		}

		jQuery.scrolline($settings);

		
		 if ($("body.admin-bar").length && $settings.position == 'top') {
		 	$('.upk-reading-progress-wrap').css('margin-top', 32);
		 }

	};

	jQuery(window).on('elementor/frontend/init', function () {
		elementorFrontend.hooks.addAction('frontend/element_ready/upk-reading-progress.default', widgetReadingProgress);
	});

}(jQuery, window.elementorFrontend));

/**
 * End news ticker widget script
 */