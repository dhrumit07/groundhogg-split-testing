(function (reporting, $, nonces) {

    $(function () {
        $('#split-step').change(function () {
            reporting.refresh(reporting.calendar);
        });
    });


    $(function () {
        $('#funnel-id').change(function () {
            $.ajax({
                type: 'post',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'groundhogg_refresh_split_steps',
                    funnel_id: $('#funnel-id').val(),
                },
                success: function (response) {
                    var $el = $("#split-step");
                    $el.empty(); // remove old options
                    $.each(response.options, function( value , key) {
                        $el.append($("<option></option>")
                            .attr("value", value).text(key));
                    });
                    reporting.refresh(reporting.calendar);
                },
            })
        });
    });


})(GroundhoggReporting, jQuery, groundhogg_nonces);