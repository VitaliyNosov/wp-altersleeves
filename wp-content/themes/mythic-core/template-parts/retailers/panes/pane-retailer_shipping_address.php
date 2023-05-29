<div class="accordion-item">
    <h2 class="accordion-header" id="headingOne">
        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne"
                aria-expanded="true" aria-controls="collapseOne">
            Store Address
        </button>
    </h2>
    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
         data-bs-parent="#accordionExample">
        <div class="accordion-body">
            <?php woocommerce_account_edit_address( 'shipping' ); ?>
        </div>
    </div>
</div>