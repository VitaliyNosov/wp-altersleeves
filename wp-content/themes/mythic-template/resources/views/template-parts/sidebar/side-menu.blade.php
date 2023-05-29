<div class="general-page-sidebar">
    <ul class="nav flex-column">
        @foreach ($side_menu as $side_menu_single)
            <li class="nav-item">
                @if ($loop->last)
                    <a class="nav-link active" aria-current="page" href="#">{{ $side_menu_single }}</a>
                @else
                    <a class="nav-link" href="#">{{ $side_menu_single }}</a>
                @endif
            </li>
        @endforeach
    </ul>
</div>
