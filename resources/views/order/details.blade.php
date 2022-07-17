

<table class="basket-table">
    <thead>
        <tr>
            <th>Item</th>
            <th>@if ($order->hasPhysicalItems()) Qty @endif</th>
            <th class="text-right">Price</th>
            <th></th>
        </tr>
    </thead>

    <tbody>

        @foreach($order->items()->get() as $item)
        <tr class='basket-item'>
            <td width="100%"> <A href="{{ $item->sellable->url }}">{{ $item->sellable->getItemName() }}</A> </td>
            
            <td> 
                @if ($item->sellable->isPhysical())
                    {{ $item->qty }} 
                @endif
            </td>
            <td class="text-right">&pound;{{ number_format($item->itemPrice, 2) }}</td>
            <td>
                @if ($item->sellable->isDownload())
                    <A href="{{ $item->getDownloadUrl() }}" class="bi-cloud-arrow-down-fill modal-link" style="font-size: 2rem; line-height: 1rem;" data-toggle="tooltip" title="Download File"></A>
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>

    <tfoot>
        <tr>
            <th></th>
            <th class="text-right" >Total:</th>
            <th class="text-right">&pound;{{ $order->total }}</th>
            <th></th>
        </tr>
    </tfoot>

    
</table>    