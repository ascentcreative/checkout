

<table class="table basket-table">
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
                    @if($sellable->is_published)
                        <A href="{{ $sellable->url }}">
                    @endif
                @endif
                {{ $item->title }}
                @if($sellable->is_published)
                    </A>
                @endif
            </td>
            <td> 
                @if($sellable->isPhysical())
                    {{ $item->qty }} 
                @endif
            </td>
            <td class="text-right">&pound;{{ number_format($item->itemPrice * $item->qty, 2) }}</td>
            <td>
                @if ($sellable->isDownload() && $sellable->is_published)
                    <A href="{{ $item->getDownloadUrl() }}" class="bi-cloud-arrow-down-fill xmodal-link" style="font-size: 1.5rem; line-height: 1rem;" data-toggle="tooltip" title="Download File"></A>
                @endif
            </td>
        </tr>
        @endforeach

    </tbody>

    <tfoot>
        <tr>
            <th colspan="2" class="text-right font-weight-bold">Item Total:</th>
            <th class="text-right font-weight-bold">&pound;{{ number_format($order->itemTotal, 2) }}</th>
            <th></th>
        </tr>
        @if($order->hasPhysicalItems())
        <tr>
            <th colspan="2" class="text-right">
                Shipping:
                @if($svc = $order->shipping_service) 
                {{ $svc->title ?? ''}}
               @endif

            </th>
            <th class="text-right">&pound;{{ number_format($order->shipping_cost, 2) }}</th>
            <th></th>
        </tr>
        @endif
        <tr>
            <th colspan="2" class="text-right font-weight-bold">Total:</th>
            <th class="text-right font-weight-bold">&pound;{{ number_format($order->total, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>

    
</table>    