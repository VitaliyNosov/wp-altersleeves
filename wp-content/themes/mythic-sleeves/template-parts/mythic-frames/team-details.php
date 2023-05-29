<?php

return;

$alterist_img        = $team['alterist_image'] ?? MC_URI_IMG.'/user/profile.png';
$content_creator_img = $team['content_creator_image'] ?? MC_URI_IMG.'/user/profile.png';

?>

<div class="row my-3 align-items-start">
    <div class="col-6 col-sm">
        <h4>Alterist</h4>
        <img src="<?= $alterist_img ?>" alt="Alterist image">
    </div>
    <div class="col-6 col-sm">
        <h4>Content Creator</h4>
        <img src="<?= $content_creator_img ?>" alt="Alterist image">
    </div>
    <div class="col-sm-6">
        <h4>Team Description</h4>
        <p><?= $team['description'] ?? '' ?>
        </p>
    </div>
</div>
