@extends(config('checkout.global_blade')) 

@section('basket.pagetitle', 'Your Basket')

@section('basket.contentstable')

        <table width="100%">
            <thead>
                <tr>
                    <th>Item</th>
                    <th>Qty</th>
                    <th>Price</th>
                </tr>
            </thead>

            <tbody>

                @foreach(basket()->items()->get() as $item)
                <tr>
                    <td> <A href="{{ $item->sellable->url }}">{{ $item->sellable->getItemName() }}</A> </td>
                    <td> {{ $item->qty }} </td>
                    <td> {{ $item->sellable->getItemPrice() }} </td>
                </tr>
                @endforeach

            </tbody>

            <tfoot>
                <tr>
                    <th></th>
                    <th>Total:</th>
                    <th>&pound;{{ basket()->total }}</th>
                </tr>
            </tfoot>

            
        </table>    

        <a href="/basket/clear">Clear Basket</a>

      

       
@endsection



@section('basket.sidebar')

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

@endsection

