@extends('cms::modal')

@section('modalTitle', "Basket Updated")

@section('modalContent')

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

<script>
    $(document).trigger('basketUpdated');
</script>

@endsection


@section('modalButtons')

<button type="button" data-dismiss="modal">Keep browsing</button>
<button type="button" onclick="location.href='/basket'">View Basket & Checkout</button>

@endsection