<?php

global $current_user;

use Mythic_Core\Ajax\Acceptance\MC_Progress_Alter;
use Mythic_Core\Ajax\MC_Store_Webcam_Image;

$args = [
    'post_type'      => 'product',
    'posts_per_page' => 100,
    'post_status'    => [ 'verify', 'internal_verify' ],
    'author__not_in' => [ 1341 ],
    'orderby'        => 'ID',
    'order'          => 'ASC',
    'fields'         => 'ids'
];
if( $current_user->ID != 1 ) $args['meta_query'] =
    [
        'key'     => 'mc_lo_res_combined_jpg',
        'compare' => '!=',
        'value'   => ''
    ];
$alters = get_posts( $args );
$count  = count( $alters );
?>

<form class="form-inline display-flex">
    <div class="mb-3 mb-2 pr-5 ">
        <label class="form-label" for="input-approval-count">Total to send</label>
        <input type="text" class="form-control" id="input-approval-count" value="100">
    </div>
    <button id="action-send-approvals" class="red--button my-0">SEND APPROVALS</button>
    <?php Mythic_Core\Ajax\Acceptance\MC_Send_Approval_Files::render_nonce(); ?>
</form>
<div class="acceptance row position-relative justify-content-start">
    <div class="approvals col-sm-8">
        <?php
        foreach( $alters as $idAlter ) :
            
            $argsAlterTypes = [ 'taxonomy' => 'alter_type', 'hide_empty' => false ];
            $objectsAlterTypes = get_terms( $argsAlterTypes );
            $url = get_the_permalink( $idAlter );
            $img = MC_Alter_Functions::image( $idAlter );
            $printing_id = MC_Alter_Functions::printing( $idAlter );
            $printing = new MC_Mtg_Printing( $printing_id );
            $imgPrinting = $printing->imgJpgNormal;
            $imgCombined = MC_Alter_Functions::getCombinedImage( $idAlter );
            $fileIndicator = 'success';
            $nameAlterist = MC_Alter_Functions::getAlteristDisplayName( $idAlter );
            $profile = MC_Alter_Functions::getAlteristProfileUrl( $idAlter );
            $webcamRequired = true;
            $issues = wp_get_object_terms( $idAlter, 'alter_status' );
            
            $webcamRequired = false;
            if( empty( $issues ) ) {
                $webcamRequired = true;
            } else {
                $softPulls = [ 26206, 26205, 26306, 26204, 26203 ];
                foreach( $issues as $objectIssue ) {
                    $idIssue = is_object( $objectIssue ) ? $objectIssue->term_id : $objectIssue;
                    if( !is_numeric( $idIssue ) ) continue;
                    if( in_array( $idIssue, $softPulls ) ) continue;
                    $webcamRequired = true;
                    break;
                }
            }
            $logs = MC_Log::getByDetails( [ 'item_id' => $idAlter ] );
            foreach( $logs as $key => $log ) if( $log->category == 101 ) unset( $logs[ $key ] );
            
            $idDesign = MC_Alter_Functions::design( $idAlter );
            $types    = wp_get_object_terms( $idDesign, 'alter_type' );
            if( !empty( $types ) && is_array( $types ) ) $selected = $types[0]->term_id;
            if( !empty( $idAlter ) && empty( $types ) ) {
                $types = wp_get_object_terms( $idAlter, 'alter_type' );
                if( !empty( $types ) && is_array( $types ) ) {
                    $selected = $types[0]->term_id;
                }
            }
            $design_type      = MC_Alter_Functions::design_type( $idDesign, 'name' );
            $generic_option   = MC_Alter_Functions::design_potentiallyGeneric( $idDesign );
            $is_generic       = get_post_meta( $idDesign, 'mc_generic', true );
            $status           = get_post_status( $idAlter );
            $internal_checked = strpos( $status, 'internal' ) !== false ? ' checked' : '';
            ?>
            <div id="approval-<?= $idAlter ?>" class="approval p-2" style="margin-top:2rem;margin-bottom: 2rem;">
                <div>
                    <h3><a href="<?= $url ?>"><?= $idAlter ?></a></h3>
                    <strong>Alterist: </strong> <?= $nameAlterist ?>
                    <div class="form-check py-2">
                        <input type="checkbox" class="form-check-input product-internal-selector"
                               id="internal-<?= $idAlter ?>" <?= $internal_checked ?>
                               data-id="<?= $idAlter ?>">
                        <label class="form-check-label" for="internal-<?= $idAlter ?>">Internal Only (Status on load = <?= $status ?>)</label>
                        <?php Mythic_Core\Ajax\Acceptance\MC_Product_Internal::render_nonce(); ?>
                    </div>
                    <div class="cas-product-info__data my-3">
                        <form action="javascript:void(0);">
                            <div class="mb-3">
                                <select id="input-mod-alter-type" class="form-select form-select-md" name="mod_alter_type"
                                        data-design="<?= $idDesign ?>">
                                    <option value="0" selected>--- Please Select ---</option>
                                    <?php
                                    foreach( $objectsAlterTypes as $objectAlterType ) :
                                        $idAlterType = $objectAlterType->term_id;
                                        $nameAlterType = $objectAlterType->name;
                                        if( is_numeric( $nameAlterType ) ) continue;
                                        ?>
                                        <option value="<?= $idAlterType ?>"
                                            <?php if( $idAlterType == $selected && !empty( $idDesign ) ) echo 'selected'; ?>><?= $nameAlterType ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <?php if( $generic_option ) : ?>
                                <div class="form-check my-0">
                                    <input type="checkbox" class="form-check-input" id="input-mod-generic" name="mod_generic"
                                           data-design="<?= $idDesign ?>" <?php if( $is_generic ) echo 'checked'; ?>>
                                    <label class="form-check-label" for="mod_generic">Generic Art Replacement</label>
                                </div>
                            <?php endif; ?>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-4">
                        <h5><a href="/files/prints/png/<?= $idAlter ?>.png">Alter File</a></h5>
                        <div class="review-item__image-float review-item__image-float--green text-center p-2">
                            <img class="img-fluid" src="<?= $img ?>" style="max-width:200px;">
                        </div>
                    </div>
                    <div class="col-4 p-2 text-center">
                        <h5>Selected Printing</h5>
                        <img class="img-fluid card-display" src="<?= $imgPrinting ?>" style="max-width:200px;">
                    </div>
                    <div class="col-4 p-2 text-center">
                        <h5>Combined Image</h5>
                        <img class="img-fluid card-display" src="<?= $imgCombined ?>" style="max-width:200px;">
                    </div>
                </div>
                <br>
                <div class="flagging-options row">
                    <div class="col">
                        <h3>Flagging options</h3>
                        <?php
                        include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/alter/options-flag-production.php' ); ?>
                    </div>
                    <div class="col">
                        <?php include( DIR_THEME_TEMPLATE_PARTS.'/acceptance/message.php' ); ?>
                    </div>
                </div>
                <hr>
                <div class="d-inline-block">
                    <button id="capture-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-capture
                    " data-version="1">
                        Capture Image
                    </button>
                    <button id="approve-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-approve cas-button--green">
                        Approve
                    </button>
                    <button id="reject-<?= $idAlter ?>" data-alter-id="<?= $idAlter ?>" class="action-reject cas-button--red">
                        Reject
                    </button>
                    
                    <?php
                    
                    MC_Progress_Alter::render_nonce();
                    MC_Store_Webcam_Image::render_nonce();
                    ?>

                </div>
                
                <?php if( !empty( $logs ) ) : ?>
                    <h2>Reported Issues</h2>
                    <div id="accordion">
                        <?php
                        $i = 0;
                        foreach( $logs as $log ) :
                            $date = $log->date;
                            ?>
                            <div class="card">
                                <div class="card-header p-1" id="heading<?= $idAlter.$i ?>">
                                    <h5 class="mb-0">
                                        <button class="btn btn-link" data-bs-toggle="collapse" data-bs-target="#collapse<?= $idAlter.$i ?>"
                                                aria-expanded="true"
                                                aria-controls="collapse<?= $idAlter.$i ?>">
                                            <?= $date ?>
                                        </button>
                                    </h5>
                                </div>

                                <div id="collapse<?= $idAlter.$i ?>" class="collapse" aria-labelledby="heading<?= $idAlter.$i ?>"
                                     data-parent="#accordion">
                                    <div class="card-body">
                                        <?= $log->message ?>
                                    </div>
                                </div>
                            </div>
                            <?php
                            $i++;
                        endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        <?php
        endforeach; ?>
    </div>
    <div class="acceptance-tools col-sm-4" style="position:fixed; right:15px;top:140px;">
        <div class="count"><strong>To approve:</strong> <span id="count-approvals"><?= $count ?></span></div>
        <div class="camera affix py-3">
            <div class="camera-feed" style="width:100%;"></div>
            <div class="camera-result">
                <div class="loading" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .approval:nth-child(even) {
        background: #f6f6f6;
        padding-top: 1rem;
        padding-bottom: 1rem;
    }

    .camera-result img {
        width: 100%;
        max-width: 320px;
    }
</style>