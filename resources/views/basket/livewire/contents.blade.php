@if (basket()->isEmpty)
    <H2>Your basket is currently empty</H2>   
@else


<div id="basket-contents">

<table class="basket-table">
    <thead>
        <tr>
            <th>Item</th>
            @if (basket()->hasPhysicalItems())<th class="text-center"> Qty </th>@endif
            <th class="text-right">Price</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

        @foreach(basket()->items()->get()->groupBy('group_key')->sortBy(function($v,$k) { return $v[0]->offer_id; }) as $sku=>$items)
        
        <tr class='basket-item'>
            
            <td width="100%"> 
                <div class="flex flex-between flex-nowrap flex-align-center">
                    <A href="{{ $items[0]->sellable->url }}">{{ $items[0]->sellable->getItemName() }}</A> 
                    <div class="badge badge-secondary">{{ $items[0]->offer_alias }}</div>
                </div>
            
            </td>
            
            @if (basket()->hasPhysicalItems())
                <td class="text-center"> 
                    @if ($items[0]->sellable->isPhysical())
                        {{ count($items) }} 
                    @endif
                </td>
            @endif
            
            <td class="text-right">&pound;{{ number_format($items[0]->itemPrice * count($items), 2) }}</td>
            <td><A href="#" wire:click="removeByKey('{{ $items[0]->group_key }}')" class="bi-x-circle-fill xajax-link" data-response-target="#basket-contents"></A></td>
        </tr>
   
        @endforeach

    </tbody>

    <tfoot>
        @if($svc = basket()->shipping_service)
        <tr>
            <th class="text-right"
                @if (basket()->hasPhysicalItems())
                colspan="2"
                @endif
             >Shipping - {{ $svc->title ?? ''}}:</th>
            <th class="text-right">
                @if(($ship = $svc->cost) == 0)
                    FREE
                @else
                    &pound;{{ number_format($ship ?? 'FREE', 2) }}</th>
                @endif
            <th></th>
        </tr>
        @endif


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