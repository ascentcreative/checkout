@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

<IMG src="{{ asset(config('app.email_logo')) }}" width="250" alt="{{ config('app.name') }}" />

@endcomponent
@endslot

{{-- Body --}}
{{-- {{ $slot }} --}}

Your order has been shipped by {{ $shipment->shipper->name }} on {{ \Carbon\Carbon::parse($shipment->shipping_date)->format('j M Y') }}.

@if($shipment->tracking_number)

Your tracking number is **{{ $shipment->tracking_number }}**.  
  
@if($url = $shipment->shipper->getTrackingUrl($shipment->tracking_number))
You may [track your shipment online]({{ $url }}).
@endif

@endif

@component('mail::table')
|  | <!-- --> | 
|:--|--|
| **Order Num:** | {{ $order->orderNumber }} |
| **Order Date:** | {{ \Carbon\Carbon::parse($order->confirmed_at)->format('g:ia, j M Y') }}  |
@endcomponent

@component('mail::table')
| Items in Shipment | Qty | 
|:---- |--:|
@foreach($shipment->items as $item)
| {{ $item->sellable->getItemName() }} | {{ $item->qty }} | 
@endforeach
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


