<div class="basket-tab border bg-white p-3 mb-3" id="tab-details">
@if(config('checkout.anonymous_checkout'))

    <div class="flex flex-between">
    <H4>Your Details</H4>  
        @if($current_tab != 'details' && $tab_status['details'] == 'complete')
            <a href="#" wire:click="setCurrentTab('details')" class="">Edit</a>
        @endif
    </div>
    @if(($current_tab == 'details'))
        
        <form wire:submit.prevent="setDetails(Object.fromEntries(new FormData($event.target)))">

            {!! prevent_submit_on_enter() !!}

            <x-cms-form-input type="text" name="name" label="Name:" wrapper="simple" value="{{ basket()->customer->name ?? '' }}" />
            
            <x-cms-form-input type="text" name="email" label="Email address:" wrapper="simple" value="{{ basket()->customer->email ?? '' }}" />

            <x-cms-form-checkbox name="email_signup" label="Signup for email newsletters?" value="{{ 1 }}" uncheckedValue="0" wrapper="inline" labelPlacement="after" />

            <div class="text-right">
                <button class="btn button btn-primary btn-sm">Next</button>
            </div>
            
        </form>

    @else 

        <table>
            <tr>
                <td>Name:</td><td>{{ basket()->customer->name }}</td>
            </tr>
            <tr>
                <td>Email:</td><td>{{ basket()->customer->email }}</td>
            </tr>

        </table>

    @endif

@else 

    {{-- Need user to login --}}
    Login

@endif
</div>

@if(basket()->hasPhysicalItems()) 

    <div class="basket-tab border bg-white p-3 mb-3">

        <div class="flex flex-between">
            <H4>Shipping Information</H4>
            @if($current_tab != 'shipping' && $tab_status['shipping'] == 'complete')
                <a href="#" wire:click="setCurrentTab('shipping')" class="">Edit</a>
            @endif
        </div>

        @if(($current_tab == 'shipping'))

            <form wire:submit.prevent="setShipping(Object.fromEntries(new FormData($event.target)))">

                {!! prevent_submit_on_enter() !!}

                {{-- Country Selector --}}
                <x-cms-form-foreignkeyselect type="select" label="Select your country" labelField="name" name="_address[country_id]" 
                    :query="\AscentCreative\Geo\Models\Country::query()" wrapper="inline">
                    <x-slot name="attr">
                        wire:change="setShippingCountry"
                    </x-slot>
                </x-cms-form-foreignkeyselect>

                {{-- Shipment Type --}}
                @if($country)

                    SHIPPING QUOTES...

                @endif


                {{-- Address (if needed) --}}


                {{-- 
                <div class="text-right">
                    <button class="btn button btn-primary btn-sm">Next</button>
                </div>
                 --}}
            </form>

        @else

            Shipping Info....

        @endif

    </div>
@endif

<div class="basket-tab border bg-white p-3 mb-3">

    <H4>Payment</H4>

    {{-- @if(($current_tab == 'payment')) --}}
    <div class="@if(($current_tab == 'payment')) d-block @else d-none @endif">
        <div wire:ignore>
            {{-- @include('checkout::payment.' . config('transact.payment_provider')) --}}
            <x-transact-stripe-ui id="stripe-ui" />
        </div>
    </div>
    {{-- @endif --}}

</div>

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