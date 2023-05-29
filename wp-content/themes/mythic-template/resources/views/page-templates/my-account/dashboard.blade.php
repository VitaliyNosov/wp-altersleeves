@extends('layouts.app')

@include('template-parts.my-account.account-header')

@section('content')
    <main class="site-content account-page-content">

        @include('template-parts.page-nav.page-nav', ['breadcrumbs' => ['Home', 'Account', 'Dashboard']])

        <div class="lg-container">
            <div class="account-body">
                <div class="account-tabs-heading">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home"
                                    type="button" role="tab" aria-controls="home" aria-selected="true">Admin
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile"
                                    type="button" role="tab" aria-controls="profile" aria-selected="false"><span
                                    class="display-mobile">Mod</span> <span class="display-desktop">Moderator</span>
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact"
                                    type="button" role="tab" aria-controls="contact" aria-selected="false">Artist
                            </button>
                        </li>
                    </ul>

                    <a href="#" class="button-link">Settings</a>
                </div>

                <div class="account-tabs-content tab-content flex-table" id="myTabContent">
                    <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                        <div class="row">
                            <div class="col-12">
                                <div class="tab-content-title">Transactions</div>
                            </div>
                        </div>
                        <div class="row d-none d-lg-block flex-table-header">
                            <div class="col-lg-10 offset-md-2">
                                <div class="row">
                                    <div class="col-lg-1">
                                        <span class="table-cell">ID</span>
                                    </div>

                                    <div class="col-lg-2">
                                        <span class="table-cell">Name</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="table-cell">Address</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="table-cell">Date</span>
                                    </div>
                                    <div class="col-lg-1">
                                        <span class="table-cell">Price</span>
                                    </div>
                                    <div class="col-lg-2">
                                        <span class="table-cell">Status</span>
                                    </div>
                                    <div class="col-lg-1">
                                        <span class="table-cell">File</span>
                                    </div>
                                    <div class="col-lg-1">
                                        <span class="table-cell"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-3 col-lg-2">
                                <ul class="nav nav-tabs nav-tabs-column d-block" id="myTab-2" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="transactions-1" data-bs-toggle="tab"
                                                data-bs-target="#transactions-a" type="button" role="tab"
                                                aria-controls="transactions-1" aria-selected="true">A
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="transactions-2" data-bs-toggle="tab"
                                                data-bs-target="#transactions-b" type="button" role="tab"
                                                aria-controls="transactions-2" aria-selected="false">B
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="transactions-3" data-bs-toggle="tab"
                                                data-bs-target="#transactions-c" type="button" role="tab"
                                                aria-controls="transactions-3" aria-selected="false">C
                                        </button>
                                    </li>
                                </ul>
                            </div>
                            <div class="col-9 col-lg-10">
                                <div class="tab-content" id="myTabContent-2">
                                    <div class="tab-pane fade show active" id="transactions-a" role="tabpanel"
                                         aria-labelledby="transactions-1">
                                        <div class="flex-table-rows">

                                            @include('template-parts.dashboard.dashboard-transaction')
                                            @include('template-parts.dashboard.dashboard-transaction')
                                            @include('template-parts.dashboard.dashboard-transaction')
                                            @include('template-parts.dashboard.dashboard-transaction')

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="transactions-b" role="tabpanel"
                                         aria-labelledby="transactions-2">
                                        <div class="flex-table-rows">

                                            @include('template-parts.dashboard.dashboard-transaction')
                                            @include('template-parts.dashboard.dashboard-transaction')

                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="transactions-c" role="tabpanel"
                                         aria-labelledby="transactions-3">
                                        <div class="flex-table-rows">
                                            @include('template-parts.dashboard.dashboard-transaction')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">

                    </div>
                    <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">...</div>
                </div>
            </div>
        </div>
    </main>
@endsection
