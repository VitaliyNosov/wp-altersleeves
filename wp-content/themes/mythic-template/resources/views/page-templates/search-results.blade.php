@extends('layouts.app')

@section('content')
    <main class="site-content search-results-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Search results'], 'nav_sort' => ['Featured', '1', '2', '3']])

        <h1 class="page-title">We found this, check it out!</h1>

        <div class="search-results-wrapper">

            @include('template-parts.sidebar.side-filter')

            <!-- Section 1 -->
            @include('template-parts.sliders.products', ['slider_title' =>'<h2 class="page-section-title">Mythic frames matching <a href="#" class="link">Category</a></h2>', 'slider_button' => 'Mythic frames', 'slider_section_class' => 'results-slider mythic-frames results-products-slider'])

            <!-- Section 2 -->
            @include('template-parts.sliders.products', ['slider_title' =>'<h2 class="page-section-title">Mythic frames matching <a href="#" class="link">Category</a></h2>', 'slider_button' => 'Alter Sleeves', 'slider_section_class' => 'results-slider alter-sleeves results-products-slider'])

            <!-- Section 3 -->
            @include('template-parts.sliders.creators', ['slider_title' =>'<h2 class="page-section-title">Creators matching <a href="#" class="link">Category</a></h2>', 'slider_section_class'=>'results-slider'])

            <!-- Section 4 -->
            @include('template-parts.sliders.creators', ['slider_title' =>'<h2 class="page-section-title">Artists matching <a href="#" class="link">Category</a></h2>', 'slider_section_class'=>'results-slider'])

            <!-- Section 5 -->
            @include('template-parts.sliders.articles', ['slider_title' =>'<h2 class="page-section-title">Articles matching <a href="#" class="link">Category</a></h2>', 'slider_section_class' => 'results-slider'])

        </div>
    </main>
@endsection
