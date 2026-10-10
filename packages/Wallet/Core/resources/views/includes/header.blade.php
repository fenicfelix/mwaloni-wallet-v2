<!DOCTYPE html>
<html lang="en" data-layout="topnav">

<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @if (request()->is('*wallet-transactions*') || request()->is('*transactions*'))
    <meta http-equiv="refresh" content="300">
    @endif
    <meta content="Naivas eWallet" name="description" />
    <meta content="Naivas eCommerce Technology Team" name="author" />
    <meta name="developer" content="Naivas eCommerce Technology Team">

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('themes/hyper/images/favicon.ico') }}">

    <!-- Plugin css -->
    <link href="{{ asset('themes/hyper/vendor/daterangepicker/daterangepicker.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('themes/hyper/vendor/jsvectormap/jsvectormap.min.css') }}" rel="stylesheet" type="text/css">

    <!-- Theme Config Js -->
    <script src="{{ asset('themes/hyper/js/hyper-config.js') }}"></script>

    <!-- Vendor css -->
    <link href="{{ asset('themes/hyper/css/vendor.min.css') }}" rel="stylesheet" type="text/css" />

    <!-- Plugin css -->
    <link rel="stylesheet" href="{{ asset('themes/hyper/vendor/jquery-toast-plugin/jquery.toast.min.css') }}">

    <!-- App css -->
    <link href="{{ asset('themes/hyper/css/app.min.css') }}" rel="stylesheet" type="text/css" id="app-style" />

    <!-- Icons css -->
    <link href="{{ asset('themes/hyper/css/unicons/css/unicons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/hyper/css/remixicon/remixicon.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('themes/hyper/css/mdi/css/materialdesignicons.min.css') }}" rel="stylesheet" type="text/css" />

    {{-- Custom css --}}
    <link href="{{ asset('themes/hyper/css/custom.css') }}" rel="stylesheet" type="text/css" id="app-style" />
</head>