<?php

?>
<div class="row">
    <div class="col-md-7">
        <h2>Social Poster</h2>
        <?= do_shortcode( '[gravityform id=5 title=false description=false ajax=true]' ); ?>
    </div>
    <div class="col-md-5">

        <div id="preview" class="bg-light rounded p-3">
            <h4 class="mb-2">
                Social Preview
            </h4>
            <div id="imgview" class="mb-2"><img src="" style="max-width:200px;"></div>
            <!-- Social Post Text -->
            <p class="w-100 d-inline-block">
                <span class="as_social_post_text" style="display:none"></span>
            </p>
            <!-- Alterist -->
            <p class="w-100 d-inline-block">
                <span class="alterist_pretext" style="display:none">🖌: @</span><span class="as_social_alterist_handle" style="display:none"></span> <span class="alterist_bracket" style="display:none;">(</span><span class="as_social_alterist_source" style="display:none"></span><span class="alterist_bracket" style="display:none;">)</span>
                <br>
                <span class="card_pretext" style="display:none">🃏: #</span><span class="as_social_card_name" style="display:none"></span>
                <br>
                <span class="artist_pretext" style="display:none">🎨: @</span><span class="as_social_artist_handle" style="display:none"></span> <span class="artist_bracket" style="display:none;">(</span><span class="as_social_artist_source" style="display:none"></span><span class="artist_bracket" style="display:none;">)</span>
            </p>
            <!-- Hashtags -->
            <p class="w-100 d-inline-block">
                <span class="as_social_hashtags">#mtg #mtgalter #mtgart #mtgalterist #mtgartist #alterist #magicthegathering</span>
            </p>
            <p class="text-danger">
                Character Count: <span class="characterCount">0</span>
            </p>
        </div>
    </div>
