<div class="sort-container d-flex align-items-center">
    <span>Sort by</span>
    <select class="form-control sort-by-control" id="results_sort">
        @foreach ($nav_sort as $nav_sort_single)
            <option>{{ $nav_sort_single }}</option>
        @endforeach
    </select>
</div>
