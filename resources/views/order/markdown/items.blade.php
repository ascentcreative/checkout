@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])

<IMG src="{{ asset('/img/ecsongs-emaillogo.png') }}" width="250" alt="Essential Christian Songs" />

@endcomponent
@endslot

{{-- Body --}}
{{-- {{ $slot }} --}}

Thank you for your order from Essential Christian Songs.

@component('mail::table')
|  | <!-- --> | 
|:--|--|
| **Order Num:** | {{ $order->orderNumber }} |
| **Order Date:** | {{ \Carbon\Carbon::parse($order->confirmed_at)->format('g:ia, j M Y') }}  |
@endcomponent

@component('mail::table')
| Item |  |  |
|:---- | -----:|--:|
@foreach($order->items as $item)
| {{ $item->sellable->getItemName() }} | &pound;{{ number_format($item->purchasePrice, 2) }} | {{--[Download]({{ url($item->getDownloadUrl())}}) | --}}
@endforeach
| **Total** | **£{{ number_format($order->total, 2) }}** |
@endcomponent

@component('mail::button', ['url'=>url($order->url)])
    Download your files
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
<BR/>
Essential Christian, registered charity number 1126997, a company limited by guarantee, registered in England and Wales, number 06667924.
@endcomponent
@endslot
@endcomponent


