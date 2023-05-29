<div class="sorted-products">
    <div class="lg-container">
        <div class="row">
            <div class="col-xl-8">
                <div class="row">
                    @include('template-parts.woocommerce.products.four-products-as-square', ['square_title' => 'BESTSELLERS'])
                    @include('template-parts.woocommerce.products.four-products-as-square', ['square_title' => 'Sales'])
                </div>
                <div class="row d-none d-md-flex">
                    @include('template-parts.woocommerce.products.four-products-as-square', ['square_title' => 'Category'])
                    @include('template-parts.woocommerce.products.four-products-as-square', ['square_title' => 'Category'])
                </div>
            </div>
            <div class="col-xl-4">
                <div class="row h-100">
                    @include('template-parts.promotions.banner-unlocked', ['banner_unlocked_data' => ''])
                </div>
            </div>
        </div>
    </div>
</div>
