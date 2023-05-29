<?php

namespace Mythic_Core\Shortcodes;

use MC_Render;
use MC_Forms;
use MC_User_Functions;
use Mythic_Core\Abstracts\MC_Shortcode;
use Mythic_Core\Functions\MC_Creator_Functions;

class MC_Creator_Hub extends MC_Shortcode {
    
    /**
     * @return string
     */
    public function getShortcode() : string {
        return 'mc_creator_hub';
    }
    
    public function generate( $args = [], $content = '' ) : string {
        $content_creator_id = MC_Creator_Functions::get_current_creator_id();
        
        if( !$_GET['tab'] ) {
            $_GET['tab'] = 'nav-home';
        }
        
        ob_start(); ?>

        <p>Hello <?= get_user_by( 'id', $content_creator_id )->display_name ?>! Thanks for being part of the Alter Sleeves family. Below
            you can withdraw and view an itemized statement of your activity, including referral orders.</p>

        <nav>
            <div class="nav nav-tabs" id="nav-tab" role="tablist">

                <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-home' ? 'active' : '' ?>" id="nav-home-tab" data-bs-toggle="tab"
                   href="#nav-home" role="tab"
                   aria-controls="nav-home" aria-selected="true">Balances</a>

                <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-report' ? 'active' : '' ?>" id="nav-report-tab" data-bs-toggle="tab"
                   href="#nav-report" role="tab"
                   aria-controls="nav-report" aria-selected="false">Statement</a>
                
                <?php if( !empty( MC_Creator_Functions::has_sales( $content_creator_id ) ) ) : ?>
                    <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-sales' ? 'active' : '' ?>" id="nav-sales-tab" data-bs-toggle="tab"
                       href="#nav-sales" role="tab"
                       aria-controls="nav-report" aria-selected="false">Sales</a>
                <?php endif; ?>
                <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-publishing' ? 'active' : '' ?>" id="nav-publishing-tab"
                   data-bs-toggle="tab" href="#nav-publishing" role="tab"
                   aria-controls="nav-publishing" aria-selected="false">Publishing</a>
                
                <?php if( MC_User_Functions::isAdmin() || MC_User_Functions::isRetailer() ) : ?>
                    <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-retail' ? 'active' : '' ?>" id="nav-retail-tab" data-bs-toggle="tab"
                       href="#nav-retail" role="tab"
                       aria-controls="nav-publishing" aria-selected="false">Retail</a>
                <?php endif; ?>
                <?php if( MC_User_Functions::isAdmin() || MC_User_Functions::isContentCreator() ) : ?>
                    <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-promotions' ? 'active' : '' ?>" id="nav-promotions-tab"
                       data-bs-toggle="tab" href="#nav-promotions" role="tab"
                       aria-controls="nav-promotions" aria-selected="false">Promotions</a>
                <?php endif; ?>

                <a class="nav-item nav-link <?php echo $_GET['tab'] == 'nav-charity' ? 'active' : '' ?>" id="nav-charity-tab" data-bs-toggle="tab"
                   href="#nav-charity" role="tab"
                   aria-controls="nav-publishing" aria-selected="false">Charity</a>
            </div>
        </nav>

        <div class="tab-content" id="nav-tabContent">
            <!-- Monthly Report -->
            <div class="tab-pane fade show py-3 <?php echo $_GET['tab'] == 'nav-home' ? 'active show' : '' ?>" id="nav-home" role="tabpanel"
                 aria-labelledby="nav-home-tab">
                <?php MC_Render::templatePart( 'finance', 'withdrawal-request' ); ?>
            </div>

            <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-report' ? 'active show' : '' ?>" id="nav-report" role="tabpanel"
                 aria-labelledby="nav-report-tab">
                <?php MC_Render::templatePart( 'reporting', 'ledger-affiliate' ); ?>
            </div>
            <?php if( MC_Creator_Functions::has_sales( $content_creator_id ) ) : ?>
                <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-sales' ? 'active show' : '' ?>" id="nav-sales" role="tabpanel"
                     aria-labelledby="nav-sales-tab">
                    <?php MC_Render::templatePart( 'creator/reports', 'sales' ); ?>
                </div>
            <?php endif; ?>
            <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-publishing' ? 'active show' : '' ?>" id="nav-publishing" role="tabpanel"
                 aria-labelledby="nav-publishing-tab">
                <h2>Publishing 123</h2>
                <p>This is the area for managing your publishing offers and agreements.</p>
                <?= do_shortcode( '[mc_rights_sharing_control]' ); ?>
            </div>
            
            <?php if( MC_User_Functions::isContentCreator() ) : ?>
                <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-charity' ? 'active show' : '' ?>" id="nav-charity" role="tabpanel"
                     aria-labelledby="nav-charity-tab">

                    <h2>Charity</h2>
                    <?php
                    $form_classes = [ 'charity' ];
                    
                    $fields = [
                        /*
                         *
                         * // Disabled for now - doesn't provide value and causes confusion
                    [
                       'label'    => 'Enable charity',
                       'name'     => 'charity_status',
                       'type'     => 'checkbox',
                       'value'    => 1,
                       'id_part'  => 'charity_status',
                       'required' => 0,
                    ],
                        */
                        [
                            'label'    => 'Charity name',
                            'name'     => 'charity_name',
                            'type'     => 'text',
                            'id_part'  => 'charity_name',
                            'required' => 1,
                        ],
                        [
                            'label'    => 'Charity URL',
                            'name'     => 'charity_url',
                            'type'     => 'text',
                            'id_part'  => 'charity_url',
                            'required' => 1,
                        ],
                        /*
                    [
                       'label'    => 'Charity image (link)',
                       'name'     => 'charity_image',
                       'type'     => 'text',
                       'id_part'  => 'charity_image',
                       'required' => 0,
                    ],
                        */
                        [
                            'label'    => 'Why you’ve chosen this charity<br>(180 chars max)',
                            'name'     => 'charity_reason',
                            'type'     => 'textarea',
                            'id_part'  => 'charity_reason',
                            'required' => 1,
                        ],
                    ];
                    
                    $hidden_fields = [
                        [
                            'mc_nonce' => 'mc_charity_data',
                        ],
                    ];
                    
                    $user_id = get_current_user_id();
                    if( $_GET['affiliate_id'] && current_user_can( 'administrator' ) ) {
                        $user_id = $_GET['affiliate_id'];
                    }
                    
                    MC_Forms::mcFormRender( $form_classes, $fields, 'Submit', $hidden_fields, [
                        //'charity_status'    => get_user_meta( get_current_user_id(), 'mc_charity_status', true ),
                        'charity_name'   => get_user_meta( $user_id, 'mc_charity_name', true ),
                        'charity_url'    => get_user_meta( $user_id, 'mc_charity_url', true ),
                        'charity_image'  => get_user_meta( $user_id, 'mc_charity_image', true ),
                        'charity_reason' => get_user_meta( $user_id, 'mc_charity_reason', true ),
                    ] );
                    ?>
                </div>
            <?php endif; ?>
            
            <?php if( MC_User_Functions::isContentCreator() ) : ?>
                <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-promotions' ? 'active show' : '' ?>" id="nav-promotions"
                     role="tabpanel" aria-labelledby="nav-promotions-tab">
                    <h2>Promotions</h2>
                    <?php MC_Render::templatePart( 'affiliates/panes/pane', 'promotions_by_affiliate',
                                                   [ 'args' => MC_Creator_Functions::get_args_for_promotion_panels() ] ); ?>
                </div>
            <?php endif; ?>
            
            <?php if( MC_User_Functions::isRetailer() ) : ?>
                <div class="tab-pane fade py-3 <?php echo $_GET['tab'] == 'nav-retail' ? 'active show' : '' ?>" id="nav-retail" role="tabpanel"
                     aria-labelledby="nav-retail-tab">
                    <div class="accordion" id="accordionExample">

                        <h2>Orders to store</h2>
                        <?php MC_Render::templatePart( 'retailers/panes/pane', 'orders_by_retailer',
                                                       [ 'args' => MC_Creator_Functions::get_args_for_retailer_order_panels() ] ); ?>
                        
                        <?php MC_Render::templatePart( 'retailers/panes/pane', 'retailer_shipping_address' ); ?>

                    </div>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
    
}