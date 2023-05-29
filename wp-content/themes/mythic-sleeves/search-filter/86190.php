<?php

global $wp;

use Mythic_Core\Functions\MC_Alter_Functions;
use Mythic_Core\Objects\MC_Mtg_Printing;
use Mythic_Core\System\MC_WP;
use Mythic_Core\Utils\MC_Pagination;

$currentPage   = $query->paged;
$altersPerPage = $altersPerPagePaging = 10;
$showStatus    = MC_Alter_Functions::getStatusArray();
$viewingStatus = '';
if( isset( $_GET['status'] ) ) {
    $viewingStatus = MC_Alter_Functions::statusConverter( $_GET['status'] );
    $viewingStatus = '<p>You are currently viewing all your <strong>'.$viewingStatus.'</strong> alters';
    $showStatus    = [ $_GET['status'] ];
    $altersPerPage = $altersPerPagePaging = 20;
}
$idCreator = wp_get_current_user()->ID;
if( !isset( $_GET['_sf_s'] ) ) {
    $args = [
        'post_type'      => 'product',
        'post_status'    => $showStatus,
        'posts_per_page' => $altersPerPage,
        'tax_query'      => [
            'RELATION' => 'AND',
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'simple',
            ],
            [
                'taxonomy' => 'product_group',
                'field'    => 'slug',
                'terms'    => 'alter',
            ],
        ],
    ];
} else {
    $args            = [
        'post_type'      => 'product',
        'post_status'    => $showStatus,
        'posts_per_page' => $altersPerPage,
        'tax_query'      => [
            'RELATION' => 'AND',
            [
                'taxonomy' => 'product_type',
                'field'    => 'slug',
                'terms'    => 'simple',
            ],
            [
                'taxonomy' => 'product_group',
                'field'    => 'slug',
                'terms'    => 'alter',
            ],
        ],
    ];
    $search          = $_GET['_sf_s'];
    $argKey          = is_numeric( $search ) ? 'p' : 's';
    $args[ $argKey ] = $search;
}
if( !MC_User_Functions::isAdmin() ) $args['author'] = $idCreator;
if( isset( $_GET['sf_paged'] ) ) {
    $args['paged'] = $_GET['sf_paged'];
}

