<br><br><br><br><div id="alter-demo-area" class="mt-5 pt-5"><h2 class="text-danger">Loading</h2></div>

<script>
    $(document).ready(function() {

        let time;

        function slideAlters() {
            if( !$('#demo-alter-1') ) return;
            let alter = $('#demo-alter-1');
            if( alter.hasClass('up') ) {
                alter.removeClass('up');
                $('#demo-alter-1 .product-slider-image').animate({ top: '0px' }).css('background', 'transparent');
                setTimeout(
                    function() {
                        $('#demo-alter-2 .product-slider-image').animate({ top: '0px' }).css('background', 'transparent');
                    }, 350);
                setTimeout(
                    function() {
                        $('#demo-alter-3 .product-slider-image').animate({ top: '0px' }).css('background', 'transparent');
                    }, 700);
            } else {
                alter.addClass('up');
                $('#demo-alter-1 .product-slider-image').animate({ top: '-75%' }).css('background', 'rgba(240, 240, 240, 0.3)');
                
                setTimeout(
                    function() {
                        $('#demo-alter-2 .product-slider-image').animate({ top: '-50%' }).css('background', 'rgba(240, 240, 240, 0.3)');
                        
                    }, 350);
                setTimeout(
                    function() {
                        $('#demo-alter-3 .product-slider-image').animate({ top: '-25%' }).css('background', 'rgba(240, 240, 240, 0.3)');
                    }, 700);
            }
        }

        setInterval(slideAlters, 3000);
        setInterval(switch_alter, 30000);

        get_alter();

        $('#demo-submit').on('click', function() {
            if( $(this).hasClass('mc-loading') ) return;
            $(this).addClass('mc-loading');
            $(this).text('Loading');
            time = 1;
            let alter_id = $('#demo-id').val(),
                alter_type = $('#demo-type').val();
            if( ( alter_id === undefined || alter_id === 0 ) && alter_type === 0 ) {
                console.log(1);
                get_alter();
                return;
            }
            if( alter_id === '' && alter_type === '' ) {
                console.log(2);
                get_alter();
                return;
            }
            if( alter_id !== undefined && alter_id !== 0 && alter_id !== '' ) {
                console.log(3);
                get_alter(alter_id);
                return;
            }

            console.log(alter_type);
            get_alter(0, alter_type);
        });

        function get_alter( alter_id, type ) {
            $.ajax({
                type: "post",
                dataType: "json",
                url: vars.ajaxurl,
                data: {
                    action: "demo-alter",
                    type: type,
                    alter_id: alter_id
                },
                success: function( response ) {
                    if( response.alter_id === 0 || response.printing_id === 0 ) get_alter(type);
                    $('#alter-demo-area').replaceWith(get_output(response.alter_img, response.printing_img));
                    $('#demo-submit').text('Load').removeClass('mc-loading');
                }
            });
        }

        function switch_alter() {
            console.log(1);
            if( time !== undefined ) return;
            setTimeout(
                function() {
                    get_alter();
                }, 3000);
            
        }

        function get_output( alter_img, printing_img ) {
            return '<div id="alter-demo-area" class="mt-5 pt-5"><div id="as-slider-0" class="product-slider-wrapper row">' +

                '<div id="demo-alter-1" class="product-slider col-3 p-2 m-0" style="width:25%;"><div class="product-slider-images position-relative"><img class="product-slider-image product-slider-image__printing" src="' + printing_img + '"><img class="product-slider-image" src="' + alter_img + '" style="position: absolute;top:-75%;left:0;background: rgba(240, 240, 240, 0.3);"></div></div>' +

                '<div id="demo-alter-2" class="product-slider col-3 p-2 m-0" style="width:25%;"><div class="product-slider-images position-relative"><img class="product-slider-image product-slider-image__printing" src="' + printing_img + '"><img class="product-slider-image" src="' + alter_img + '" style="position: absolute;top:-50%;left:0;background: rgba(240, 240, 240, 0.3);"></div></div>' +

                '<div id="demo-alter-3" class="product-slider col-3 p-2 m-0" style="width:25%;"><div class="product-slider-images position-relative"><img class="product-slider-image product-slider-image__printing" src="' + printing_img + '"><img class="product-slider-image" src="' + alter_img + '" style="position: absolute;top:-25%;left:0;background: rgba(240, 240, 240, 0.3);"></div></div>' +
                '' +
                '' +
                '' +
                '<div class="product-slider product-slider--down col-3 p-2 m-0" data-target="0" style="width:25%"><div class="product-slider-images "><img class="product-slider-image product-slider-image__printing" src="' + printing_img + '"><img class="product-slider-image product-slider-image__alter product-slider--shake" src="' + alter_img + '"></div></div></div></div>';
        }

    });
</script>


