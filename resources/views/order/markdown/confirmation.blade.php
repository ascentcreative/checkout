@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

<IMG src="{{ asset(config('app.email_logo')) }}" width="250" alt="{{ config('app.name') }}" />

@endcomponent
@endslot

{{-- Body --}}
{{-- {{ $slot }} --}}

Thank you for your order.

@component('mail::table')
|  | <!-- --> | 
|:--|--|
| **Order Num:** | {{ $order->orderNumber }} |
| **Order Date:** | {{ \Carbon\Carbon::parse($order->confirmed_at)->format('g:ia, j M Y') }}  |
@endcomponent

@component('mail::table')
| Item | Price | Qty | Total |
|:---- | -----:|--:|--:|--:|
@foreach($order->items as $item)
| {{ $item->sellable->getItemName() }} | &pound;{{ number_format($item->itemPrice, 2) }} | {{ $item->qty }} | &pound;{{ number_format($item->qty * $item->itemPrice, 2) }} |
@endforeach
| **Total** |  |  | **£{{ number_format($order->total, 2) }}** |
@endcomponent




{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. @lang('All rights reserved.')
@endcomponent
@endslot
@endcomponent