</div>
<script>
    $(function() {
        <?php
        $args = [
            'post_type'      => 'social_post',
            'order'          => 'DESC',
            'orderby'        => 'ID',
            'posts_per_page' => 1,
        ];
        // the query
        $the_query = new WP_Query( $args );
        if ( $the_query->have_posts() ) :
        while ( $the_query->have_posts() ) : $the_query->the_post();
        $featured_artists = sa_post_meta( 'as_social_featured_alterists' );
        $new_artists = sa_post_meta( 'as_social_new_alterists' );

        $featured_artists = explode( PHP_EOL, $featured_artists );
        $featured_output = false;
        $fa_count = count( $featured_artists );
        $i = 1;
        foreach( $featured_artists as $artist ) {
            if( strlen( $artist ) < 2 ) {
                // do nothing
            } else {
                $artist          = preg_replace( '/\s+/', ' ', trim( $artist ) );
                $featured_output .= $artist;
                if( $i !== $fa_count ) {
                    $featured_output .= " ";
                }
            }
            $i ++;
        }
        ?>
        var featured_artists = '<?= $featured_output; ?>';
        // Populate fields on load
        $("#input_5_13").html(featured_artists.split(" ").join("\n"));

        <?php
        $new_artists = explode( PHP_EOL, $new_artists );
        $new_output = false;
        $fa_count = count( $new_artists );
        $i = 1;
        foreach( $new_artists as $artist ) {
            if( strlen( $artist ) < 2 ) {
                // do nothing
            } else {
                $artist     = preg_replace( '/\s+/', ' ', trim( $artist ) );
                $new_output .= $artist;
                if( $i !== $fa_count ) {
                    $new_output .= " ";
                }
            }
            $i ++;
        }
        ?>
        var new_artists = '<?= $new_output; ?>';
        // Populate fields on load
        $("#input_5_14").html(new_artists.split(" ").join("\n"));
        <?php
        endwhile;
        endif;
        wp_reset_postdata();
        ?>
    });

    function readURL( input ) {

        if( input.files && input.files[0] ) {
            var reader = new FileReader();
            jQuery('#imgview').show();
            reader.onload = function( e ) {
                jQuery('#imgview>img').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // your File input id "#input_1_2"

    jQuery(document).on("change", "#input_5_17", function() {
        readURL(this);
    });
    /* Preview Generator */
    $(function() {
        $('.characterCount').text($("#input_5_12").val().length);
        // Post Text
        $('#input_5_11').keyup(function() {
            if( $(this).val().length > 0 ) {
                $('.as_social_post_text').show();
                $('.as_social_post_text').text($(this).val());
            } else {
                $('.as_social_post_text').hide();
            }
        });
        // Update Card Name with Title
        $('#input_5_2').keyup(function() {
            $('#input_5_4').val($(this).val());
        });
        // Alterist Elements
        $('#input_5_5').keyup(function() {
            if( $(this).val().length > 0 ) {
                // Reveal Alterist elements
                $('.as_social_alterist_handle').show();
                $('.alterist_pretext').show();
                $('.alterist_pretext').show();
                $('.as_social_alterist_handle').text($(this).val());
            } else {
                $('.as_social_alterist_handle').hide();
                $('.alterist_pretext').hide();
                $('.alterist_pretext').hide();
            }
        });
        // Alterist Source
        $('#input_5_15').change(function() {
            $('.alterist_bracket').show();
            $('.as_social_alterist_source').show();
            if( $(this).val() == 'artstation' ) {
                $('.as_social_alterist_source').text("artstation");
            } else if( $(this).val() == 'deviantart' ) {
                $('.as_social_alterist_source').text("deviantart");
            } else if( $(this).val() == 'facebook' ) {
                $('.as_social_alterist_source').text("fb");
            } else if( $(this).val() == 'instagram' ) {
                $('.as_social_alterist_source').text("ig");
            } else if( $(this).val() == 'reddit' ) {
                $('.as_social_alterist_source').text("reddit");
            } else if( $(this).val() == 'twitter' ) {
                $('.as_social_alterist_source').text("tw");
            } else {
                $('.alterist_bracket').hide();
                $('.as_social_alterist_source').hide();
            }
        });
        // Card Elements
        $('#input_5_4').keyup(function() {
            if( $(this).val().length > 0 ) {
                // Reveal Alterist elements
                $('.as_social_card_name').show();
                $('.card_pretext').show();
                $('.as_social_card_name').text($(this).val().replace(/\s/g, ''));
            } else {
                $('.as_social_card_name').hide();
                $('.card_pretext').hide();
            }
        });
        // Artist Elements
        $('#input_5_8').keyup(function() {
            if( $(this).val().length > 0 ) {
                // Reveal Alterist elements
                $('.as_social_artist_handle').show();
                $('.artist_pretext').show();
                $('.artist_pretext').show();
                $('.as_social_artist_handle').text($(this).val());
            } else {
                $('.as_social_artist_handle').hide();
                $('.artist_pretext').hide();
                $('.artist_pretext').hide();
            }
        });
        // Artist Source
        $('#input_5_16').change(function() {
            $('.artist_bracket').show();
            $('.as_social_artist_source').show();
            if( $(this).val() == 'artstation' ) {
                $('.as_social_artist_source').text("artstation");
            } else if( $(this).val() == 'deviantart' ) {
                $('.as_social_artist_source').text("deviantart");
            } else if( $(this).val() == 'facebook' ) {
                $('.as_social_artist_source').text("fb");
            } else if( $(this).val() == 'instagram' ) {
                $('.as_social_artist_source').text("ig");
            } else if( $(this).val() == 'reddit' ) {
                $('.as_social_artist_source').text("reddit");
            } else if( $(this).val() == 'tw' ) {
                $('.as_social_artist_source').text("tw");
            } else {
                $('.artist_bracket').hide();
                $('.as_social_artist_source').hide();
            }
        });
        // Hashtags
        $('#input_5_12').keyup(function() {
            if( $(this).val().length > 0 ) {
                $('.as_social_hashtags').show();
                $('.as_social_hashtags').text($(this).val());
            } else {
                $('.as_social_hashtags').hide();
            }
        });
        // Add hashtags to words
        var a = document.getElementById('input_5_12');
        a.addEventListener('keydown', addHash, false);

        function addHash( event ) {
            if( event.keyCode === 32 && event.target.value.length ) {
                event.preventDefault();
                var elem = event.target,
                    val = elem.value;
                if( val.slice(-1) !== '#' ) {
                    elem.value += ' #';
                }
            } else if( !event.target.value.length ) {
                if( event.keyCode === 32 ) {
                    event.preventDefault();
                }
                event.target.value = '#';
            }
        }

        /* Character Counter */
        $("#input_5_11, #input_5_5, #input_5_4, #input_5_8, #input_5_12").on("input", function() {
            // Get the length of each input's current value, then put it
            // in the .character-counter div

            /* Social Text */
            // Post Text
            if( $("#input_5_11").val().length > 0 ) {
                var postText = $("#input_5_11").val().length + 2; // 2 accounts for line breaks
            } else {
                var postText = $("#input_5_11").val().length
            }

            /* Credits */
            // Alterist
            if( $("#input_5_5").val().length > 0 ) {
                if( $("#input_5_15").val() == 'artstation' ) {
                    var sourceLength = 14;
                } else if( $("#input_5_15").val() == 'deviantart' ) {
                    var sourceLength = 14;
                } else if( $("#input_5_15").val() == 'facebook' ) {
                    var sourceLength = 6;
                } else if( $("#input_5_15").val() == 'instagram' ) {
                    var sourceLength = 6;
                } else if( $("#input_5_15").val() == 'reddit' ) {
                    var sourceLength = 10;
                } else if( $("#input_5_15").val() == 'tw' ) {
                    var sourceLength = 6;
                } else {
                    var sourceLength = 1;
                }
                var alteristHandle = $("#input_5_5").val().length + parseInt(sourceLength) + 6; // 4 for pretext and 1 for line break
            } else {
                var alteristHandle = $("#input_5_5").val().length
            }

            // Card Name
            if( $("#input_5_4").val().length > 0 ) {
                var cardName = $("#input_5_4").val().replace(/\s/g, '').length + 6; // 4 for pretext and 1 for line break

            } else {
                var cardName = $("#input_5_4").val().length;
            }

            // Alterist
            if( $("#input_5_8").val().length > 0 ) {
                if( $("#input_5_16").val() == 'artstation' ) {
                    var sourceLength = 13;
                } else if( $("#input_5_16").val() == 'deviantart' ) {
                    var sourceLength = 13;
                } else if( $("#input_5_16").val() == 'facebook' ) {
                    var sourceLength = 5;
                } else if( $("#input_5_16").val() == 'instagram' ) {
                    var sourceLength = 5;
                } else if( $("#input_5_16").val() == 'reddit' ) {
                    var sourceLength = 9;
                } else if( $("#input_5_16").val() == 'tw' ) {
                    var sourceLength = 5;
                } else {
                    var sourceLength = 0;
                }
                var artistHandle = $("#input_5_8").val().length + parseInt(sourceLength) + 5; // 4 for pretext and 1 for line break
            } else {
                var artistHandle = $("#input_5_8").val().length
            }

            // Hashtags
            if( $("#input_5_12").val().length > 0 ) {
                var hashtags = $("#input_5_12").val().length + 2; // 4 for pretext and 1 for line break
            } else {
                var hashtags = $("#input_5_12").val().length;
            }
            // Character Count output
            $('.characterCount').text(postText + alteristHandle + cardName + artistHandle + hashtags);

        });
    });

    jQuery('#imgview').hide();
    /* Make preview scroll down */
    ( function( $ ) {
        var element = $('#preview'),
            originalY = element.offset().top;

        // Space between element and top of screen (when scrolling)
        var topMargin = 40;

        // Should probably be set in CSS; but here just for emphasis
        element.css('position', 'relative');

        $(window).on('scroll', function( event ) {
            var scrollTop = $(window).scrollTop();

            element.stop(false, false).animate({
                top: scrollTop < originalY
                    ? 0
                    : scrollTop - originalY + topMargin
            }, 300);
        });

    } )(jQuery);

</script>