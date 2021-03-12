@extends(config('checkout.global_blade')) 

@section('basket.pagetitle', 'Thank you for your order')

@if($order->confirmed != 1)
@push('scripts')
    <SCRIPT>

        // swap this for AJAX polling...
        setInterval(function(){
            pollOrderConfirmation('{{ $order->uuid }}');
        }, 1000);

        function pollOrderConfirmation(uuid) {
            
            $.get('/basket/pollorderconfirmation/' + uuid, function(data) {

                if (data.status == 'confirmed') {
                    window.location.reload();
                } 
            });

        }

    </SCRIPT>
@endpush
@endif




@section('contentbody')

@if($order->confirmed == 1)

    <P>Your order has been confirmed.</P>

    <P>(More detail to be included here, such as download links etc...)</P>

@else

    We're confirming your payment.

@endif

@endsection
