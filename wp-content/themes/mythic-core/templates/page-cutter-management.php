<?php
/**
 * Template Name: Admin - MC_Cutter Management
 */

use Mythic_Core\System\MC_Redirects;


if( !is_user_logged_in() || !MC_User_Functions::isAdmin() ) MC_Redirects::home();

$target = $_GET['target'];
$role   = $_GET['role'] ?? '';

get_header();
?>
    <div id="page" class="page">
        <div class="wing bg-white-wing"></div>
        <div class="container content bg-white-content">
            <h1>Mask MC_Cutter Management</h1>
            <div class="row align-items-start dashboard">
                <div class="sidebar col-sm-auto">
                    <a class="nav-item" href="/cutter-management?target=maps&role=list">Mask Maps</a>
                    <a class="nav-item" href="/cutter-management?target=elements&role=list">Elements</a>
                    <a class="nav-item" href="/cutter-management?target=preconfigurations&role=list">Preconfigurations</a>
                </div>
                <div class="col-sm">
                    <?php
                    ob_start();
                    MC_Render::templatePart( '/tools/cutter/management/'.$role.'/'.$target );
                    $output = ob_get_clean();

                    if( empty( $output ) ) : ?>
                        <p>Please select a management option to the left</p>
                    <?php
                    else : ?>
                        <?= $output ?><?php
                    endif; ?>
                </div>
            </div>

        </div>
        <div class="wing bg-white-wing"></div>
    </div>

<?php
get_footer();