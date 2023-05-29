<?php

global $wp;
$url = home_url( $wp->request );

$creatorDiscord = '';
if( ( MC_User_Functions::isArtist() && basename( $url ) == 'dashboard' ) || MC_User_Functions::isAdmin() ) {
    $creatorDiscord = '<br><p><strong>As an alterist, please join our alterist Discord server: <a href="https://discord.gg/4KHwZhn">https://discord.gg/4KHwZhn</a></strong></p>';
}

?>

<div class="col content bg-white-content">
    <?php
    if( have_posts() ) :
        while( have_posts() ) : the_post();
            
            the_title( '<h1>', '</h1>' );
            
            the_content();
        
        endwhile;
    endif;
    ?>
    
    <?php
    $creatorDiscord = '';
    if( ( MC_User_Functions::isArtist() && basename( $url ) == 'dashboard' ) && empty(get_user_meta( MC_User::id(), 'discord_username', true ) ) ) {
        $creatorDiscord = '<hr><br><p><strong>As an alterist, please join our alterist Discord server: <a class=nav-item href="https://discord.gg/4KHwZhn">https://discord.gg/4KHwZhn</a></strong></p>';
    }
    echo $creatorDiscord;
    ?>
</div>