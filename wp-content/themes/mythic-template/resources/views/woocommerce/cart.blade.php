@extends('layouts.app')

@section('content')
    <main class="site-content cart-page-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Category', 'Product']])

        <div class="d-md-none mobile-proceed-checkout">
            <div class="total">
                $25
            </div>
            <button class="button button-primary-gradient proceed-to-checkout-submit">Checkout</button>
        </div>

        @include('template-parts.promotions.banner')

        <div class="cart-body-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="cart-products">
                            <h1 class="page-title">My cart</h1>

                            @include('template-parts.woocommerce.cart.cart-products-table')

                        </div>
                        <p class="cart-policy-links">Learn about <a href="#">shipping cost</a>, <a href="#">payment
                                options</a> and our <a href="#">return policy</a>.</p>
                    </div>
                    <div class="col-md-4">

                        @include('template-parts.woocommerce.sidebar.sidebar-cart')

                    </div>
                </div>

            </div>
        </div>

        <!-- Recommended products -->
        @include('template-parts.sliders.products', ['parent_section_class' => 'recommendations-section', 'slider_title' =>'How About THIS?'])

    <!-- Recommended products row 2 -->
        @include('template-parts.sliders.products', ['parent_section_class' => 'recommendations-section', 'slider_title' =>'You also may like'])

    </main>
@endsection
