jQuery(document).ready(function() {

    /** Change background color of image **/
    $(".review-item__image-float").click(function() {
        if( $(this).hasClass('review-item__image-float--green') ) {
            $(this).removeClass('review-item__image-float--green');
            $(this).addClass('review-item__image-float--grey');
        } else {
            $(this).removeClass('review-item__image-float--grey');
            $(this).addClass('review-item__image-float--green');
        }
    });

    /** Regenerate Files **/
    jQuery(".design--regenerate").click(function( e ) {
        var id = jQuery(this).data('design-id');
        var deleteThis = jQuery(this);
        jQuery(this).html(jQuery(this).html().replace('REGENERATE', 'REGENERATING'));
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "cas-regenerate-variation-files",
                product_id: id,
                mc_nonce: getNonceValByAction('cas-regenerate-variation-files')
            },
            success: function( response ) {
                deleteThis.html('Regenerated files will be available shortly. Reloading page');
                location.reload();
            }
        })
    });

    jQuery(".design--reindex").click(function( e ) {
        var id = jQuery(this).data('design-id');
        var deleteThis = jQuery(this);
        jQuery(this).html(jQuery(this).html().replace('REINDEX', 'REINDEXING'));
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "reindex-alter",
                product_id: id,
                mc_nonce: getNonceValByAction('cas-regenerate-variation-files')
            }
        })
    });

    /** Hide completed Images **/
    $(".order2process__design img").on('click', function( event ) {
        jQuery(this).toggleClass('hidden');
    });

    /** Download label CSV **/
    $(document).on('click', '#orders-label-csv', function() {
        const fileName = "labels.csv";
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-orders-label-csv",
                mc_nonce: getNonceValByAction('as-orders-label-csv')
            },
            success: function( response ) {
                let data = decodeURIComponent(escape(response.data));
                saveData(data, fileName);
            }
        })
    });

    $(document).on('click', '#orders-send-all', function() {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "send-orders-print",
                mc_nonce: getNonceValByAction('send-orders-print')
            },
            success: function( response ) {
                $(this).text('Sent');
            }
        })
    });

    $(document).on('click', '#withdrawals-csv', function() {
        const fileName = "withdrawals.csv";
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-withdrawals-csv",
                mc_nonce: getNonceValByAction('as-withdrawals-csv')
            },
            success: function( response ) {
                console.log(response);
                let data = decodeURIComponent(escape(response.data));
                saveData(data, fileName);
            }
        })
    });

    $(document).on('click', '#royalties-csv', function() {
        const fileName = "royalties.csv";
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-royalties-csv",
                mc_nonce: getNonceValByAction('as-royalties-csv')
            },
            success: function( response ) {
                let data = decodeURIComponent(escape(response.data));
                saveData(data, fileName);
            }
        })
    });

    /** Download label CSV **/
    const saveData = ( function() {
        const a = document.createElement("a");
        document.body.appendChild(a);
        a.style = "display: none";
        return function( data, fileName ) {
            const blob = new Blob([data], { type: "octet/stream" }),
                url = window.URL.createObjectURL(blob);
            a.href = url;
            a.download = fileName;
            a.click();
            window.URL.revokeObjectURL(url);
        };
    }() );

    /** Send Files to Rasterlink **/
    $(document).on('click', '.order2process__print--files', function() {
        var id = jQuery(this).data('order-id');
        var printThis = jQuery(this);
        jQuery(this).html(jQuery(this).html().replace('SEND FILES', 'SENDING'));
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "order-files-to-print",
                order_id: id,
                user_id: vars.user_id,
                mc_nonce: getNonceValByAction('order-files-to-print')
            },
            success: function( response ) {
                printThis.html(printThis.html().replace('SENDING', 'SEND FILES'));
                jQuery('#order_' + id + ' button').removeClass('pink--button').addClass('grey--button');
            }
        })
    });

    $(document).on('click', '.order2process__print--product', function() {
        var printThis = jQuery(this);
        var order_id = $(this).data('order-id');
        var product_id = $(this).data('alter-id');
        var quantity = $('#product-count-' + product_id).val();
        jQuery(this).html(jQuery(this).html().replace('SEND FILES', 'SENDING'));
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-alter-pdf-rasterlink",
                order_id: order_id,
                product_id: product_id,
                user_id: vars.user_id,
                quantity: quantity
            },
            success: function( response ) {
                console.log(response);
                printThis.html(printThis.html().replace('SENDING', 'SEND FILES'));
                jQuery('#order_' + order_id + ' .order2process__print--product').removeClass('pink--button').addClass('grey--button');
            }
        })
    });

    /** Remove rows **/
    $(document).on('click', '.order2process__remove--init', function() {
        var id = jQuery(this).attr("href");
        var counter = jQuery('.order_count');
        var currentCount = counter.text();
        var newCount = currentCount - 1;
        id = id.substr(1);

        jQuery('#order_' + id).remove();
        counter.text(newCount);
    });

    /** Complete Order **/
    $(document).on('click', '.order2process__complete-order', function() {
        var id = jQuery(this).attr("href");
        id = id.substr(1);
        jQuery('#order_' + id).remove();
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-order-complete",
                order_id: id,
                mc_nonce: getNonceValByAction('as-order-complete')
            }
        })
    });

    /** Re-Complete Order **/
    $(document).on('click', '.order2process__recomplete-order', function() {
        var id = jQuery(this).attr("href");
        id = id.substr(1);
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-order-complete",
                order_id: id,
                mc_nonce: getNonceValByAction('as-order-complete')
            },
            success: function( response ) {
                jQuery('#order_' + id + ' .order2process__recomplete-order button strong').html('RE-CONFIRMED');
                jQuery('#order_' + id + ' .order2process__recomplete-order button').removeClass('green--button').addClass('grey--button');
            }
        })
    });

    /** Re-Ship Order **/
    $(document).on('click', '.order2process__reship', function() {
        var id = jQuery(this).data('order-id');
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "as-order-reship",
                order_id: id,
                mc_nonce: getNonceValByAction('as-order-reship')
            },
            success: function( response ) {
                jQuery('#order_' + id + ' .order2process__reship button strong').html('RE-SHIPPED');
                jQuery('#order_' + id + ' .order2process__reship button').removeClass('green--button').addClass('grey--button');
            }
        })
    });

    /** Order Queries **/
    $(".orders-filter-button").on('click', function( event ) {
        jQuery.ajax({
            type: "post",
            dataType: "json",
            url: vars.ajaxurl,
            data: {
                action: "cas-orders-filter",
                query: jQuery('#orders_id').val(),
                status: jQuery('#orders_status').val(),
                date: jQuery('#orders_date').val(),
                mc_nonce: getNonceValByAction('orders-filter-button')
            },
            success: function( response ) {
                jQuery('.orders-results').html('');
                jQuery('.orders-results').html(response.html);
            }
        })

    });

});