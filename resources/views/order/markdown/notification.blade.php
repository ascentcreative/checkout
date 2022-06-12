@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

<IMG src="{{ asset(config('app.email_logo')) }}" width="250" alt="{{ config('app.name') }}" />

@endcomponent
@endslot

{{-- Body --}}
{{-- {{ $slot }} --}}

You've got a new order!

@component('mail::table')
|  | <!-- --> | 
|:--|--|
| **Order Num:** | {{ $order->orderNumber }} |
| **Order Date:** | {{ \Carbon\Carbon::parse($order->confirmed_at)->format('g:ia, j M Y') }}  |
| **Customer Name:** | {{ $order->customer->name }} |
| **Customer Email:** | {{ $order->customer->email }} |

@endcomponent

@component('mail::table')
| Item |  |  |
|:---- | -----:|--:|
@foreach($order->items as $item)
| {{ $item->sellable->getItemName() }} | &pound;{{ number_format($item->purchasePrice, 2) }} | {{--[Download]({{ url($item->getDownloadUrl())}}) | --}}
@endforeach
| **Total** | **£{{ number_format($order->total, 2) }}** |
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


