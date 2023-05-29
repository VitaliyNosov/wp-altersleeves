<?php

$alterist     = get_queried_object();
$idAlterist   = $alterist->ID;
$nameAlterist = $alterist->display_name;
?>
    <br>
    <div class="header d-md-none">
        <h1><?= $nameAlterist ?></h1>
    </div>
    <div class="info row align-items-start">
        <div class="details col-md-4">
            <?php include DIR_THEME_TEMPLATE_PARTS.'/profile/alterist/details.php'; ?>
        </div>
        <div class="col-md-8 info">
            <h1 class="d-none d-md-block"><?= $nameAlterist ?></h1>
            <?php
            include DIR_THEME_TEMPLATE_PARTS.'/profile/alterist/description.php';
            include DIR_THEME_TEMPLATE_PARTS.'/profile/alterist/bestselling.php';
            ?>
        </div>
    </div>
<?php
if( MC_User::isContentCreator($idAlterist) ) {
    include DIR_THEME_TEMPLATE_PARTS.'/profile/content-creator/favs.php';
    
}
ob_start();
include DIR_THEME_TEMPLATE_PARTS.'/profile/alterist/collections.php';
include DIR_THEME_TEMPLATE_PARTS.'/profile/alterist/designs.php';
$idAlterist = $alterist->ID;


$portfolio = ob_get_clean();
if( !empty( $portfolio ) ) :
    ?>
    <div class="row">
        <div class="bg-white">
            <?= $portfolio ?>
        </div>
    </div>

<?php
endif;
if( !MC_User::isContentCreator($idAlterist) ) return;
MC_Render::templatePart( 'profile/alterists' );
MC_Render::templatePart( 'profile/bestselling' );
