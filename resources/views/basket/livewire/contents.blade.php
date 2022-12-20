@if (basket()->isEmpty())
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

        {{-- @foreach(basket()->items()->groupBy('group_key')->sortBy(function($v,$k) { return $v[0]->offer_id; }) as $sku=>$items) --}}
        @foreach(basket()->items()->groupBy('sku')->sortBy('sku') as $sku=>$items)

        <tr class='basket-item'>
            
            <td width="100%"> 
                <div class="flex flex-between flex-nowrap flex-align-center">
                    <A href="{{ $items[0]->sellable->url }}">{{ $items[0]->sellable->getItemName() }}</A> 
                </div>
            
                @php
                    $offers = $items->pluck('offer_id')->unique()->filter();
                    // dump($offers);
                @endphp
                @foreach($offers as $offer) 
                    <div class="badge badge-secondary">{{ AscentCreative\Offer\Models\Offer::find($offer)->alias }}</div>
                @endforeach

            </td>
            
            @if (basket()->hasPhysicalItems())
                <td class="text-center"> 
                    @if ($items[0]->is_physical)
                        <input class="form-control text-center" style="width: 60px" wire:change="updateQty('{{ $items[0]->sku }}', $event.target.value)" value="{{ ($items->sum('qty')) }}" />
                    @endif
                </td>
            @endif

            <td class="text-right">
              
                &pound;{{ number_format($items->sum('itemPrice'), 2) }}
                @if(count($offers) > 0) <div class="text-muted small text-strikeout"><del>&pound;{{ number_format($items->sum('original_price'), 2) }}</del></div> @endif
            </td>
            {{-- <td class="text-right">&pound;{{ number_format($items[0]->itemPrice * count($items), 2) }}</td> --}}
            {{-- <td class="text-right">&pound;{{ number_format($items[0]->itemPrice * $items->sum('qty'), 2) }}</td> --}}
            <td><A href="#" wire:click.prevent="updateQty('{{ $items[0]->sku }}', 0)" class="bi-x-circle-fill"></A></td>
        </tr>
   
        @endforeach

        {{-- @foreach(basket()->itemOfferUses()->get()->groupBy('offer_id') as $offer=>$uses)

            @include('checkout::basket.livewire.offerrow')

        @endforeach --}}

        <tr class="basket-item">
            
            <th class="text-right" @if (basket()->hasPhysicalItems()) colspan="2" @endif >Item Total:</th>
            <td class="text-right font-weight-bold">&pound;{{ number_format(basket()->itemTotal(), 2) }}</td>
            <th></th>

        </tr>

    </tbody>

    <tfoot>
        @if($svc = basket()->getShippingService())
        <tr class="basket-item">
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
           
            <th class="text-right">&pound;{{ number_format(basket()->totalValue(), 2) }}</th>
            <th></th>
        </tr>
    </tfoot>

    <tbody>

{{-- 
        @foreach(basket()->offerUses()->get()->groupBy('offer_id') as $offer=>$uses)

           @include('checkout::basket.livewire.offerrow')

        @endforeach --}}

    </tbody>

    
</table>    

</div>

@endif


<script>
    document.addEventListener("DOMContentLoaded", () => {
        Livewire.hook('component.initialized', (component) => {})
        Livewire.hook('element.initialized', (el, component) => {})
        Livewire.hook('element.updating', (fromEl, toEl, component) => {})
        Livewire.hook('element.updated', (el, component) => {})
        Livewire.hook('element.removed', (el, component) => {})
        Livewire.hook('message.sent', (message, component) => {
            $('#basket-contents').addClass('basket-updating');
            console.log('starting basket update');
            // alert('sending');
        })
        Livewire.hook('message.failed', (message, component) => {
            // alert('fail');
        })
        Livewire.hook('message.received', (message, component) => {
            // alert('recv');
        })
        Livewire.hook('message.processed', (message, component) => {
            $('#basket-contents').removeClass('basket-updating');
            console.log('basket update complete');
            // alert('proc');
        })
    });
</script>