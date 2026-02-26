jQuery(document).ready(function ($) {
    // Delegate to capture dynamically injected biggopties as well
    $(document).on('click', '.ultimate-post-kit-biggopti.is-dismissible .bdt-biggopti-dismiss', function () {
        $this = $(this).parents('.ultimate-post-kit-biggopti');
        var $id = $this.attr('id') || '';
        var $time = $this.attr('dismissible-time') || '';
        var $meta = $this.attr('dismissible-meta') || '';
        $.ajax({
            url: (window.UltimatePostKitBiggoptiConfig && UltimatePostKitBiggoptiConfig.ajaxurl) ? UltimatePostKitBiggoptiConfig.ajaxurl : (typeof ajaxurl !== 'undefined' ? ajaxurl : ''),
            type: 'POST',
            data: {
                action: 'ultimate-post-kit-biggopties',
                id: $id,
                meta: $meta,
                time: $time,
                _wpnonce: UltimatePostKitBiggoptiConfig.nonce,
            }
        });
    });
});