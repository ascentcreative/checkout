

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
        @php $sellable = $item->sellable()->withUnpublished()->first(); @endphp
        <tr class='basket-item'>
            <td width="100%">
                @if($sellable = $item->sellable)
                    <A href="{{ $sellable->url }}">
                @endif
                {{ $item->title }}
                @if($sellable = $item->sellable)
                    </A>
                @endif
            </td>
            <td> 
                @if($sellable->isPhysical())
                    {{ $item->qty }} 
                @endif
            </td>
            <td class="text-right">&pound;{{ number_format($item->itemPrice, 2) }}</td>
            <td>
                @if ($sellable->isDownload())
                    <A href="{{ $item->getDownloadUrl() }}" class="bi-cloud-arrow-down-fill xmodal-link" style="font-size: 2rem; line-height: 1rem;" data-toggle="tooltip" title="Download File"></A>
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