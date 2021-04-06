@extends(config('checkout.global_blade')) 

@section('basket.pagetitle', 'Your Basket')

@section('basket.contentstable')

        <H2>Your basket is currently empty</H2>      

@endsection



@section('basket.sidebar')

{{-- if the site requires a login to checkout, display a login  / register form which redirects back to the basket --}}
@if(!@config('checkout.anonymous_checkout'))

@else
{{-- otherwise, just show the checkout blocks --}}

@endif

@endsection