$query         = new WP_Query( $args );
$originalCount = $query->found_posts;
if( $query->have_posts() ) :
    global $wp;
    $url = home_url( $wp->request );
    
    $availableStatuses = MC_Alter_Functions::getStatusArray();
    $statuses          = [];
    
    foreach( $availableStatuses as $availableStatus ) {
        $status_query = [
            'author'         => $idCreator,
            'post_type'      => 'product',
            'posts_per_page' => -1,
            'post_status'    => [ $availableStatus ],
            'tax_query'      => [
                'RELATION' => 'AND',
                [
                    'taxonomy' => 'product_type',
                    'field'    => 'slug',
                    'terms'    => 'simple',
                ],
                [
                    'taxonomy' => 'product_group',
                    'field'    => 'slug',
                    'terms'    => 'alter',
                ],
            ],
            'fields'         => 'ids',
        ];
        $count        = count( get_posts( $status_query ) );
        if( !empty( $count ) ) $statuses[ $availableStatus ] = $count;
    }
    
    $statusCount = count( $statuses );
    $i           = 1;
    
    $output = '<p class="manage__statuses">';
    foreach( $statuses as $key => $status ) {
        $readableKey = MC_Alter_Functions::statusConverter( $key );
        $link        = '<a class="'.$key.'" href="'.$url.'?status='.$key.'">'.$readableKey.' ('.$status.')</a>';
        if( isset( $_GET['status'] ) ) {
            if( $_GET['status'] == $key ) {
                $link = '<strong>'.$link.'</strong>';
            }
        }
        $output .= $link.' | ';
        if( $i == $statusCount ) {
            $output .= '<strong><a href="'.$url.'">All Alters</a></strong>';
        }
        $i++;
    }
    $output .= '</p>';
    echo $output;
    echo $viewingStatus;
    
    MC_Pagination::searchAndFilter( $query, 12 );
    echo '<div class="manage__container">';
    while( $query->have_posts() ) :
        $query->the_post();
        $id         = get_the_ID();
        $linkedCard = MC_WP::meta( 'mc_linked_printing', $id );
        $nameDesign = MC_WP::meta( 'mc_design_name', $id );
        if( strlen( $linkedCard ) > 0 ) {
            $printing       = new MC_Mtg_Printing( $linkedCard );
            $linkedCardName = $printing->name;
            $linkedCardSet  = $printing->set_name;
            if( strlen( $nameDesign ) == 0 ) $nameDesign = $linkedCardName.' ('.$linkedCardSet.')';
        }
        $status         = get_post_status( $id );
        $readableStatus = MC_Alter_Functions::getSleeveStatus( $id );
        $readableStatus = $readableStatus == 'Flagged' ? $readableStatus.' <i class="fas fa-flag"></i>' : $readableStatus;
        $nameAlterist   = get_the_author();
        $originalAlter  = MC_WP::meta( 'mc_original_alter', $id );
        $image          = MC_WP::meta( 'mc_lo_res_combined_jpg', $id );
        ?>
        <div id="<?= $id; ?>" class="manage__item">
            <!-- Top Text -->
            <div class="manage__heading">
                <div class="manage__name">
                    <a href="<?= get_the_permalink( $id ) ?>"><?= $nameDesign; ?></a>
                </div>
                <div class="manage__status">
                    <strong>Status: <span class="<?= $status ?>"><?= $readableStatus; ?></span></strong><br>
                    <small><em>Submitted: <?= get_the_date() ?></em></small>
                </div>
            </div>
            <div class="manage__content row">
                <!-- Image -->
                <div class="manage__image col-4 col-sm-2">
                    <img class="sleeveable__corners" src="<?= $image ?>" alt="<?= $nameDesign ?> alter by <?= $nameAlterist ?>">
                </div>
                <!-- Info -->
                <div class="manage__info col-8 col-sm-3">
                    <?php
                    
                    $alterAssets = [];
                    
                    $alterAssets['id']        = [
                        'name'  => 'ID',
                        'value' => 'Alter: <a href="/dashboard/submit-alter?alter_id='.$id.'">'.$id.'</a>',
                    ];
                    $alterAssets['card_name'] = [
                        'name'  => 'Card Name',
                        'value' => $linkedCardName,
                    ];
                    $alterAssets['set_name']  = [
                        'name'  => 'Set Name',
                        'value' => $linkedCardSet,
                    ];
                    $output                   = '';
                    foreach( $alterAssets as $alterAsset ) {
                        $value  = $alterAsset['value'];
                        $output .= '<span class="manage__info--value manage__info--element">'.$value.'</span>';
                    }
                    echo $output;
                    ?>
                    <strong><a href="/files/prints/png/<?= $id ?>.png" target="_blank">Original file</a></strong>
                </div>
                <!-- Message -->
                <div class="manage__message col-sm-5">
                    <?php
                    $postStatus = get_post_status( $id );
                    
                    if( $postStatus == 'action' ) {
                        $violationText = MC_WP::meta( 'mc_violation_text', $id );
                        
                        $list = '<ul>';
                        
                        if( has_term( 3022, 'alter_status', $id ) ) {
                            $list .= '<li>Incorrect resolution: image should be 2980px by 4160px</li>';
                        }
                        
                        if( has_term( 3036, 'alter_status', $id ) ) {
                            $list .= '<li>Intellectual property/Copyright infringement</li>';
                        }
                        
                        if( has_term( 26204, 'alter_status', $id ) ) {
                            $list .= '<li>Incorrect crop type</li>';
                        }
                        
                        if( has_term( 3026, 'alter_status', $id ) ) {
                            $list .= '<li>You have cropped your design too tight</li>';
                        }
                        
                        if( has_term( 3023, 'alter_status', $id ) ) {
                            $list .= '<li>We think there are parts your meant to crop out but forgot to</li>';
                        }
                        
                        if( has_term( 3025, 'alter_status', $id ) ) {
                            $list .= '<li>You have used an opacity blending technique that doesn\'t translate to print</li>';
                        }
                        
                        if( has_term( 3024, 'alter_status', $id ) ) {
                            $list .= '<li>You have used pure white (RGB 255,255,255 or CMYK 0,0,0,0): this prints transparent. Please amend your design to reflect this</li>';
                        }
                        
                        if( has_term( 3039, 'alter_status', $id ) ) {
                            $list .= '<li>We have found some unwanted elements while printing: please check your original file for forgotten low opacity elements as these can print as hard white</li>';
                        }
                        $list .= '</ul>';
                        if( strlen( $list ) > 20 ) {
                            $message = '<div class="manage__message--border"><p>Unfortunately your design seems to be suffering from the following issues:</p>'.$list;
                        } else {
                            $message = '<div class="manage__message--border"><p>Unfortunately your design seems to be suffering from some printing issues; we will update you when we can</p>';
                        }
                        
                        if( strlen( $violationText ) > 0 ) {
                            $message .= '<p>We also have the following message for you:<br>'.$violationText.'</p>';
                        }
                        $message .= '</div>';
                        echo $message;
                    }
                    ?>
                </div>
                <!-- Buttons -->
                <div class="manage__buttons col-sm-2">
                    <?php
                    $status = get_post_status( $id );
                    
                    $output = '';
                    // @Todo remove edit
                    $buttons = [ 'edit' ];
                    
                    foreach( $buttons as $button ) {
                        $url = home_url( $wp->request );
                        switch( $button ) {
                            case 'configure' :
                                $url = '/upgrade-alter?alter_id='.$id;
                                break;
                            case 'view' :
                                $url = get_the_permalink( $id );
                                break;
                            case 'edit' :
                                $url = '/submit-alter?alter_id='.$id;
                                break;
                        }
                        
                        $output .= '<div class="manage__button">';
                        if( $url != '' ) $output .= '<a href="'.$url.'">';
                        $output .= '<button data-alter-id="'.$id.'" class="manage__'.$button.'">'.$button.'</button>';
                        if( $url != '' ) $output .= '</a>';
                        $output .= '</div>';
                    }
                    $output .= '<div class="manage__button"><button data-delete-id="'.$id.'" class="manage__delete">Delete</button></div>';
                    echo $output;
                    ?>
                </div>
            </div>
        </div>
        <?php
        Mythic_Core\Ajax\TrashDesign::render_nonce();
    endwhile;
    echo '</div>';
    ?><?php
    MC_Pagination::searchAndFilter( $query, 12 );

else :
    echo "<p>Sorry, but you haven't submitted an alter that meets criteria (or any at all)</p>";
    echo "<p>You can submit one <a href='/dashboard/alterist/upload-alter'><strong>here</strong></a></p>";
endif;
