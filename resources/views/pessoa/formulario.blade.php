@extends('layouts.app')

@section('title')
    {{ $title }}
@endsection()

@section('content')
    <div>
        <div class="container-fluid py-5 px-4 px-md-5">
            <div class="row justify-content-center">
                <div class="col-12">
                    <!-- Cabeçalho -->
                    <div class="d-flex flex-wrap justify-content-between p-4">
                        <h1 class="text-dark m-0 fs-2 fw-bold" style="min-width: 288px; letter-spacing: -0.01em;">New Person
                        </h1>
                    </div>
                    <!-- Abas -->
                    <div class="pb-3">
                        <ul class="nav nav-tabs border-bottom-1 px-4 gap-2 gap-md-4">
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-primary border-3 pb-3 pt-4 text-decoration-none"
                                    href="#">
                                    <span class="small fw-bold text-dark"
                                        style="letter-spacing: 0.015em;">{{ __('General') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                                    href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Customer') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                                    href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Provider') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                                    href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Employee') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                                    href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Contact') }}</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="d-flex flex-column align-items-center justify-content-center border-bottom border-3 pb-3 pt-4 text-decoration-none"
                                    href="#" style="border-bottom-color: transparent !important; color: #49739c;">
                                    <span class="small fw-bold" style="letter-spacing: 0.015em;">{{ __('Address') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Campos do formulário -->
                    @include('layouts.form')
                </div>
            </div>
        </div>
    </div>
@endsection
