
/**
 * Start mailchimp widget script
 */
;(function($) {

    function gernerateHtml(type, className, text,) {
      return [
        '<div class="upk-newsletter-' + type + ' ' + className + '">',
          '<div class="upk-alert-box">',
              text,
          '</div>',
        '</div>'
      ].join('');
    }
 
    $.alert = function(text) {
      $.alert.hide();
      var options = {
        text: text
      };
      $(gernerateHtml('alert', '', options.text))
      .appendTo(document.body);
    };
    $.alert.hide = function() {
      $('.upk-newsletter-alert').slideUp(800).remove();
    };
  
    })(jQuery);
/**
 * Start mailchimp widget script
 */



( function( $, elementor ) {

    'use strict';

    var widgetMailChimp = function( $scope, $ ) {

        var $mailChimp = $scope.find('.upk-newsletter');
            
        if ( ! $mailChimp.length ) {
            return;
        }
 
        var langStr = window.UltimatePostKitConfig.mailchimp;

        $mailChimp.submit(function(){
            
            var mailchimpform = $(this);

            $.alert('<span class="uwk-newsletter-loader"></span>'+langStr.subscribing);

            $.ajax({
                url:mailchimpform.attr('action'),
                type:'POST',
                data:mailchimpform.serialize(),
                success:function(data){
                  setTimeout(function(){ 
                    $.alert(data);
                    setTimeout(function(){ 
                        $.alert.hide(); 
                    }, 5000);
                }, 2000);
                    
                }
            });
            return false;

        });

        return false;

    };


    jQuery(window).on('elementor/frontend/init', function() {
        elementorFrontend.hooks.addAction( 'frontend/element_ready/upk-newsletter.default', widgetMailChimp );
    });

}( jQuery, window.elementorFrontend ) );

/**
 * End mailchimp widget script
 */
 