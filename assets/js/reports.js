(function (reporting, $, nonces) {

   $(function () {
        $( '#broadcast-a' ).change( function () {
            reporting.refresh( reporting.calendar );
        });
   });


    $(function () {
        $( '#broadcast-b' ).change( function () {
            reporting.refresh( reporting.calendar );
        });
    });

})(GroundhoggReporting, jQuery, groundhogg_nonces);