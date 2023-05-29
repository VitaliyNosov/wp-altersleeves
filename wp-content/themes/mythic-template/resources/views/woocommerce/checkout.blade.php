@extends('layouts.app')

@section('content')
    <main class="site-content cart-page-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Checkout']])

        <div class="checkout-body-section">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        @include('template-parts.woocommerce.checkout.checkout-form')
                    </div>
                    <div class="col-lg-4">
                        @include('template-parts.woocommerce.sidebar.sidebar-checkout')
                    </div>
                </div>

            </div>
        </div>

    </main>
@endsection
