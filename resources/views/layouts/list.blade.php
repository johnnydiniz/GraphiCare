@extends('layouts.app')

@section('title')
    {{ __($title) }}
@endsection()

@section('content')
    <div class="container-fluid py-5 px-4 px-md-5">
        <div class="row justify-content-center">
            <div class="col-12">
                <!-- Cabeçalho -->
                <div class="d-flex flex-wrap justify-content-between p-4">
                    <h1 class="text-dark m-0 fs-2 fw-bold" style="min-width: 288px; letter-spacing: -0.01em;">
                        {{ __($title) }} </h1>
                    <a class="btn bg-light text-dark d-flex align-items-center justify-content-center overflow-hidden rounded-3 py-1 px-4 text-truncate small fw-medium"
                        style="min-width: 84px; max-width: 480px; height: 32px;" href="{{ route($route) }}"> +
                        {{ __($title) }} </a>
                </div>

                <x-search-filters :title="$title" :status-options="$statusOptions ?? null" />

                @yield('table')
            </div>
        </div>
    </div>
@endsection()
