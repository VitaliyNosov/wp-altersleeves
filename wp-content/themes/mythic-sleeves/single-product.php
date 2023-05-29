<?php

use Mythic_Core\System\MC_WP;

get_header();

$idProduct   = get_queried_object()->ID;
$printing_id = $_GET['printing_id'] ?? false;
if( !empty( $printing_id ) ) {
    $pathToUse = 'product_page_'.$idProduct.'_'.$printing_id;
} else {
    $pathToUse = 'product_page_'.$idProduct;
}
if( !MC_User_Functions::isArtist() && !MC_User::isContentCreator() &&
    !empty( get_transient( $pathToUse ) ) && empty( $_COOKIE['coupon'] ) ) {
    $output = get_transient( $pathToUse );
} else {
    $idUser     = is_user_logged_in() ? wp_get_current_user()->ID : 0;
    $idAlterist = MC_WP::authorId( $idProduct );
    ob_start();
    include( DIR_THEME_TEMPLATE_PARTS.'/store/product/layout.php' );
    $output = ob_get_clean();
    if( !MC_User_Functions::isAdmin() ) set_transient( $pathToUse, $output, 24 * HOUR_IN_SECONDS );
}

echo $output;

get_footer();

