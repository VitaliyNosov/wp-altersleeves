<div class="lg-container">
    <div class="home-banner"
         @if(!empty($short_banner_data['text']))
         style="background-image:url({{ $short_banner_data['bg_image'] }})"
        @endif
    >
        @if(!empty($short_banner_data['text']))
            <div class="banner-text">
                {{ $short_banner_data['text'] }}
            </div>
        @endif
    </div>
</div>
