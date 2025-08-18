jQuery(document).ready(function ($) {

    if (jQuery('.wrap').hasClass('ultimate-post-kit-dashboard')) {

        // total activate
        function total_widget_status() {
            var total_widget_active_status = [];

            var totalActivatedWidgets = [];
            jQuery('#ultimate_post_kit_active_modules_page input:checked').each(function () {
                totalActivatedWidgets.push(jQuery(this).attr('name'));
            });

            total_widget_active_status.push(totalActivatedWidgets.length);

            var totalActivatedExtensions = [];
            jQuery('#ultimate_post_kit_elementor_extend_page input:checked').each(function () {
                totalActivatedExtensions.push(jQuery(this).attr('name'));
            });

            total_widget_active_status.push(totalActivatedExtensions.length);


            jQuery('#bdt-total-widgets-status').attr('data-value', total_widget_active_status);
            jQuery('#bdt-total-widgets-status-core').text(total_widget_active_status[0]);
            jQuery('#bdt-total-widgets-status-extensions').text(total_widget_active_status[1]);

            jQuery('#bdt-total-widgets-status-heading').text(total_widget_active_status[0] + total_widget_active_status[1]);

        }

        total_widget_status();

        jQuery('.ultimate-post-kit-settings-save-btn').on('click', function () {
            setTimeout(function () {
                total_widget_status();
            }, 2000);
        });

        // end total active



        // modules
        var moduleUsedWidget = jQuery('#ultimate_post_kit_active_modules_page').find('.upk-used-widget');
        var moduleUsedWidgetCount = jQuery('#ultimate_post_kit_active_modules_page').find('.upk-options .upk-used').length;

        moduleUsedWidget.text(moduleUsedWidgetCount);
        var moduleUnusedWidget = jQuery('#ultimate_post_kit_active_modules_page').find('.upk-unused-widget');
        var moduleUnusedWidgetCount = jQuery('#ultimate_post_kit_active_modules_page').find('.upk-options .upk-unused').length;
        moduleUnusedWidget.text(moduleUnusedWidgetCount);

        // total widgets

        var dashboardChatItems = ['#bdt-db-total-status', '#bdt-total-widgets-status'];

        dashboardChatItems.forEach(function ($el) {

            const ctx = jQuery($el);

            var $value = ctx.data('value');
            $value = $value.split(',');

            var $labels = ctx.data('labels');
            $labels = $labels.split(',');

            var $bg = ctx.data('bg');
            $bg = $bg.split(',');

            // var $bgHover = ctx.data('bg-hover');
            // $bgHover = $bgHover.split(',');


            const data = {
                // labels: $labels,
                datasets: [{
                    data: $value,
                    backgroundColor: $bg,
                    // hoverBackgroundColor: false, //$bgHover,
                    borderWidth: 0,
                }],

            };

            const config = {
                type: 'doughnut',
                data: data,
                options: {
                    animation: {
                        duration: 3000,
                    },
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                    },
                    title: {
                        display: false,
                        text: ctx.data('label'),
                        fontSize: 16,
                        fontColor: '#333',
                    },
                    hover: {
                        mode: null
                    },

                }
            };

            if (window.myChart instanceof Chart) {
                window.myChart.destroy();
            }

            var myChart = new Chart(ctx, config);
            // if (x != 'init'){
            //     // myChart.destroy();

            //     // var myChart = new Chart(ctx, config);
            //      myChart.update();
            // }


        });
    }

    jQuery('.ultimate-post-kit-notice.notice-error img').css({
        'margin-right': '8px',
        'vertical-align': 'middle'
    });

    /* ===================================
       API NOTICE
       =================================== */
    
    /**
     * Initialize countdown timers for API notices
     * This function finds all countdown elements and starts the countdown timer
     */
    function initAPINoticeCountdown() {
        // Find all countdown elements on the page
        jQuery('.bdt-notice-countdown').each(function() {
            var $countdown = jQuery(this);
            var $timer = $countdown.find('.countdown-timer');
            var endDate = $countdown.data('end-date');
            var timezone = $countdown.data('timezone');
            
            // Skip if no end date or timer element found
            if (!endDate || !$timer.length) {
                return;
            }
            
            /**
             * Update the countdown display
             * Calculates time remaining and formats it for display
             */
            function updateCountdown() {
                var endTime = new Date(endDate + ' ' + timezone).getTime();
                var now = new Date().getTime();
                var distance = endTime - now;
                
                // If countdown has expired, hide the countdown
                if (distance < 0) {
                    $countdown.hide();
                    return;
                }
                
                // Calculate time units
                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);
                
                // Add leading zeros
                days = days < 10 ? "0" + days : days;
                hours = hours < 10 ? "0" + hours : hours; 
                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;
                
                // Build countdown text with wrapped numbers and labels
                var countdownText = "";
                if (days > 0) {
                    countdownText += '<div class="countdown-item"><span class="number">' + days + '</span><span class="label">days</span></div><span class="separator"></span>';
                }
                // Always show hours (even if 00) for consistent layout
                countdownText += '<div class="countdown-item"><span class="number">' + hours + '</span><span class="label">hrs</span></div><span class="separator"></span>';
                
                countdownText += '<div class="countdown-item"><span class="number">' + minutes + '</span><span class="label">min</span></div><span class="separator"></span>';
                
                countdownText += '<div class="countdown-item"><span class="number">' + seconds + '</span><span class="label">sec</span></div>';
                
                // Update the timer display
                $timer.html(countdownText);
            }
            
            // Initial update to show countdown immediately
            updateCountdown();
            
            // Set up interval to update countdown every second
            setInterval(updateCountdown, 1000);
        });
    }
    
    // Initialize countdown on page load
    initAPINoticeCountdown();
    
    // Re-initialize countdown when new notices are added (for dynamic content)
    // This ensures countdown works even if notices are loaded after page load
    jQuery(document).on('DOMNodeInserted', '.bdt-notice-countdown', function() {
        initAPINoticeCountdown();
    });

    /* ===================================
       END API NOTICE
       =================================== */

});