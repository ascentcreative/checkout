@extends(config('checkout.basket_blade')) 

@push('styles')

@endpush

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

            @include('checkout::payment.' . config('checkout.payment_provider'))


        @endif

    @endif

@endsection

