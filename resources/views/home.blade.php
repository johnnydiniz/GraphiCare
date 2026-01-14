@extends('layouts.app')

@section('content')
<div class="gap-1 px-6 flex flex-1 justify-center py-5">
    <div class="container-fluid p-0" style="max-width: 960px;">
        <!-- Cabeçalho -->
        <div class="d-flex flex-wrap justify-content-between p-4">
            <h1 class="text-dark m-0 fs-2 fw-bold" style="min-width: 288px; letter-spacing: -0.01em;">Dashboard</h1>
        </div>

        <!-- Cards de Métricas -->
        <div class="row g-4 p-4">
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-light">
                    <p class="text-dark mb-0 fw-medium">Total Orders</p>
                    <p class="text-dark m-0 fs-3 fw-bold">1,250</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-light">
                    <p class="text-dark mb-0 fw-medium">Pending Orders</p>
                    <p class="text-dark m-0 fs-3 fw-bold">150</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-light">
                    <p class="text-dark mb-0 fw-medium">Revenue</p>
                    <p class="text-dark m-0 fs-3 fw-bold">$50,000</p>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="d-flex flex-column gap-2 rounded-3 p-4 border border-light">
                    <p class="text-dark mb-0 fw-medium">Stock Level</p>
                    <p class="text-dark m-0 fs-3 fw-bold">80%</p>
                </div>
            </div>
        </div>

        <!-- Gráfico de Pedidos -->
        <h2 class="text-dark fs-4 fw-bold px-4 pb-3 pt-5" style="letter-spacing: -0.015em;">Order Trends</h2>
        <div class="row px-4 py-4">
            <div class="col-12">
                <div class="d-flex flex-column gap-2 rounded-3 border border-light p-4">
                    <p class="text-dark mb-0 fw-medium">Orders Over Time</p>
                    <p class="text-dark m-0 fs-2 fw-bold text-truncate">1,250</p>
                    <div class="d-flex gap-2">
                        <p class="text-secondary mb-0">Last 12 Months</p>
                        <p class="text-success mb-0 fw-medium">+15%</p>
                    </div>
                    <div class="d-flex flex-column gap-4 py-3" style="min-height: 180px;">
                        <svg width="100%" height="148" viewBox="-3 0 478 150" fill="none"
                            xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                            <path
                                d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25V149H326.769H0V109Z"
                                fill="url(#paint0_linear_1131_5935)"></path>
                            <path
                                d="M0 109C18.1538 109 18.1538 21 36.3077 21C54.4615 21 54.4615 41 72.6154 41C90.7692 41 90.7692 93 108.923 93C127.077 93 127.077 33 145.231 33C163.385 33 163.385 101 181.538 101C199.692 101 199.692 61 217.846 61C236 61 236 45 254.154 45C272.308 45 272.308 121 290.462 121C308.615 121 308.615 149 326.769 149C344.923 149 344.923 1 363.077 1C381.231 1 381.231 81 399.385 81C417.538 81 417.538 129 435.692 129C453.846 129 453.846 25 472 25"
                                stroke="#6c757d" stroke-width="3" stroke-linecap="round"></path>
                            <defs>
                                <linearGradient id="paint0_linear_1131_5935" x1="236" y1="1" x2="236"
                                    y2="149" gradientUnits="userSpaceOnUse">
                                    <stop stop-color="#e9ecef"></stop>
                                    <stop offset="1" stop-color="#e9ecef" stop-opacity="0"></stop>
                                </linearGradient>
                            </defs>
                        </svg>
                        <div class="d-flex justify-content-around">
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Jan</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Feb</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Mar</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Apr</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">May</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Jun</p>
                            <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Jul</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status de Inventário -->
        <h2 class="text-dark fs-4 fw-bold px-4 pb-3 pt-5" style="letter-spacing: -0.015em;">Inventory Status</h2>
        <div class="row px-4 py-4">
            <div class="col-12">
                <div class="d-flex flex-column gap-2 rounded-3 border border-light p-4">
                    <p class="text-dark mb-0 fw-medium">Stock Levels</p>
                    <p class="text-dark m-0 fs-2 fw-bold text-truncate">80%</p>
                    <div class="d-flex gap-2">
                        <p class="text-secondary mb-0">Current</p>
                        <p class="text-success mb-0 fw-medium">+5%</p>
                    </div>
                    <div class="grid-container py-3"
                        style="display: grid; grid-template-columns: auto 1fr; gap: 1rem 1.5rem; align-items: center; min-height: 180px;">
                        <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Paper</p>
                        <div class="progress-container h-100 w-100">
                            <div class="h-100 bg-light" style="width: 20%; border-right: 2px solid #6c757d;"></div>
                        </div>
                        <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Ink</p>
                        <div class="progress-container h-100 w-100">
                            <div class="h-100 bg-light" style="width: 50%; border-right: 2px solid #6c757d;"></div>
                        </div>
                        <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Toner</p>
                        <div class="progress-container h-100 w-100">
                            <div class="h-100 bg-light" style="width: 40%; border-right: 2px solid #6c757d;"></div>
                        </div>
                        <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Envelopes</p>
                        <div class="progress-container h-100 w-100">
                            <div class="h-100 bg-light" style="width: 40%; border-right: 2px solid #6c757d;"></div>
                        </div>
                        <p class="text-secondary mb-0 small fw-bold" style="letter-spacing: 0.015em;">Labels</p>
                        <div class="progress-container h-100 w-100">
                            <div class="h-100 bg-light" style="width: 60%; border-right: 2px solid #6c757d;"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
