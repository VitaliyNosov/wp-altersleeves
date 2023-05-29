<?php
$coupon = $_GET['coupon'] ?? '';
if( empty($coupon) || !in_array($coupon, [ 'pastimeevents', 'gamermats', 'fullmoon']) || !is_front_page() ) return;

?>

<!-- Modal -->
<div class="modal fade show" id="promotionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document" style="max-width:900px">
        <div class="modal-content">
            <div class="modal-header">
                <div class="row justify-content-between align-items-center w-100">
                    <div class="col-sm-auto text-center text-sm-start">
                        <h3 class="modal-title">Gen-Con Savings</h3>
                    </div>
                    <div class="col-sm-auto text-center text-sm-end next-action" style="display:none;">
                        <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
            <div class="modal-body">
                <p class="fw-bold">Greetings from everyone here at Alter Sleeves, we hope you're enjoying Gen Con!</p>
                
                <p>If you're reading this, then you've activated a coupon that will <strong>save you 10%</strong> on your next purchase here at Alter Sleeves!</p>
                
                <p>Do remember that you can make savings on shipping by adding certain numbers of Alter Sleeves to your cart:</p>
                
                <ul>
                    <li><span class="fw-bold">5 Alter Sleeves</span> - free untracked shipping</li>
                    <li><span class="fw-bold">10 Alter Sleeves</span> - free tracked shipping*</li>
                </ul>
                
                <p>We're always happy to help so if you have any issues, just email support@altersleeves.com</p>
                
                <small>*<a href="https://www.altersleeves.com/countries-and-shipping-options">Check your country's eligibility for tracking here</a></small>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal">Start Shopping</button>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(window).on('load', function() {
        $('#promotionModal').modal('show');
    });
</script>