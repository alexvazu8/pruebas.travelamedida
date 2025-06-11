@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Login') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-0 ">
                            <div class="col-md-6   text-end ">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>&nbsp;
                            </div>
                            <div class="col-md-6  text-start ">
                                <a class="google-login-btn d-inline-flex align-items-center px-3 py-2 border rounded shadow-sm text-dark text-decoration-none" 
                                    href="{{ route('login.google') }}" style="background-color: white;">
                                    <svg class="me-2" width="20" height="20" viewBox="0 0 533.5 544.3" xmlns="http://www.w3.org/2000/svg">
                                        <path fill="#4285F4" d="M533.5 278.4c0-17.4-1.6-34.1-4.7-50.4H272v95.3h146.9c-6.3 34.2-25.1 63.2-53.5 82.7v68.4h86.4c50.7-46.7 81.7-115.4 81.7-196z"/>
                                        <path fill="#34A853" d="M272 544.3c72.6 0 133.6-24.1 178.1-65.3l-86.4-68.4c-24 16.1-54.6 25.6-91.7 25.6-70.5 0-130.3-47.6-151.7-111.4H30.6v69.9c44.5 87.7 135.8 149.6 241.4 149.6z"/>
                                        <path fill="#FBBC05" d="M120.3 324.8c-10.3-30.3-10.3-62.9 0-93.2v-69.9H30.6c-41.3 82.2-41.3 180.9 0 263.1l89.7-69.9z"/>
                                        <path fill="#EA4335" d="M272 107.7c39.5-.6 77.4 13.8 106.3 40.7l79.4-79.4C417.7 25.5 345.9-1.5 272 0 166.4 0 75.1 61.9 30.6 149.6l89.7 69.9C141.7 155.3 201.5 107.7 272 107.7z"/>
                                    </svg>
                                    <span>{{ __('text.Login_google') }}</span>
                                </a>
                            </div>                          
                        </div>
                        <div class="row mb-0 justify-content-center">
                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
