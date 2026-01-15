@extends('core::layouts.auth')

@section("styles")

@endsection

@section( 'main_body')

<div>
        <div class="auth-div">
            <div class="card p-0 col-lg-8 offset-lg-2">
                <div class="row no-gutters">
                    <div class="col-md-6 bg-dark r-l" style="">
                        <div class="p-4 d-flex flex-column h-100">
                            <h4 class="mb-3 text-white">Mwaloni</h4>
                            <div class="text-fade">Intelligent Systems</div>
                            <div class="d-flex flex align-items-center justify-content-center">
                                <div class="animate fadeIn">
                                    <img src="{{ asset('themes/agile/img/logo-white-lg.png') }}" alt="Mwaloni Limited Logo" width="250">
                                </div>
                            </div>
                            <div class="text-right text-inherit"><a href="#" class="text-fade">Version 3.0.0</a></div>
                        </div>
                    </div>
                    <div class="col-md-6" id="content-body">
                        <div class="p-4">
                            <h5>Welcome back</h5>
                            <p>
                                <small class="text-muted">Login to manage your account</small>
                            </p>
                            <form method="POST" action="{{ route('login') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="login-email">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" value="" autocomplete="false" required>
                                </div>
                                <div class="form-group">
                                    <label for="password">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" value="" autocomplete="false" required>
                                    <div class="my-3 text-right"><span class="forgot-password"></span> <span class="forgot-password forgot-link"><a href="{{ route('forgot-password') }}" title="Forgot password link" class="text-muted">Forgot password?</a></span></div>
                                </div>
                                <div class="checkbox mb-3">
                                    <label class="ui-check">
                                    <input type="checkbox" id="remember_me" name="remember"><i></i> Remember me
                                    </label>
                                </div>
                                @if (session()->has('status'))
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="alert alert-danger" role="alert">{{ session("status") }}</div>
                                        </div>
                                    </div>
                                @endif
                                <button type="submit" class="btn btn-dark btn-block rounded mb-4">Sign in</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section("scripts")

@endsection


