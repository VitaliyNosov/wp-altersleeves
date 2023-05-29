<div class="page-section @if(!empty($parent_section_class)){{ $parent_section_class }}@endif">
    <div class="container-right">
        <div class="d-md-flex align-items-center justify-content-md-between text-center">
            <h2 class="page-section-title">{{ $slider_title }}</h2>
        </div>

        <div class="@if(!empty($slider_section_class)){{ $slider_section_class }} @else 'section-slider' @endif">
            <div class="article-card">
                <div class="article-title">Article title</div>
                <div class="article-thumbnail">
                    <img src="assets/images/article-1.png" alt="photo">
                </div>
                <div class="article-actions">
                    <a href="#" class="button button-link">Read article</a>
                </div>
            </div>

            <div class="article-card">
                <div class="article-title">Article title</div>
                <div class="article-thumbnail">
                    <img src="assets/images/article-2.png" alt="photo">
                </div>
                <div class="article-actions">
                    <a href="#" class="button button-link">Read article</a>
                </div>
            </div>

            <div class="article-card">
                <div class="article-title">Article title</div>
                <div class="article-thumbnail">
                    <img src="assets/images/article-2.png" alt="photo">
                </div>
                <div class="article-actions">
                    <a href="#" class="button button-link">Read article</a>
                </div>
            </div>
        </div>

        <div>
            <a href="#" class="button button-link btn-see-more">More articles</a>
        </div>
    </div>
</div>
