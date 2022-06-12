@extends(config('checkout.basket_blade')) 

@push('styles')

@endpush


@section('wrapper-class', 'basket')

@section('basket.pagetitle', 'Your Basket')

@section('basket.contentstable')

    @include('checkout::basket.contents')   

    @if(!basket()->isEmpty)
        <a href="/basket/clear" class="ajax-link button btn-small" data-response-target="refresh"><i class="bi-x-circle-fill"></i> Clear Basket</a>
    @endif

@endsection



@section('basket.sidebar')

    @if(!basket()->isEmpty)

        {{-- if the site requires a login to checkout, display a login  / register form which redirects back to the basket --}}
        @if(!@config('checkout.anonymous_checkout') && !Auth::user())

                <div class="sb_block">
                    
                    
                    <div class="">
                    
                        @include('auth.loginform', ['intro'=>'Please Login to continue your purchase', 'intended'=>request()->path()])

                    </div>


                </div>

        @else
            {{-- otherwise (either logged in or anon-allowed,), just show the checkout blocks --}}
            <div class="formpanel">
                <x-transact-stripe-ui id="stripe-ui" />
            </div>

         
           
          

        @endif

    @endif

@endsection



@push('scripts')
<SCRIPT>

    // assign a function to the stripe UI.
    // this will be run as a promise when the pay button is clicked.
    // - for example, this could use AJAX to submit a form to save a record
    // - it should then start a Transaction and return the payment intent.
    // it is required to return the PaymentIntent
     // the CS will then be passed to the .then chain to actually process the payment

    $(document).ready(function() {
        $('#stripe-ui').stripeui('setStartFunction', function(resolve, reject) {
           
            $.ajax({       
                type: 'POST',
                url: '/basket/transact',
                data: {
                    '_token': '{{ csrf_token() }}'
                },
                headers: {
                    'Accept' : "application/json"
                }
             }).done(function(data, xhr, request) {
                // resolve('force-fail');
                resolve(data); // return the PaymentIntent
             }).fail(function(data) {
                reject(data.statusText);
             });

         });
    });

    $(document).on('transact-success', function() {
        window.location = '/basket/complete';
    });
   


    // a function to run when the payment completes
//     $('stripe-ui').onSuccess() {

//     }

//     $('stripe-ui').onFail() {

//      }
   

   

</SCRIPT>
@endpush

