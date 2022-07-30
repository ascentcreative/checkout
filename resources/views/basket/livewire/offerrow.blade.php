@php 
    $offer = AscentCreative\Offer\Models\Offer::find($offer);
    $format = new \NumberFormatter("en-GB", \NumberFormatter::CURRENCY);
@endphp

<tr class='basket-item'>
                
    <td colspan="@if(basket()->hasPhysicalItems()) 2 @else 1 @endif " width="100%" class="text-right"> 
            <div class="badge badge-secondary">{{ $offer->alias }}</div>
    </td>
   
    
    <td class="text-right text-nowrap">{{ $format->format($uses->sum('value')) }}</td>
    <td>
        @if($offer->code)
            <A href="#" wire:click="removeCode('{{ $offer->code }}')" class="bi-x-circle-fill xajax-link" data-response-target="#basket-contents"></A>
       @endif

    </td>
</tr>