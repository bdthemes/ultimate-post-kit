/* eslint-disable prettier/prettier */
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

    const widgetAjaxGrid = function ($scope, $) {
        let ajaxGrid = $scope.find(".upk-ajax-grid"),
            loadmoreSettings = ajaxGrid.data("loadmore"),
            animation,
            parentSettings = $scope.data("settings");

        if (typeof parentSettings !== "undefined") {
            animation =
                parentSettings.upk_in_animation_show !== undefined ? "yes" : "";
        }
        if (!ajaxGrid.length) {
            return;
        }
        if (loadmoreSettings.loadmore_enable !== "yes") {
            return;
        }
        const settings = ajaxGrid.data("settings"),
            loadmoreButton = $scope.find(".upk-loadmore-btn"),
            loadmoreContainer = $scope.find(".upk-loadmore-container"),
            gridWrarpper = ajaxGrid.find(".upk-ajax-grid-wrap"),
            delay = gridWrarpper.data("in-animation-delay")
                ? gridWrarpper.data("in-animation-delay")
                : 200;
        let loading = false,
            nomorePosts = false,
            currentItemCount = settings.posts_per_page;
        const loadMorePosts = function () {
            if (nomorePosts) return;
            // animation queue start
            var itemQueue = [];
            var queueTimer;

            // ajax data
            let action;
            const match = ajaxGrid?.attr("class")?.match(/upk-([a-z0-9_-]+)-grid/);

            if (match) {
                action = `upk_${match[1]}_grid_loadmore_posts`;
            }

            let dataSettings = {
                action: action,
                settings: settings,
                per_page: settings.ajax_item_load,
                offset: currentItemCount,
                animation: animation,
            };

            // ajax call
            jQuery.ajax({
                url: window.UltimatePostKitConfig.ajaxurl,
                type: "post",
                data: dataSettings,
                success: function (response) {
                    $(gridWrarpper).append(response.markup);
                    currentItemCount += settings.ajax_item_load;
                    loading = false;
                    if (loadmoreSettings.loadmore_btn === "yes") {
                        loadmoreButton.html("Load More");
                    }
                    if ($(response.markup).length < settings.ajax_item_load) {
                        nomorePosts = true;
                        loadmoreButton.hide();
                        loadmoreContainer.hide();
                    }

                    // animation queue start
                    if (animation === "yes") {
                        function processItemQueue() {
                            if (queueTimer) return;
                            queueTimer = window.setInterval(function () {
                                if (itemQueue.length) {
                                    $(itemQueue.shift()).addClass("is-inview");
                                    processItemQueue();
                                } else {
                                    window.clearInterval(queueTimer);
                                    queueTimer = null;
                                }
                            }, delay);
                        }

                        postKitObserveTarget($('.upk-ajax-grid .upk-item:not(.is-inview)')[0], function () {
                            itemQueue.push($('.upk-ajax-grid .upk-item:not(.is-inview)'));
                            processItemQueue();
                        }, {
                            root: null,
                            rootMargin: '0px',
                            threshold: 0.8
                        });
                        
                    }
                },
            });
        };

        if (loadmoreSettings.loadmore_btn === "yes")
            $(loadmoreButton).on("click", function () {
                if (!loading) {
                    loading = true;
                    loadmoreButton.html("loading...");
                    loadMorePosts();
                } else {
                    loading = false;
                }
            });

        if (loadmoreSettings.infinite_scroll === "yes") {
            $(window).on("scroll", function () {
                if (
                    $(window).scrollTop() ==
                    $(document).height() - $(window).height()
                ) {
                    $(".upk-ajax-loading").css("display", "block");
                    loadMorePosts();
                }
            });
        }
    };

    jQuery(window).on('elementor/frontend/init', function () {

        const ajaxLoadMoreWidgets = [
            "upk-kalon-grid",
            "upk-alex-grid",
            "upk-alice-grid",
            "upk-alter-grid",
            "upk-elite-grid",
            "upk-hazel-grid",
            "upk-gratis-grid",
            "upk-maple-grid",
            "upk-pixina-grid",
            "upk-ramble-grid",
        ];
        
        ajaxLoadMoreWidgets.forEach(widget => {
            elementorFrontend.hooks.addAction(
                `frontend/element_ready/${widget}.default`,
                widgetAjaxGrid
            );
        });
    });
})(jQuery, window.elementorFrontend);
