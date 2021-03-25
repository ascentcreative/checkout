@extends(config('checkout.order_blade')) 

@section('order.pagetitle', $pageTitle)

@section('order.details')

    @include('checkout::order.details')

@endsection



@section('order.sidebar')
   
<div class="sb_block">
    <div class="sb_block_head">Order Info</div>

    <div class="sb_block_body">

        <table cellpadding="5">

            <tr><td class="font-weight-bold small">Order Num:</td><td>{{$order->orderNumber}}</td></tr>
            <tr><td class="font-weight-bold small">Order Date:</td><td>{{\Carbon\Carbon::parse($order->confirmed_at)->format('d F Y, g:ia')}}</td></tr>
            @if($last4 = $order->transactionLast4)
            <tr><td class="font-weight-bold small">Card# Ending:</td><td>{{ $last4 }}</td></tr>
            @endif


        </table>

    </div>

</div>

    {{-- While it'd be great to include these, going to hang fire for now... --}}
    {{-- <div class="sb_block">
        <div class="sb_block_head">Actions</div>

        <div class="sb_block_body">

            @if($order->hasDownloadItems())
            <button style="display: inline-block; width: 100%; margin-bottom: 5px" class="bi-cloud-arrow-down-fill"> Download All Files</button>
            @endif

            <A class="button ajax-link" style="display: block; width: 100%; margin-bottom: 5px" href="/checkout/orders/{{$order->uuid}}/resend">Resend Email</a>

        </div>
    </div> --}}

    <div class="text-center">
        <A href="{{ route('checkout-all-orders') }}" class="button"><i class="bi-caret-left-fill"></i> Back to orders list</A>
    </div>
        
@endsection