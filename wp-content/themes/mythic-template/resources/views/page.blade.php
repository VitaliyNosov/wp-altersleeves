@extends('layouts.app')

@section('content')
    <main class="site-content general-page-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Generic page']])

        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    @include('template-parts.sidebar.side-menu', ['side_menu' => ['Shipping', 'Returns', 'FAQ', 'Terms', 'Legal information', 'Privacy policy']])
                </div>
                <div class="col-md-8">
                    <div class="general-page-body">
                        <h1>General page</h1>

                        <div class="text-section">
                            <h4>Aenean turpis lacus</h4>
                            <p>
                                Semper et diam quis, pellentesque sodales magna. Nunc ac turpis augue. Orci varius
                                natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nam aliquet
                                rhoncus sapien, id consequat nunc venenatis non. Ut in magna auctor sapien varius
                                aliquam quis sit amet risus. Mauris quis fermentum ante. Phasellus quis venenatis risus.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Nunc tempor aliquam ipsum</h4>
                            <p>
                                Vel sagittis nisl porta et. Nam vulputate ex vitae ligula efficitur bibendum. Fusce eget
                                turpis nisl. Donec diam ante, vulputate eget enim ac, maximus rhoncus neque. Class
                                aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.
                                Sed dapibus nulla non ex congue, sed interdum metus vulputate. Phasellus ut eros
                                vestibulum, venenatis tortor at, scelerisque elit. Nullam et enim sed ex ullamcorper
                                gravida vitae a lorem. Duis nec placerat est, sit amet scelerisque tortor. Duis metus
                                est, aliquet a velit vel, mattis placerat elit.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Aenean</h4>
                            <p>
                                Lobortis libero consequat, vehicula risus in, vehicula nisl. Vestibulum accumsan lectus
                                neque, eu commodo purus porta dignissim. Maecenas nec nulla in eros ornare feugiat.
                                Aliquam lorem justo, gravida quis massa ac, euismod commodo ex. Ut sollicitudin
                                ullamcorper diam sed ultrices. Quisque volutpat vel odio quis dictum. Phasellus lacinia
                                ac ipsum vitae pulvinar. Nullam in arcu tincidunt, ullamcorper tellus in, auctor ante.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Donec a sodales</h4>
                            <p>
                                Nunc vitae augue gravida, maximus sem nec, luctus eros. Aenean dui sapien, consequat
                                eget rhoncus in, viverra in arcu. Praesent nec sapien in mi rutrum tempor. Proin rhoncus
                                iaculis nisi quis scelerisque. Donec scelerisque consequat metus, ut fringilla nisl.
                                Donec id nulla quis dui porttitor imperdiet. Integer eleifend ex faucibus, pellentesque
                                sapien at, pellentesque arcu. Fusce ac orci ullamcorper, rhoncus magna tempus, porttitor
                                elit. Nullam luctus dictum diam, at luctus velit ullamcorper nec. Phasellus pretium et
                                leo ac mattis. Mauris sagittis feugiat neque, quis dapibus est posuere non. Pellentesque
                                blandit urna sed risus condimentum convallis.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Nunc sagittis</h4>
                            <p>
                                Lorem sed quam vehicula rutrum. Aenean sed aliquet ipsum, vel tristique nisl. Integer
                                feugiat in tellus ut aliquam. Ut id sem suscipit, semper sem ut, dictum metus. Curabitur
                                dui arcu, ultricies vitae molestie nec, dignissim in nibh. Mauris vitae odio augue. Ut
                                at purus lectus. Nullam molestie, orci eget semper commodo, ipsum sapien pulvinar ante,
                                vel vulputate enim est non libero. Morbi a urna id ante fringilla laoreet. In cursus
                                arcu quis nibh condimentum malesuada. Curabitur fermentum ultrices diam, at tristique
                                nulla consequat non.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Sed ultrices</h4>
                            <p>
                                Arcu et enim sagittis, nec tempus enim porttitor. Nam egestas pretium magna vitae
                                convallis. Aenean consequat sodales enim. Praesent dignissim neque eget metus fermentum,
                                et mollis turpis mattis. Vivamus imperdiet at purus vel imperdiet. Aliquam ut sem non
                                nibh vulputate sollicitudin. Sed vel justo eu est condimentum feugiat. Nunc porta, est
                                nec feugiat pulvinar, metus lectus condimentum nulla, at ultrices tellus purus nec
                                magna. Curabitur sed pharetra nisl. Pellentesque et eros est. Donec sit amet tincidunt
                                dui, quis scelerisque velit.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Vivamus viverra</h4>
                            <p>
                                Tellus mollis aliquet feugiat, sapien erat faucibus velit, a pellentesque libero ipsum
                                nec velit. Vivamus commodo lorem leo, et consectetur augue convallis nec. Nam iaculis
                                sapien vel viverra vulputate. Curabitur ut neque lacus. Fusce sit amet nisi magna.
                                Aliquam sed viverra nisl. Nulla at dolor elit. Interdum et malesuada fames ac ante ipsum
                                primis in faucibus. In a dolor rhoncus, efficitur justo eu, pharetra dolor.
                            </p>
                        </div>

                        <div class="text-section">
                            <h4>Cras nec mi cursus</h4>
                            <p>
                                Porta nunc in, posuere nunc. Quisque orci sapien, luctus sed dictum ut, rutrum pharetra
                                tellus. Sed pulvinar volutpat magna, non molestie turpis cursus nec. Duis sit amet dui
                                vel nisi feugiat semper et sit amet nunc. Proin id viverra enim. Duis sed nulla et leo
                                suscipit condimentum id quis nulla. Pellentesque in ante in diam ullamcorper placerat
                                sed eu elit. Nunc vel semper libero. Orci varius natoque penatibus et magnis dis
                                parturient montes, nascetur ridiculus mus. Pellentesque fermentum velit massa, nec
                                euismod risus vehicula sit amet. Integer vel finibus massa, volutpat sodales ligula. Sed
                                id tortor ac nunc facilisis hendrerit eu vitae orci. Lorem ipsum dolor sit amet,
                                consectetur adipiscing elit. Aenean eu faucibus nisi, et aliquam orci.
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection
