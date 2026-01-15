@extends('core::layouts.auth')

@section("styles")

@endsection

@section( 'main_body')

<div class="d-flex flex-column flex">
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
                            <div>
                                <h5>Forgot your password?</h5>
                                <p class="text-muted my-3">
                                Enter your email below and we will send you instructions on how to change your password.
                                </p>
                            </div>
                            <form method="POST" action="{{ route('forgot-password.email') }}" class="py-4 my-md-4">
                                @csrf
                                <div class="form-group">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="Email" class="form-control" required="">
                                </div>
                                <button type="submit" class="btn btn-dark btn-block rounded">Send</button>
                            </form>
                            <div class="pt-1">
                                Return to 
                                <a href="{{ route('login') }}" class="text-dark font-weight-bold" data-pjax-state="">Sign in</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section("scripts")

@endsection