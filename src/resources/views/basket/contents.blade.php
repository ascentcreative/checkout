@if (basket()->isEmpty)
    <H2>Your basket is currently empty</H2>   
@else

<div id="basket-contents">

<table class="basket-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>@if (basket()->hasPhysicalItems()) Qty @endif</th>
            <th class="text-right">Price</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

        @foreach(basket()->items()->get() as $item)
        <tr class='basket-item'>
            <td width="100%"> <A href="{{ $item->sellable->url }}">{{ $item->sellable->getItemName() }}</A> </td>
            
            <td> 
                @if ($item->sellable->isPhysical())
                    {{ $item->qty }} 
                @endif
            </td>
            <td class="text-right">&pound;{{ number_format($item->purchasePrice, 2) }}</td>
            <td><A href="/basket/remove/{{$item->uuid}}" class="bi-x-circle-fill ajax-link" data-response-target="#basket-contents"></A></td>
        </tr>
        @endforeach

    </tbody>

    <tfoot>
        <tr>
            <th></th>
            <th class="text-right" >Total:</th>
            <th class="text-right">&pound;{{ basket()->total }}</th>
            <th></th>
        </tr>
    </tfoot>

    
</table>    

</div>

@endif