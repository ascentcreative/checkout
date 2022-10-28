{{-- @extends(config('checkout.basket_blade'))  --}}
@extends(config('cms.wrapper_blade'))


@section(config('cms.wrapper_blade_section'))
<div class="centralise pt-3">
    <livewire:checkout />
</div>
@endsection