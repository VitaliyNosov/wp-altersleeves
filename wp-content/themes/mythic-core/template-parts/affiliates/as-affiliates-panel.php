<?php

use Mythic_Core\Display\MC_Render;


if(
    !MC_User_Functions::isAdmin() ||
    empty( $_GET['affiliate_id'] ) ||
    empty( $current_affiliate = get_user_by( 'ID', $_GET['affiliate_id'] ) )
) {
    return;
}
?>
<div class="mc-affiliates-control-container">
    <ul class="nav nav-tabs mc-af-header">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#finance">Finance</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#affiliate_report">Affiliate Hub</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#licensed_designs">Licensed Designs</a>
        </li>
    </ul>
    <div class="tab-content mc-af-content">
        <div class="tab-pane fade show active" id="finance">
            <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-panel', 'finance', [ 'current_affiliate' => $current_affiliate ] ); ?>
        </div>
        <div class="tab-pane fade" id="affiliate_report">
            <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-panel', 'affiliate-report',
                                           [ 'current_affiliate' => $current_affiliate ] ); ?>
        </div>
        <div class="tab-pane fade" id="licensed_designs">
            <?php MC_Render::templatePart( 'affiliates/parts/as-affiliates-panel', 'licensed-designs',
                                           [ 'current_affiliate' => $current_affiliate ] ); ?>
        </div>
    </div>
</div>
