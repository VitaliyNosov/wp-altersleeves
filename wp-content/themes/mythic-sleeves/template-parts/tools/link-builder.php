<?php

if( !MC_User_Functions::isAdmin() ) return;

$url = ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? "https" : "http" )."://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";

?>

<div class="campaign-url-builder bg-white px-3 border">
    <div class="position-relative container py-2">
        <div class="open-campaign-url-builder"><i class="fa fa-minus"></i></div>
        <div class="close-campaign-url-builder" style="display: none;"><i class="fa fa-times"></i></div>
        <div class="instructions"><h2>Open Link builder</h2></div>
        <div class="tool-wrapper" style="display:none;">
            <input id="input-campaign-original-url" type="hidden" value="<?= $url ?>">
            <div class="row align-items-end">
                <div class="col-sm mb-3 mb-sm-0">
                    <label class="form-label" for="input-campaign-source">Source</label>
                    <select class="form-control" id="input-campaign-source">
                        <option value="internal">N/A</option>
                        <option value="edhrec">EDHRec</option>
                        <option value="command_beacon">Command Beacon</option>
                        <option value="google">Google</option>
                        <option value="google_adwords">Google Adwords</option>
                        <option value="google_remarketing">Google Remarketing</option>
                        <option value="facebook">Facebook</option>
                        <option value="instagram">Instagram</option>
                        <option value="twitter">Twitter</option>
                        <option value="sendinblue">Sendinblue</option>
                    </select>
                </div>
                <div class="col-sm mb-3 mb-sm-0">
                    <label class="form-label" for="input-campaign-medium">Medium</label>
                    <select class="form-control" id="input-campaign-medium">
                        <option value="internal">N/A</option>
                        <!-- <option value="affiliate">Affiliates</option> -->
                        <option value="display">Display</option>
                        <option value="email">Email</option>
                        <option value="organic"> Organic Search</option>
                        <option value="paidsearch">Paid Search</option>
                        <option value="social">Social</option>
                        <option value="partner">Partners</option>
                        <option value="referral">Referral</option>
                    </select>
                </div>
                <div class="col-sm mb-3 mb-sm-0">
                    <label class="form-label" for="input-campaign-name">Campaign Name</label>
                    <input id="input-campaign-name" type="text" class="form-control" placeholder="Product, promo code, or slogan (e.g. spring_sale)">
                </div>
                <div class="col-sm">
                    <button id="button-build-campaign-url" type="button" class="btn btn-primary w-100 m-0">Build URL</button>
                    <?php Mythic_Core\Ajax\Marketing\MG_Shorten_Link::render_nonce(); ?>
                    <div class="text-copied text-success" style="display:none;">Text copied</div>
                </div>
            </div>
            <div class="campaign-url my-2" style="font-size:13px;">
                <?= $url ?>
            </div>
            <div id="short-url" class="short-url my-2" style="font-size:13px;font-weight: bold;"></div>
            <input id="input-campaign-built-url" class="offscreen" type="text" value="">
            <input id="input-short-url" class="offscreen" type="text" value="">
        </div>
    </div>
</div>

<style>
    .campaign-url-builder {
        width: 100%;
        position: fixed;
        bottom: 0;
        left: 0;
        z-index: 999;
    }

    .campaign-url-builder label {
        font-weight: bold;
    }

    .open-campaign-url-builder,
    .close-campaign-url-builder {
        position: absolute;
        top: 0;
        right: 10px;
        color: red;
    }

    .offscreen {
        position: absolute;
        left: -9999px;
    }

    .close-campaign-url-builder:hover,
    .close-campaign-url-builder:hover {
        cursor: pointer;
    }
</style>