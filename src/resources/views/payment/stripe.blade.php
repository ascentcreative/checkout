@push('scripts')
    @script('https://js.stripe.com/v3/')

    <SCRIPT>

        var stripe = Stripe('{{ config('checkout.stripe_public_key') }}');
        var elements = stripe.elements();

        $(document).ready(function() {
            //Set up Stripe.js and Elements to use in checkout form
            var style = {
                base: {
                    backgroundColor: "#ffffff",
                    padding: '10px',
                    fontFamily: 'Montserrat, sans-serif'
                }
            };

            card = elements.create("card", { style: style });
            card.mount("#card-element");

            card.on('change', ({error}) => {
                const displayError = document.getElementById('card-errors');
                if (error) {
                    displayError.textContent = error.message;
                } else {
                    displayError.textContent = '';
                }
            });

            
            $('BUTTON#stripe-submit').click( function() {

               //     alert('ok');
                    //return false;

                    stripe.confirmCardPayment('{{ session('checkout_paymentIntent')->client_secret }}', {
                        payment_method: {
                        card: card,
                        billing_details: {
                            name: $('#cardholder').val(),
                        }
                        
                        }
                    }).then(function(result) {
                        if (result.error) {
                        // Show error to your customer (e.g., insufficient funds)
                            $('#paymentCurtain').remove();
                            console.log(result.error.message);
                        } else {
                        // The payment has been processed!
                        if (result.paymentIntent.status === 'succeeded') {

                            console.log(result);

                            window.location = '/basket/complete';
                            // Show a success message to your customer
                            // There's a risk of the customer closing the window before callback
                            // execution. Set up a webhook or plugin to listen for the
                            // payment_intent.succeeded event that handles any business critical
                            // post-payment actions.
                        }
                        }
                    });

            });
	  


        });




    </SCRIPT>

@endpush

<div class="formpanel">

    <x-cms-form-input type="text" name="cardholder" id="cardholder" label="Cardholder Name" value="{{old('cardholder', '')}}" wrapper="simple">
        The name exactly as it appears on the card
    </x-cms-form-inpt>

    <div id="card-element" class="stripe-card-wrap">
    </div>
    
    <div id="card-errors"></div>

    <button id="stripe-submit">Pay now</button>

    <div class="small p-2 mt-3 text-center">
        <p><a href="https://stripe.com" target="_blank"><img src="/img/stripe.svg" height="20" width="auto" alt="Powered by STRIPE" border="0"/></a></p>
        Your card details will be processed by Stripe. Essential Christian Songs do not have access to your payment details.
    </div>
    {{-- session('checkout_paymentIntent') --}}

</div>