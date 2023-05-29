@extends('layouts.app')

@include('template-parts.my-account.account-header')

@section('content')
    <main class="site-content profile-page-content artist-profile-content">

        <div class="profile-heading" style="background-image:url(assets/images/bg-artist.jpg);">
            @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Blog', 'Creator']])
        </div>

        @include('template-parts.my-account.profile-main')

        @include('template-parts.sliders.products-featured-works')

        <!-- Favourite -->
        @include('template-parts.sliders.products', ['parent_section_class' => 'profile-favourite-section', 'slider_title' =>'Favourite designs'])


        <!-- Bestsellers -->
        @include('template-parts.sliders.products', ['parent_section_class' => 'profile-bestsellers-section', 'slider_title' =>'Bestsellers from artist'])

        <!-- See more -->
        @include('template-parts.promotions.promotion-need-to-see-more')

        <!-- Other artists -->
        @include('template-parts.sliders.creators', ['parent_section_class' => 'profile-artists-section', 'slider_title' =>'CHECK OUT OTHER ARTISTS'])

    </main>
@endsection
