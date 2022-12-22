<div class="basket-tab border bg-white p-3 mb-3" id="tab-details">
@if(config('checkout.anonymous_checkout') || auth()->user())

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

            {{-- <x-cms-form-checkbox name="email_signup" label="Signup for email newsletters?" value="{{ 1 }}" uncheckedValue="0" wrapper="inline" labelPlacement="after" /> --}}

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

    {{-- Anonymous Checkout disallowed and no current user - need user to login --}}
    @include('auth.loginform')

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

        {{-- @include("checkout:basket.blockselect.shipping") --}}
        @if(($current_tab == 'shipping'))

            <form wire:submit.prevent="setShipping(Object.fromEntries(new FormData($event.target)))">

                {!! prevent_submit_on_enter() !!}

                {{-- Country Selector --}}
                <x-cms-form-foreignkeyselect type="select" label="Select your country" labelField="name" name="address[country_id]" 
                    :query="\AscentCreative\Geo\Models\Country::orderBy('is_common', 'desc')" 
                    :value="basket()->getShippingAddress()->country_id ?? ''" wrapper="simple">
                    <x-slot name="attr">
                        wire:change="setShippingCountry($event.target.value)"
                    </x-slot>
                </x-cms-form-foreignkeyselect>

            
                {{-- Shipment Type --}}
                @if(basket()->getShippingAddress()->country_id)

                    @php 
                        $quotes = basket()->getShippingQuotes();
                        // if there's only one quote, select it.
                        if(count($quotes) == 1) {
                            basket()->setShippingService($quotes[0]);
                        } 
                    @endphp

                    <x-cms-form-blockselect :readonly="true" name="shipping_service_id" label="Choose Shipping Method:" 

                        :value="basket()->getShippingService()->id ?? ''"
                        :options="$quotes"
                        blockblade="checkout::basket.blockselect.shipping"
                        optionKeyField="id"
                        maxSelect="1" wrapper="simple" columns="1">

                    </x-cms-form-blockselect>
      
                @endif

                @if(basket()->needsAddress())
                    {{-- Address (if needed) --}}
                    <label>Shipping Address:</label>
                    @php $addr = basket()->getShippingAddress(); @endphp
                    <x-cms-form-input type="text" name="address[street1]" label="" placeholder="Address line 1" :value="$addr->street1 ?? ''" wrapper="simple"/>
                    <x-cms-form-input type="text" name="address[street2]" label="" placeholder="Address line 2" :value="$addr->street2 ?? ''" wrapper="simple"/>
                    <x-cms-form-input type="text" name="address[city]" label="" placeholder="City" :value="$addr->city ?? ''" wrapper="simple"/>
                    <x-cms-form-input type="text" name="address[state]" label="" placeholder="County" :value="$addr->state ?? ''" wrapper="simple"/>
                    <x-cms-form-input type="text" name="address[zip]" label="" placeholder="Postcode / Zip" :value="$addr->zip ?? ''" wrapper="simple"/>

                @endif

                <div class="text-right mt-2">
                    <button class="btn button btn-primary btn-sm">Next</button>
                </div>
                
            </form>

        @else

            {{-- Shipping Info.... --}}

            {{ basket()->getShippingAddress()->stringify(', ') }}

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

    $(document).on('change', 'input[name="shipping_service_id"]', function(e) {
        Livewire.emit('setShippingService', $('input[name="shipping_service_id"]:checked').val());
        console.log('shipping:' + e.target.value);
    });

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
        window.location = '/basket/orderconfirmed/{{ basket()->uuid }}';
    });
    


    // a function to run when the payment completes
//     $('stripe-ui').onSuccess() {

//     }

//     $('stripe-ui').onFail() {

//      }
   

   

</SCRIPT>
@endpush