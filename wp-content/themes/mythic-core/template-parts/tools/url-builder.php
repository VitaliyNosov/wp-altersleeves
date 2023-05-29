<?php

use Mythic_Core\Utils\MC_Url;

if( !MC_User_Functions::isAdmin() ) return;

$url = MC_Url::current();

?>

<input id="input-campaign-original-url" type="hidden" value="<?= $url ?>">
<div class="row align-items-end">
    <div class="col-sm mb-3 mb-sm-0">
        <label for="input-campaign-source">Source</label>
        <select class="form-control" id="input-campaign-source">
            <option value="internal">N/A</option>
            <option value="edhrec">EDHRec</option>
            <option value="command_beacon">Command Beacon</option>
            <option value="google">Google</option>
            <option value="google_adwords">Google Adwords</option>
            <option value="google_remarketing">Google Remarketing</option>
            <option value="facebook">Facebook</option>
            <option value="reddit">Reddit</option>
            <option value="instagram">Instagram</option>
            <option value="twitter">Twitter</option>
            <option value="sendinblue">Sendinblue</option>
        </select>
    </div>
    <div class="col-sm mb-3 mb-sm-0">
        <label for="input-campaign-medium">Medium</label>
        <select class="form-control" id="input-campaign-medium">
            <option value="internal">N/A</option>
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
        <label for="input-campaign-name">Campaign Name</label>
        <input id="input-campaign-name" type="text" class="form-control" placeholder="Product, promo code, or slogan (e.g. spring_sale)">
    </div>
    <div class="col-sm">
        <button id="button-build-campaign-url" type="button" class="btn btn-primary w-100 m-0">Build URL</button>
        <div class="text-copied text-success" style="display:none;">Text copied</div>
    </div>
</div>
<div class="campaign-url my-2" style="font-size:13px;">
    <?= $url ?>
</div>
<div id="short-url" class="short-url my-2" style="font-size:13px;font-weight: bold;"></div>
<label for="input-campaign-built-url" class="sr-only">Built URL</label>
<input id="input-campaign-built-url" class="offscreen" type="text" value="">
<label for="input-short-url" class="sr-only">Short URL</label>
<input id="input-short-url" class="offscreen" type="text" value="">