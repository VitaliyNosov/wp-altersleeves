<?php

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_User;

if( !MC_User::authorCurrentObject() && !MC_User_Functions::isAdmin() ) return;

if( !isset( $idAlter ) ) return;
if( $idAlter != get_queried_object()->ID ) $idAlter = get_queried_object()->ID;
$idDesign   = MC_Alter_Functions::design( $idAlter );
$typeAlter  = MC_Alter_Functions::type( $idAlter );
$typeDesign = MC_Alter_Functions::design_type( $idDesign );
$alters     = MC_Alter_Functions::design_alters( $idDesign );

if( count( $alters ) < 2 ) return;

?>
<hr>
<h3 class="mb-3">Alters in Design</h3>
<div class="row" style="list-style: none;">
    <?php foreach( $alters as $alter ) :
        $image = MC_Alter_Functions::getCombinedImage( $alter );
        ?>
        <div class="col-4 col-sm-2"><a href="<?= get_the_permalink( $alter ) ?>">
                <img class="card-display my-2" src="<?= $image ?>" style="max-width:100px;"><br>
                <?= $alter ?>
            </a>
        </div>
    <?php endforeach; ?>
</div>
