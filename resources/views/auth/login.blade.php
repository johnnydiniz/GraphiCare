@extends('layouts.app')

@section('content')
    <div class="container-fluid flex-grow-1 d-flex justify-content-center py-3">
        <div class="layout-content-container flex-column py-3 w-100">
            <h2 class="login-title text-center px-4 pb-3 pt-5">{{ __('Login') }}</h2>
            <form method="POST" action="{{ route('autenticar') }}">
                @csrf
                <div class="max-w-480 px-4 py-3">
                    <div class="form-group">
                        <input id="login" name="login" type="text"
                            class="form-control form-control-custom @error('login') is-invalid @enderror"
                            value="{{ old('login') }}" required autofocus placeholder="{{ __('Login') }}" />
                        @error('login')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="max-w-480 px-4 py-3">
                    <div class="form-group">
                        <input id="password" name="password" type="password"
                            class="form-control form-control-custom @error('password') is-invalid @enderror" required
                            placeholder="{{ __('Password') }}" />
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="d-flex align-items-center px-4 min-h-14 justify-content-between">
                    <p class="text-dark mb-0 flex-grow-1 text-truncate">{{ __('Remember Me') }}</p>
                    <div class="form-check">
                        <input id="remember" name="remember" type="checkbox" class="form-check-input checkbox-custom"
                            {{ old('remember') ? 'checked' : '' }} />
                    </div>
                </div>
                <div class="px-4 py-3">
                    <button type="submit" class="btn btn-custom w-100">
                        <span class="text-truncate">{{ __('Login') }}</span>
                    </button>
                </div>
            </form>

            @if (Route::has('password.request'))
                <p class="text-center px-4 pb-3 pt-1">
                    <a href="#" class="text-decoration-underline"
                        style="color: #49739c;">{{ __('Forgot Your Password?') }}</a>
                </p>
            @endif
        </div>
    </div>
@endsection
