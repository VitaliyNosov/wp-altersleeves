@extends('layouts.app')

@section('content')
    <main class="site-content bureau-page-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Account']])

        <h1 class="page-title text-center position-absolute start-50 translate-middle-x">HELLO USER</h1>

        <div class="lg-container bureau-body-section">
            <div class="row bureau-body-section-row">
                <div class="col-12 col-lg-8 mt-3 mt-lg-0">
                    <div class="container bureau-body-section-container bureau-body-section-container-transactions">
                        <h2 class="section-title-orange mb-4">TRANSACTIONS</h2>
                        <div class="row position-relative">
                            <div class="col-md-1 offset-md-1">
                                <span class="table-cell">ID</span>
                            </div>

                            <div class="col-md-3">
                                <span class="table-cell">Name</span>
                            </div>
                            <div class="col-md-4">
                                <span class="table-cell">Address</span>
                            </div>
                            <div class="col-md-2">
                                <span class="table-cell">Date</span>
                            </div>
                            <div class="col-md-1">
                                <span class="table-cell"></span>
                            </div>
                        </div>
                        <div class="row position-relative">
                            <div class="col-md-1 offset-md-1">
                                <span class="table-cell">1234</span>
                            </div>
                            <div class="col-md-3">
                                <span class="table-cell">Frame Set #01</span>
                            </div>
                            <div class="col-md-4">
                                <span class="table-cell">Lorem Ipsum dolor 123</span>
                            </div>
                            <div class="col-md-2">
                                <span class="table-cell">04/29/2021</span>
                            </div>
                            <div class="col-md-1 mc_transactions_data_action">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row position-relative">
                            <div class="col-md-1 offset-md-1">
                                <span class="table-cell">1234</span>
                            </div>
                            <div class="col-md-3">
                                <span class="table-cell">Frame Set #01</span>
                            </div>
                            <div class="col-md-4">
                                <span class="table-cell">Lorem Ipsum dolor 123</span>
                            </div>
                            <div class="col-md-2">
                                <span class="table-cell">04/29/2021</span>
                            </div>
                            <div class="col-md-1 mc_transactions_data_action">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row position-relative">
                            <div class="col-md-1 offset-md-1">
                                <span class="table-cell">1234</span>
                            </div>
                            <div class="col-md-3">
                                <span class="table-cell">Frame Set #01</span>
                            </div>
                            <div class="col-md-4">
                                <span class="table-cell">Lorem Ipsum dolor 123</span>
                            </div>
                            <div class="col-md-2">
                                <span class="table-cell">04/29/2021</span>
                            </div>
                            <div class="col-md-1 mc_transactions_data_action">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row position-relative">
                            <div class="col-md-1 offset-md-1">
                                <span class="table-cell">1234</span>
                            </div>
                            <div class="col-md-3">
                                <span class="table-cell">Frame Set #01</span>
                            </div>
                            <div class="col-md-4">
                                <span class="table-cell">Lorem Ipsum dolor 123</span>
                            </div>
                            <div class="col-md-2">
                                <span class="table-cell">04/29/2021</span>
                            </div>
                            <div class="col-md-1 mc_transactions_data_action">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <h2 class="section-title mb-4">GRAPH</h2>
                            </div>
                            <div class="col-12 col-md-6 d-flex justify-content-center justify-content-md-end">
                                <button class="button button-icon-white-bg btn-file">
                                    <i class="icon-table-file"></i>
                                </button>
                                <h2 class="section-title-orange mb-4">MONTHLY</h2>
                            </div>
                        </div>
                        <div class="row mc_pie_chart_container">
                            <div class="col-12 col-md-6 mb-5 mb-md-0">
                                <canvas class="mc_pie_chart"></canvas>
                            </div>
                            <div class="col-12 col-md-6 mc_pie_chart_legends_container">
                                <div class="row mb-4">
                                    <div class="col-2 d-flex flex-column justify-content-center"><span
                                            class="mc_pie_chart_legend_span mc_pie_chart_legend_span_red"></span></div>
                                    <div class="col-10">
                                        <p class="mc_pie_chart_data" data-mc_pie_chart_data_value="50">Lorem ipsum1</p>
                                        <p>50%</p>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-2 d-flex flex-column justify-content-center"><span
                                            class="mc_pie_chart_legend_span mc_pie_chart_legend_span_blue"></span></div>
                                    <div class="col-10">
                                        <p class="mc_pie_chart_data" data-mc_pie_chart_data_value="30">Lorem ipsum2</p>
                                        <p>30%</p>
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <div class="col-2 d-flex flex-column justify-content-center"><span
                                            class="mc_pie_chart_legend_span mc_pie_chart_legend_span_yellow"></span>
                                    </div>
                                    <div class="col-10">
                                        <p class="mc_pie_chart_data" data-mc_pie_chart_data_value="20">Lorem ipsum3</p>
                                        <p>20%</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row bureau-body-section-row">
                <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">SALES</h2>
                            </div>
                            <div class="col-6 text-right">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title-orange">$</h2>
                            </div>
                            <div class="col-6 text-right">
                                <p>+5%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-lg-0">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">VISITORS</h2>
                            </div>
                            <div class="col-6 text-right">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title-orange">272</h2>
                            </div>
                            <div class="col-6 text-right">
                                <p>+14%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3 mb-3 mb-sm-0">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">ORDERS</h2>
                            </div>
                            <div class="col-6 text-right">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title-orange">31</h2>
                            </div>
                            <div class="col-6 text-right">
                                <p>+8%</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">PROFIT</h2>
                            </div>
                            <div class="col-6 text-right">
                                <button class="button button-icon btn-actions"><i class="icon-dots-more"></i></button>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title-orange">$</h2>
                            </div>
                            <div class="col-6 text-right">
                                <p>+2%</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row bureau-body-section-row">
                <div class="col-12 col-lg-8 mb-3 mb-lg-0">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">GRAPH</h2>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <button class="button button-icon-white-bg btn-file">
                                    <i class="icon-table-file"></i>
                                </button>
                                <h2 class="section-title-orange mb-4">MONTHLY</h2>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-4">
                    <div class="container bureau-body-section-container">
                        <div class="row">
                            <div class="col-6">
                                <h2 class="section-title mb-4">GRAPH</h2>
                            </div>
                            <div class="col-6 d-flex justify-content-end">
                                <button class="button button-icon-white-bg btn-file">
                                    <i class="icon-table-file"></i>
                                </button>
                                <h2 class="section-title-orange mb-4">MONTHLY</h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </main>
@endsection
