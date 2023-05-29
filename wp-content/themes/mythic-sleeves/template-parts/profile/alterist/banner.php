<?php

if( ( MC_User_Functions::isArtist() && !MC_User_Functions::isAdmin() ) || !is_user_logged_in() ) return;
?>
<div class="bg-blue">
    <div class="container">
        <div class="row">
            <div class="text-center text-sm-start col-sm-auto text">
                Want to contribute alters of your own?<br>
                Find out more today
            </div>
            <div class="col-sm-auto text-center text-sm-start">
                <a href="/alterist-info" title="Click here to find out more about becoming an alterist">
                    <button class="cas-button--pink cas-button">Click here</button>
                </a>
            </div>
        </div>
    </div>
</div>