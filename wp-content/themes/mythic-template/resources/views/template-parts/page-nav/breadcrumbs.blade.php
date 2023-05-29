<nav class="page-breadcrumbs">
    @foreach ($breadcrumbs as $breadcrumb)
        @if ($loop->last)
            {{ $breadcrumb }}
        @else
            <a href="#">{{ $breadcrumb }}</a>
            <span class="devider">&gt;</span>
        @endif
    @endforeach
</nav>
