<div class="page-navigation">
    <div class="lg-container d-flex justify-content-between">
        @if(!empty($breadcrumbs))
            @include('template-parts.page-nav.breadcrumbs', ['breadcrumbs' => $breadcrumbs])
        @endif
        @if(!empty($nav_tags))
            @include('template-parts.page-nav.tags', ['nav_tags' => $nav_tags])
        @endif
        @if(!empty($nav_sort))
            @include('template-parts.page-nav.sort', ['nav_sort' => $nav_sort])
        @endif
    </div>
</div>
