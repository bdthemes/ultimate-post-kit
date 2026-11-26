/**
 * Safe values from widget markup
 *
 * Widget handlers read their options from data-settings and other data-*
 * attributes. Those are only trustworthy when the widget printed them: markup
 * that imitates a widget (a comment or excerpt that keeps class and data-*
 * through KSES) reaches the same handler, and an Elementor element id saved by a
 * Contributor ends up inside the selectors the widgets print. Swiper resolves
 * navigation/pagination selectors through a helper that builds elements from any
 * string holding "<", and writes some string options into markup, so values that
 * could carry markup are dropped. Ultimate Post Kit and Ultimate Post Kit Pro
 * both ship these; the first one loaded wins.
 */
(function (window) {

    'use strict';

    var UNSAFE_STRING = /[<>"'`]/;

    if (typeof window.upkSafeSelector !== 'function') {
        /**
         * A selector read from widget markup, or one that matches nothing when the
         * string could be parsed as HTML. Elements and jQuery collections a handler
         * builds itself pass through: jQuery never parses those as HTML.
         */
        window.upkSafeSelector = function (selector) {
            if (typeof selector === 'string') {
                return UNSAFE_STRING.test(selector) ? '#upk-invalid-selector' : selector;
            }
            return selector && typeof selector === 'object' ? selector : '#upk-invalid-selector';
        };
    }

    if (typeof window.upkSafeSwiperSettings !== 'function') {
        /**
         * Swiper options read from widget markup: plain JSON values only, no string
         * that could carry markup, and none of the options Swiper turns into markup.
         */
        window.upkSafeSwiperSettings = function (settings) {
            var DROPPED_KEYS = ['virtual', 'injectStyles', 'injectStylesUrls', 'on', 'modules', '__proto__', 'constructor', 'prototype'];

            var clean = function (value, depth) {
                if (depth > 6) {
                    return undefined;
                }
                if (value === null || typeof value === 'boolean') {
                    return value;
                }
                if (typeof value === 'number') {
                    return isFinite(value) ? value : undefined;
                }
                if (typeof value === 'string') {
                    return UNSAFE_STRING.test(value) ? undefined : value;
                }
                if (Array.isArray(value)) {
                    return value.map(function (item) {
                        return clean(item, depth + 1);
                    }).filter(function (item) {
                        return item !== undefined;
                    });
                }
                if (typeof value === 'object') {
                    var out = {};
                    Object.keys(value).forEach(function (key) {
                        if (DROPPED_KEYS.indexOf(key) !== -1) {
                            return;
                        }
                        var item = clean(value[key], depth + 1);
                        if (item !== undefined) {
                            out[key] = item;
                        }
                    });
                    return out;
                }
                return undefined;
            };

            var out = clean(settings, 0);
            if (!out || typeof out !== 'object' || Array.isArray(out)) {
                out = {};
            }

            // Swiper writes bulletElement into markup as a tag name.
            if (out.pagination && typeof out.pagination === 'object' && 'bulletElement' in out.pagination &&
                !/^[a-z][a-z0-9-]*$/i.test(String(out.pagination.bulletElement))) {
                delete out.pagination.bulletElement;
            }

            return out;
        };
    }

    if (typeof window.upkSafeUrl !== 'function') {
        /**
         * A link read from widget markup, or an empty string unless it is a web, mail,
         * phone or relative link. data-* attributes are not URL attributes, so KSES
         * keeps "javascript:" in them.
         */
        window.upkSafeUrl = function (url) {
            if (typeof url !== 'string') {
                return '';
            }

            var value = url.trim();
            var scheme = /^([a-z][a-z0-9+.\-]*):/i.exec(value.replace(/[\u0000- \u007f-\u009f]/g, ''));

            if (scheme && ['http', 'https', 'mailto', 'tel'].indexOf(scheme[1].toLowerCase()) === -1) {
                return '';
            }

            return value;
        };
    }

}(window));

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
