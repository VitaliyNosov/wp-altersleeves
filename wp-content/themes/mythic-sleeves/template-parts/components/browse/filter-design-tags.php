<?php

$tags = MC_Alter_Functions::get_tags();
$selected = $_GET['artist_id'] ?? 0;

?>
<div id="field-artist" class="mb-3 col-sm-6 col-md-3">
    <small><label class="form-label" for="filter-artist">Set</label><a href="javscript:void(0);" class="reset" data-reset="tags"
                                                                       style="display: none;"> - reset</a></small>
    <select class="form-control mc_select2 form-control-filter" id="filter-artist"
            name="artist">
        <option value="0"> -- Select a tag -- </option>
        <?php
        
        $sets = [];
        foreach( $tags as $tag ) :
            ?>
            <option value="<?= $tag->term_id ?>" <?= $selected == $tag->term_id ? 'selected' : '' ?>><?= $tag->name ?></option>
        <?php endforeach; ?>
    </select>

</div>