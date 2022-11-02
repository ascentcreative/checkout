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

        @foreach(basket()->items()->get()->groupBy('sku') as $item)
        <tr class='basket-item'>
            
            {{-- @dd($item) --}}
            <td width="100%"> <A href="{{ $item[0]->sellable->url }}">{{ $item[0]->sellable->getItemName() }}</A> </td>
            
            @if (basket()->hasPhysicalItems())
                <td> 
                    @if ($item[0]->sellable->isPhysical())
                        {{ count($item) }} 
                    @endif
                </td>
            @endif
            
            <td class="text-right">&pound;{{ number_format($item[0]->itemPrice, 2) }}</td>
            <td><A href="/basket/remove/{{$item[0]->uuid}}" class="bi-x-circle-fill ajax-link" data-response-target="#basket-contents"></A></td>
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