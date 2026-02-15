<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - GraphiCare</title>

    <link rel="stylesheet" as="style" onload="this.rel='stylesheet'"
        href="https://fonts.googleapis.com/css2?display=swap&amp;family=Inter%3Awght%40400%3B500%3B700%3B900&amp;family=Noto+Sans%3Awght%40400%3B500%3B700%3B900" />
    @vite(['resources/sass/app.scss', 'resources/css/app.css'])

    <style>
        body {
            font-family: Inter, "Noto Sans", sans-serif;
            background: #fff;
        }
        .print-header {
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 24px;
        }
        .print-logo {
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.015em;
        }
        .print-doc-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #555;
        }
        .print-actions {
            margin-bottom: 20px;
        }
        .signature-line {
            border-top: 1px solid #333;
            width: 300px;
            margin-top: 60px;
            padding-top: 5px;
            text-align: center;
        }
        @media print {
            .print-actions {
                display: none !important;
            }
            body {
                font-size: 12px;
                margin: 0;
                padding: 10mm;
            }
            .container {
                max-width: 100% !important;
                padding: 0 !important;
            }
            .table {
                font-size: 11px;
            }
            .print-header {
                margin-bottom: 16px;
                padding-bottom: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="print-actions d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-print me-1"></i> {{ __('Print') }}
            </button>
            <button onclick="window.close()" class="btn btn-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> {{ __('Back') }}
            </button>
        </div>

        <div class="print-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('imgs/logo.png') }}" alt="GraphiCare" style="height: 36px;">
                <span class="print-logo">GraphiCare</span>
            </div>
            <div class="print-doc-title text-end">
                @yield('doc-title')
            </div>
        </div>

        @yield('content')
    </div>
</body>

</html>
