<div id="cutter-card-search-results" class="my-3 p-2" style="display: none;">
    <h3>Card Results</h3>
    <?php if( !empty( $cards ) ) : ?><?php foreach( $cards as $card_id ) :
        $card = get_term_by( 'id', $card_id, 'mtg_card' );
        ?>
        <div class="cutter-card-result py-2" data-card="<?= $card_id ?>"><a href="javascript:void(0);"><?= $card->name ?></a></div>
    <?php endforeach; ?><?php endif; ?>
    <style>
        #cutter-card-search-results {
            max-height: 400px;
            background: #fff;
            overflow-y: auto;
        }

        .cutter-card-result:hover {
            cursor: pointer;
        }
    </style>
</div>
