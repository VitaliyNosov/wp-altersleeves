$(document).ready(function() {

    /** Global Variables **/
    window.asLib = {};
    window.asLib.animations = {};
    window.asLib.functions = {
        alter: {}
    };
    window.asLib.product = {
        info: {
            data: {
                class: 'cas-product-info__data',
                selector: '.cas-product-info__data',
                $selector: $('.cas-product-info__data'),
                active: {
                    class: 'cas-product-info--active'
                }
            },
            options: {
                frameEra: {
                    container: {
                        class: 'cas-product-info-frame-eras',
                        selector: 'cas-product-info-frame-eras',
                        $selector: $('.cas-product-info-frame-eras')
                    },
                    chevron: {
                        $selector: $('#chevron-frame-eras')
                    },
                    class: 'cas-product-info-frame-era',
                    selector: '.cas-product-frame-era',
                    data: {
                        id: 'selected-frame-eras',
                        selector: '#selected-frame-eras',
                        $selector: $('#selected-frame-eras')
                    },
                    wrapper: {
                        class: 'cas-product-info-frame-eras-wrapper',
                        selector: 'cas-product-info-frame-eras-wrapper',
                        $selector: $('.cas-product-info-frame-eras-wrapper')
                    },
                },
                printing: {
                    container: {
                        class: 'cas-product-info-printings',
                        selector: 'cas-product-info-printings',
                        $selector: $('.cas-product-info-printings')
                    },
                    chevron: {
                        $selector: $('#chevron-printings')
                    },
                    class: 'cas-product-info-printing',
                    selector: '.cas-product-info-printing',
                    data: {
                        id: 'selected-printings',
                        selector: '#selected-printings',
                        $selector: $('#selected-printings')
                    },
                    wrapper: {
                        class: 'cas-product-info-printings-wrapper',
                        selector: 'cas-product-info-printings-wrapper',
                        $selector: $('.cas-product-info-printings-wrapper')
                    },
                }
            },
            title: {
                class: 'cas-product-info__title',
                selector: '.cas-product-info__title',
                $selector: $('.cas-product-info__title')
            },
        },
        sidebar: {
            button: {
                addToCart: {
                    class: 'cas-product-alter-sidebar .cas-add-to-cart',
                    selector: '.cas-product-alter-sidebar .cas-add-to-cart',
                    $selector: $('.cas-product-alter-sidebar .cas-add-to-cart')
                }
            }
        },
        slider: {
            images: {
                alter: {
                    class: 'product-slider-image__alter',
                    selector: '.product-slider-image__alter',
                    $selector: $('.product-slider-image__alter'),
                },
                class: 'product-slider-images',
                selector: '.product-slider-images',
                $selector: $('.product-slider-images'),
                printing: {
                    class: 'product-slider-image__printing',
                    selector: '.product-slider-image__printing',
                    $selector: $('.product-slider-image__printing'),
                },
                shake: {
                    class: 'product-slider--shake',
                    selector: '.product-slider--shake',
                    $selector: $('.product-slider--shake'),
                }
            },
            slider: {
                class: 'product-slider',
                selector: '.product-slider',
                $selector: $('.product-slider'),
                status: {
                    up: {
                        class: 'product-slider--up',
                        selector: '.product-slider--up',
                        $selector: $('.product-slider--up')
                    },
                    down: {
                        class: 'product-slider--down',
                        selector: '.product-slider--down',
                        $selector: $('.product-slider--down')
                    },
                }
            },
            wrapper: {
                class: 'product-slider-wrapper',
                selector: '.product-slider-wrapper',
                $selector: $('.product-slider-wrapper'),
            }
        }
    };

});