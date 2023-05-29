jQuery(function( $ ) {
    $(document).on('submit', '.form-admin-mythic-core-run-commands', function( e ) {
        e.preventDefault();
        let currentSubmit = $(this).find('#submit').attr('disabled', true);
        let data = {
            'action': "mcAdminRunCommands",
            'function_namespace': $('#function_namespace').val(),
            'function_name': $('#function_name').val(),
            'function_parameters': $('#function_parameters').val(),
        };

        checkAndAddDataContainers(currentSubmit);

        $request = $.ajax({
            type: "post",
            dataType: "json",
            url: ajaxurl,
            data: data,
            success: function( response ) {
                if( !response ) return;

                currentSubmit.attr('disabled', false);
                $('#mc-admin-run-commands-notices').text(response.message);
                $('#mc-admin-run-commands-data').html(JSON.stringify(response.data))
            }
        });

    });

    function checkAndAddDataContainers( currentSubmit ) {
        if( $('#mc-admin-run-commands-notices').length || $('#mc-admin-run-commands-data').length ) return;
        let currentSubmitParent = currentSubmit.parent();
        currentSubmitParent.before("<p id='mc-admin-run-commands-notices'></p>");
        currentSubmitParent.after("<pre id='mc-admin-run-commands-data'></pre>");
    }

});