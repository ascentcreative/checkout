{{-- @extends('base') --}}
@extends(config('cms.wrapper_blade'))

@section('contenthead')
    <H1>@yield('basket.pagetitle')</H1>
@endsection

@section(config('cms.wrapper_blade_section'))

<div class="centralise pt-3">

    @yield('contenthead')

    @if(config('checkout.livewire_checkout'))

        @yield('checkout')

    @else

    <div class="grid" style="display: grid; gap: 2rem; grid-template-columns: repeat(auto-fill, minmax(400px, 1fr))">
        <div style="grid-column: 1 / span 2;">

            <div class="border bg-white p-5">
                @yield('basket.contentstable')
            </div>


        <div>
            <div class="border bg-white p-5">
                @yield('basket.sidebar')
            </div>
        </div>

    </div>

   @endif


</div>

@endsection