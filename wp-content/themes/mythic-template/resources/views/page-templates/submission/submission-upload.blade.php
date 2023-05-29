@extends('layouts.app')

@include('template-parts.my-account.account-header')

@section('content')
    <main class="site-content submission-page-content">
        <div class="container">
            <h1 class="page-title">Design submission</h1>
            <div class="upload-container">
                @include('template-parts.submission.submission-step-1')
            </div>
            <div class="submit-container">
                <a href="#" class="button button-link">Back</a>
                <a href="#" class="button button-primary-gradient">Next step</a>
            </div>
        </div>
    </main>
@endsection
