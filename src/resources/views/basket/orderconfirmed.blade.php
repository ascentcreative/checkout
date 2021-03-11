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
            
            console.log('polling for ' + uuid);

            $.get('/basket/pollorderconfirmation/' + uuid, function(data) {

                if (data.status == 'confirmed') {
                    window.location.reload();
                } 
                console.log(data);
            });

        }

    </SCRIPT>
@endpush
@endif




@section('contentbody')

@if($order->confirmed == 1)

    {{ $order }}

@else

    We're confirming your payment.

@endif

@endsection
