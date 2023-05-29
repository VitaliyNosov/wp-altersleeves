<?php

use Mythic_Core\System\MC_Nav;

?>

<nav id="header-nav" class="col-auto order-5 order-sm-3 ">

    <div class="row d-md-flex p-0 px-md-3" style="display:none;">
        
        <?php
        
        MC_Nav::displayNav( [
                                'theme_location'  => 'header',
                                'menu_class'      => 'nav',
                                'container'       => 'div',
                                'container_class' => 'col-md-auto order-2 order-md-1 p-0',
                            ] );
        
        MC_Nav::displayNav( [
                                'theme_location'  => 'user',
                                'menu_class'      => 'nav user-nav',
                                'container'       => 'div',
                                'container_class' => 'col-md-auto order-1 order-md-2 p-0',
                            ] );
        
        ?>

    </div>

</nav>

<div id="header-control-hamburger" class="d-md-none col-auto order-4 hvr-push">
    <div class="hamburger--3dx" tabindex="0" aria-label="Menu" role="button" aria-controls="navigation" aria-expanded="true/false">
        <div class="hamburger-box">
            <div class="hamburger-inner"></div>
        </div>
    </div>
</div>