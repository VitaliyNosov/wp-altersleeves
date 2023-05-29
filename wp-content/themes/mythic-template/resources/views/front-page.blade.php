@extends('layouts.app')

@section('content')
    <main class="site-content home-page-content" id="app">

        @if(!empty($banner_short_data))
            @include('template-parts.promotions.banner-short', ['banner_short_data' => $banner_short_data])
        @endif

        @include('template-parts.sliders.products-big-single-and-half')

        @include('template-parts.homepage.sorted-products')

        @include('template-parts.woocommerce.products.featured-with-logo-text-and-slider')

        @include('template-parts.woocommerce.products.featured-with-logo-text-and-slider')

        @include('template-parts.sliders.articles', ['slider_title' =>'NEWEST ARTICLES'])

        @include('template-parts.sliders.creators', ['slider_title' =>'RECENTLY JOINED CREATORS'])

        @include('template-parts.sliders.products', ['slider_title' =>'INSPIRED BY YOUR BROWSING HISTORY'])

        @include('template-parts.promotions.promotion-join-us')

    </main>
@endsection
