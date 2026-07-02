@extends('layouts.app')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="d-flex justify-content-between align-items-center mb-4 gap-3 flex-wrap">
                <div>
                    <p class="text-uppercase text-body-secondary small mb-1">Admin</p>
                    <h1 class="h2 mb-0">Nouvel article</h1>
                </div>
            </div>

            <div class="card shadow-sm border-0">
                <div class="card-body p-4 p-lg-5">
                    <form method="POST" action="{{ route('admin.articles.store') }}">
                        @csrf
                        @include('admin.articles._form')
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
