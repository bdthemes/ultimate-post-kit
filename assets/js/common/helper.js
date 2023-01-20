// make the window global for site js
var $window = $(window);

var debounce = function (func, wait, immediate) {
    // 'private' variable for instance
    // The returned function will be able to reference this due to closure.
    // Each call to the returned function will share this common timer.
    var timeout;

    // Calling debounce returns a new anonymous function
    return function () {
        // reference the context and args for the setTimeout function
        var context = this,
            args = arguments;

        // Should the function be called now? If immediate is true
        //   and not already in a timeout then the answer is: Yes
        var callNow = immediate && !timeout;

        // This is the basic debounce behaviour where you can call this
        //   function several times, but it will only execute once
        //   [before or after imposing a delay].
        //   Each time the returned function is called, the timer starts over.
        clearTimeout(timeout);

        // Set the new timeout
        timeout = setTimeout(function () {

            // Inside the timeout function, clear the timeout variable
            // which will let the next execution run when in 'immediate' mode
            timeout = null;

            // Check if the function already ran with the immediate flag
            if (!immediate) {
                // Call the original function with apply
                // apply lets you define the 'this' object as well as the arguments
                //    (both captured before setTimeout)
                func.apply(context, args);
            }
        }, wait);

        // Immediate mode and no wait timer? Execute the function..
        if (callNow) func.apply(context, args);
    };
};

// $(document).on("click", ".ep-pagination .upk-pagination a", function (e) {
//     e.preventDefault();

//     const url = $(this).prop('href');
//     const widgetId = $(this).parents('ul').data('widget-id')
//     var upkWidget = '.elementor-element-' + widgetId;

//     $.ajax({
//         url: url,
//         success: function (response) {
//             var html = $(response).find(upkWidget).html();
//             if (html) $(upkWidget).html(html);
//         }
//     });
// });

// hide orphan

$window.on('elementor/frontend/init', function () {
    var ModuleHandler = elementorModules.frontend.handlers.Base,
        OrphanHide;

    OrphanHide = ModuleHandler.extend({

        bindEvents: function () {
            this.run();
        },

        onElementChange: debounce(function (prop) {
            if (prop.indexOf('hide_orphan_') !== -1) {
                this.run();
            }
        }, 400),

        settings: function (key) {
            return this.getElementSettings(key);
        },

        run: function () {
            var $element = this.findElement('.elementor-widget-container').get(0);

            if (this.settings('hide_orphan_tablet') == 'yes') {
                var
                    tablet_column = this.settings('columns_tablet'),
                    widgetWrapper = $($element).find('.upk-grid-wrapper'),
                    totalItem = widgetWrapper.children().length;

                var result = totalItem % tablet_column;

                if (result !== 0) {
                    $(widgetWrapper).children().slice(-result).addClass('upk-hide-orphan-tablet');
                }
            }

        }
    });

    elementorFrontend.hooks.addAction('frontend/element_ready/widget', function ($scope) {
        elementorFrontend.elementsHandler.addHandler(OrphanHide, {
            $element: $scope
        });
    });
});