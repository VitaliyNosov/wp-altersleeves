@extends('layouts.app')

@section('content')
    <main class="site-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Category', 'Page'], 'nav_tags' => ['MTG','Mythic Frames','Tag']])

        <div class="wrap-single-product-content">
            <div id="product-1" class="product">
                <!-- Main section -->
                @include('template-parts.woocommerce.product-single.product-main')

                <!-- Related products -->
                @include('template-parts.sliders.products', ['parent_section_class' => 'product-related-section', 'slider_title' =>'RELATED'])

                <!-- Product details -->
                @include('template-parts.woocommerce.product-single.product-details')

                <!-- Browsing history -->
                @include('template-parts.sliders.products', ['parent_section_class' => 'product-browsing-history-section', 'slider_title' =>'YOUR BROWSING HISTORY'])

            </div>
        </div>

    </main>
@endsection
