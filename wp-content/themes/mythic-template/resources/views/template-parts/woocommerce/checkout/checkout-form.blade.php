<form action="#" class="checkout-form">
    @include('template-parts.woocommerce.checkout.checkout-form-user')

    @include('template-parts.woocommerce.checkout.checkout-form-shipping-address')

    @include('template-parts.woocommerce.checkout.checkout-form-shipping-method')

    @include('template-parts.woocommerce.checkout.checkout-form-payment-method')

    @include('template-parts.woocommerce.checkout.checkout-form-comments')
</form>
