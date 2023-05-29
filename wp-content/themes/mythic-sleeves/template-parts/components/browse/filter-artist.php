<?php

$artists = MC_User::get_active_creators();
$selected = $_GET['artist_id'] ?? 0;

?>
<div id="field-artist" class="mb-3 col-sm-6 col-md-3">
    <small><label class="form-label" for="filter-artist">Set</label><a href="javscript:void(0);" class="reset" data-reset="tags"
                                                                              style="display: none;"> - reset</a></small>
    <select class="form-control mc_select2 form-control-filter" id="filter-artist"
            name="artist">
            <option value="0"> -- Select an artist -- </option>
        <?php
        $sets = [];
        foreach( $artists as $artist ) :
        ?>
        <option value="<?= $artist->ID ?>" <?= $selected == $artist->ID ? 'selected' : '' ?>><?= $artist->display_name ?></option>
        <?php endforeach; ?>
    </select>

</div>