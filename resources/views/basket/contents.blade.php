@if (basket()->isEmpty)
    <H2>Your basket is currently empty</H2>   
@else

<div id="basket-contents">

<table class="basket-table">
    <thead>
        <tr>
            <th>Item</th>
            @if (basket()->hasPhysicalItems())<th> Qty </th>@endif
            <th class="text-right">Price</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

        @foreach(basket()->items()->get() as $item)
        <tr class='basket-item'>
            
            <td width="100%"> <A href="{{ $item->sellable->url }}">{{ $item->sellable->getItemName() }}</A> </td>
            
            @if (basket()->hasPhysicalItems())
                <td> 
                    @if ($item->sellable->isPhysical())
                        {{ $item->qty }} 
                    @endif
                </td>
            @endif
            
            <td class="text-right">&pound;{{ number_format($item->purchasePrice, 2) }}</td>
            <td><A href="/basket/remove/{{$item->uuid}}" class="bi-x-circle-fill ajax-link" data-response-target="#basket-contents"></A></td>
        </tr>
        @endforeach

    </tbody>

    <tfoot>
        <tr>
            @if (basket()->hasPhysicalItems())
                <th></th>
            @endif
            <th class="text-right" >Total:</th>
           
            <th class="text-right">&pound;{{ number_format(basket()->total, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>

    
</table>    

</div>

@endif