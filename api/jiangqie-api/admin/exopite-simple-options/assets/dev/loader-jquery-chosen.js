;(function( $ ) {
    "use strict";

    $( document ).ready(function() {

        $( '.exopite-sof-field-select ').find( 'select.chosen' ).each(function(index, el) {
            $( el ).chosen({
                disable_search_threshold: 15,
                width: '375px',
                allow_single_deselect: true
            });
        });

    });

}(jQuery));
