@extends('adminlte::page')

{{-- Extend and customize the browser title --}}

@section('title')
    {{ config('adminlte.title') }}
    @hasSection('subtitle')
        | @yield('subtitle')
    @endif
@stop

{{-- Extend and customize the page content header --}}

@section('content_header')
    @hasSection('content_header_title')
        <h1 class="text-muted">
            @yield('content_header_title')

            @hasSection('content_header_subtitle')
                <small class="text-dark">
                    <i class="fas fa-xs fa-angle-right text-muted"></i>
                    @yield('content_header_subtitle')
                </small>
            @endif
        </h1>
    @endif
@stop

{{-- Rename section content to content_body --}}

@section('content')
    @include('layouts.flash-message')
    @yield('content_body')
@stop

{{-- Create a common footer --}}

@section('footer')
    <div class="float-right">
        Version: {{ config('app.version', '1.0.0') }}
    </div>

    <strong>
        <a href="{{ config('app.company_url', '#') }}">
            {{ config('app.company_name', 'Made by Viktor Molnar') }}
        </a>
    </strong>
@stop

@push('css')
    <style type="text/css">
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active {
            background-color: #6C757D;
        }

        .card-primary:not(.card-outline)>.card-header {
            background-color: #343A40;
        }

        a {
            color: #343A40;
            &:hover {
                color: #6C757D;
            }
        }

        .page-item.active .page-link {
            background-color: #343A40;
            border-color: #343A40;
        }

        .page-link, .page-link:hover {
            color: #343A40;
        }
    </style>
@endpush