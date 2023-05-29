$(function() {

    let campaignConnector,
        campaignExt,
        campaignSourceSelector = '#input-campaign-source',
        campaignMediumSelector = '#input-campaign-medium',
        campaignNameSelector = '#input-campaign-name',
        url = window.location.href,
        shortUrl = document.getElementById("input-short-url"),
        urlArr = window.location.href.split('?');

    $(document).on('click', '#button-build-campaign-url', function() {
        let campaignUrl = $('#input-campaign-original-url').val(),
            campaignSource = $(campaignSourceSelector).length ? $(campaignSourceSelector).val() : '',
            campaignMedium = $(campaignMediumSelector).length ? $(campaignMediumSelector).val() : '',
            campaignName = $(campaignNameSelector).length ? $(campaignNameSelector).val() : '';
        if( !campaignSource.length || !campaignMedium.length || !campaignName ) return;
        campaignExt = 'utm_source=' + encodeURI(campaignSource)
            + '&utm_medium=' + encodeURI(campaignMedium)
            + '&utm_campaign=' + encodeURI(campaignName);

        campaignConnector = url.indexOf("?") !== -1 ? '?' : '&';

        campaignExt = campaignConnector + campaignExt;
        campaignUrl = campaignUrl + campaignExt;
        $('.campaign-url').empty().text(campaignUrl);
        $('#input-campaign-built-url').attr('value', campaignUrl);
        hideButtonUrl();

        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-shorten-link",
                destination: campaignUrl,
                mc_nonce: getNonceValByAction('as-shorten-link')
            },
            success: function( e ) {
                console.log(e);
                $('.short-url').text(e.link);
                $('#input-short-url').attr('value', e.link);
                shortUrl.select();
                shortUrl.setSelectionRange(0, 99999);
                document.execCommand("copy");
            }
        });

    });

    $(document).on('click', '.close-campaign-url-builder', function() {
        $('.tool-wrapper').slideUp();
        $('.instructions').slideDown();
        $(this).hide();
        $('.open-campaign-url-builder').show();
    });

    $(document).on('click', '.open-campaign-url-builder', function() {
        $('.tool-wrapper').slideDown();
        $('.instructions').slideUp();
        $(this).hide();
        $('.close-campaign-url-builder').show();
    });
    $(document).on('click', '.instructions', function() {
        $('.tool-wrapper').slideDown();
        $(this).slideUp();
        $('.open-campaign-url-builder').hide();
        $('.close-campaign-url-builder').show();
    });

    function hideButtonUrl() {
        $('#button-build-campaign-url').slideUp(500);
        showTextCopy();
    }

    function showTextCopy() {
        $('.text-copied').slideDown(500);
        hideTextCopy();
    }

    function hideTextCopy() {
        $('.text-copied').slideUp(500);
        showButtonUrl();
    }

    function showButtonUrl() {
        $('#button-build-campaign-url').slideDown(500);
    }

    /** Ad pack **/
    $(".design--ads").click(function( e ) {
        var id = $(this).data('design-id');
        var _ = $(this);

        var idPrinting = findGetParameter('printing_id', 0);

        $(this).html($(this).html().replace('ASSETS', 'ZIPPING'));
        $.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "cas-generate-ad-assets",
                product_id: id,
                printing_id: idPrinting,
                mc_nonce: getNonceValByAction('cas-generate-ad-assets')
            },
            success: function( response ) {
                if( response.url !== '' ) {
                    location.href = response.url;
                }
                _.html(_.html().replace('ZIPPING', 'ASSETS'));
            }
        })
    });

})