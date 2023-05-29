<div class="checkout-form-section payment-section">
    <h3 class="section-title">Payment method</h3>

    <div class="row payment-select-row">
        <div class="col-xl-6 payment-col">
            <div class="form-check">
                <label class="form-check-label" for="payment_method_card">
                    <input class="form-check-input" type="checkbox" name="payment_method_card" id="payment_method_card">
                    <span class="form-check-control"></span>
                    <label class="form-check-label" for="payment_method_card">
                        <i class="icon-card"></i>
                        Credit Card
                    </label>
                </label>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <input type="text" class="form-control" id="credit_card_owner" name="credit_card_owner"
                           placeholder="Owner*">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="credit_card_number" name="credit_card_number"
                           placeholder="Card Number*">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="credit_card_expire" name="credit_card_expire"
                           placeholder="Expiry Date*">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" id="credit_card_cvv" name="credit_card_cvv"
                           placeholder="CVC/CVV*">
                </div>
                <div class="col">
                    <div class="form-control-hint dark">Payments are secure and encrypted</div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 payment-col">
            <div class="form-check">
                <label class="form-check-label" for="payment_method_paypal">
                    <input class="form-check-input" type="checkbox" name="payment_method_paypal"
                           id="payment_method_paypal">
                    <span class="form-check-control"></span>
                    <label class="form-check-label" for="payment_method_paypal">
                        <i class="icon-paypal"></i>
                        Pay Pal
                    </label>
                </label>
            </div>
            <div class="row align-items-center h-50">
                <div class="col-12">
                    <div class="form-control-hint">Direct payment with your PayPal account.</div>
                </div>
            </div>
        </div>
    </div>
</div>
