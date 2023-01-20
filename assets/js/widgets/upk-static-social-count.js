; (function ($) {
    $(window).on("elementor/frontend/init", function () {
        elementorFrontend.hooks.addAction("frontend/element_ready/upk-static-social-count.default", function (scope) {
            scope.find('.upk-static-social-count').each(function () {
                var element = $(this)[0];
                var counter = $(element).find('.counter-value');
                function intToString(num) {
                    num = num.toString().replace(/[^0-9.]/g, '');
                    if (num <= 999) {
                        return Math.ceil(num);
                    }
                    let si = [
                        { v: 1E3, s: "K" },
                        { v: 1E6, s: "M" },
                        { v: 1E9, s: "B" },
                        { v: 1E12, s: "T" },
                        { v: 1E15, s: "P" },
                        { v: 1E18, s: "E" }
                    ];
                    let index;
                    for (index = si.length - 1; index > 0; index--) {
                        if (num >= si[index].v) {
                            break;
                        }
                    }
                    return (num / si[index].v).toFixed(2).replace(/\.0+$|(\.[0-9]*[1-9])0+$/, "$1") + si[index].s;
                }

                if (element) {
                    $(counter).each(function () {
                        var $this = $(this);
                        jQuery({ Counter: 0 }).animate({ Counter: $this.text() }, {
                            duration: 3000,
                            easing: 'swing',
                            step: function () {
                                $this.text(intToString(this.Counter));
                            }
                        });
                    });
                }
            });
        });
    });
})(jQuery);
