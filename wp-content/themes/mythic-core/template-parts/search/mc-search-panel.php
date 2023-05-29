<?php $search = !empty( $_GET['search'] ) ? $_GET['search'] : ''; ?>
<form action="/" class="as_ajax_form mc_search_form">
    <input type="text" class="mc_sets_search_input" name="mc_sets_search_input" value="<?php echo $search ?>">
    <button type="submit">Filter</button>
    <?php Mythic_Core\Ajax\Search\MC_Ajax_Search::render_nonce(); ?>
</form>