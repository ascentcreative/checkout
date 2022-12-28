@extends('cms::admin.base.show')


@push('scripts')

    <script>

        $(document).ready(function() {
            $('#myTab li:first-child a').tab('show');
        });

    </script>

@endpush


@section('showlayout')

<div class="cms-screenblock cms-screenblock-main bg-white rounded shadow" style="">
  
    <div class="container">

        <div class="row">

            <div class="col-12 col-md-6 col-lg-2 xrow">
                <div class="font-weight-bold">Order Num:</div>
                <div class="text-nowrap">{{ $model->orderNumber }}</div>
            </div>

            <div class="col-12 col-md-6 col-lg-2 rxow">
                <div class="font-weight-bold">Order Date:</div>
                <div class="text-nowrap"> {{ $model->formatOrderDate() }}</div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 rxow">
                <div class="font-weight-bold">Customer:</div>
                <div class="">{{ $model->customer->name}}<br/>({{ $model->customer->email}})</div>
            </div>

            <div class="col-12 col-md-6 col-lg-4 xrow">
                <div class="font-weight-bold">Payment Ref:</div>
                <div class="">{{ $model->transactionRef }}</div>
            </div>

        </div>

    </div>

</div>


<div class="cms-screenblock cms-screenblock-tabs bg-white rounded shadow" style="">

    <ul class="nav nav-tabs px-3 pt-3 bg-light" id="myTab" role="tablist">

        <li class="nav-item">
            <a class="nav-link" id="items-tab" data-toggle="tab" href="#items-pane" role="items" aria-controls="items-pane" aria-selected="false">Items</a>
          </li>

        @if($model->hasPhysicalItems())
       
        <li class="nav-item">
          <a class="nav-link" id="shipments-tab" data-toggle="tab" href="#shipments-pane" role="shipments" aria-controls="shipments-pane" aria-selected="false">Shipments</a>
        </li>

        @endif

       
      </ul>

      <div class="tab-content" id="tabs">

        <div class="tab-pane p-3" id="items-pane" role="tabpanel" aria-labelledby="items-tab">
           
    <table class="table table-hover">

        <thead>

            <th>Item</th>
            <th>Offers</th>
            <th width="100" class="text-right">Unit Price</th>
            <th width="100" class="text-right">Qty</th>
            <th width="100" class="text-right">Total</th>

        </thead>

        <tbody>

            @foreach($model->items->groupBy('group_key') as $key=>$items)

            <tr>
                <td class="font-weight-bold">{{ $items[0]->title }}</td>
                <td>{{ $items[0]->offer->first()->alias ?? '' }}</td>
                <td class="text-right">&pound;{{ number_format($items[0]->effectivePrice, 2) }}</td>
                <td class="text-right">{{ $qty = $items->sum('qty') }}</td>
                <td class="text-right">&pound;{{ number_format($items[0]->effectivePrice * $qty, 2) }}</td>
            </tr>

            @endforeach


            @foreach($model->offerUses()->get()->groupBy('offer_id') as $offer=>$uses)

                @php 
                    $offer = \AscentCreative\Offer\Models\Offer::find($offer); 
                    $format = new \NumberFormatter("en-GB", \NumberFormatter::CURRENCY);
                @endphp

                <tr>
                    <td></td>
                    <td>{{ $offer->alias ?? '' }}</td>
                    {{-- <td class="text-right">&pound;{{ number_format($items[0]->effectivePrice, 2) }}</td> --}}
                    {{-- <td class="text-right">{{ $qty = $items->sum('qty') }}</td> --}}
                    <td colspan="3" class="text-right">{{ $format->format($uses->sum('value')) }}</td>
                </tr>

            @endforeach

        {{-- </tbody>

        <tfoot> --}}


            @if($svc = $model->shipping_service)
            <tr class="basket-item">
                <th colspan="3" class="text-right">Shipping - {{ $svc->title ?? ''}}:</th>
                <th></th>
                <th class="text-right">
                    @if(($ship = $model->shipping_cost) == 0)
                        FREE
                    @else
                        &pound;{{ number_format($ship ?? 'FREE', 2) }}
                    @endif
                </th>
                
            </tr>
            @endif

            <tr>
                <th colspan="3" class="text-right">Total:</th>
                <th class="text-right">{{ $model->totalQuantity }}</th>
                <th class="text-right">&pound;{{ number_format($model->total, 2) }}</th>
            </tr>
           
            <tr>
                <th colspan="3" class="text-right font-weight-normal">Fees:</th>
                <th class="text-right"></th>
                <th class="text-right font-weight-normal">&pound;{{ number_format($model->fees, 2) }}</th>
            </tr>
       
            <tr>
                <th colspan="3" class="text-right font-weight-normal">Nett:</th>
                <th class="text-right font-weight-normal"></th>
                <th class="text-right font-weight-normal">&pound;{{ number_format($model->nett, 2) }}</th>
            </tr>

        </tfoot>



    </table>

        </div>

        @if($model->hasPhysicalItems())

        <div class="tab-pane p-3" id="shipments-pane" role="tabpanel" aria-labelledby="shipments-tab">

            {{-- @dump($model->countUnshippedItems()) --}}

            @if($model->hasUnshippedItems())
            <h4>Unshipped Items</h4>

            <table class="table table-hover">

                <thead>
        
                    <th>Item</th>
                    <th width="100" class="text-right">Qty</th>
                    
                </thead>

                <tbody>

                    @foreach($model->getUnshippedItems() as $item) 
                    <tr>
                        <td class="font-weight-bold">{{ $item->sellable->title }}</td>
                        <td class="text-right">{{ $item->qty }}</td>
                    </tr>
                    @endforeach

                </tbody>

            </table>
            @endif


            @foreach($model->shipments as $shipment) 

                <h4>{{ $shipment->shipping_date }}</h4>
                <div>{{ $shipment->shipper->name ?? '' }} @if($shipment->tracking_number) - Tracking: {{ $shipment->tracking_number }} @endif </div>

                <table class="table table-hover">

                    <thead>
            
                        <th>Item</th>
                        <th width="100" class="text-right">Qty</th>
                        
                    </thead>

                    <tbody>

                        @foreach($shipment->items as $item) 
                        <tr>
                            <td class="font-weight-bold">{{ $item->sellable->title }}</td>
                            <td class="text-right">{{ $item->qty }}</td>
                        </tr>
                        @endforeach

                    </tbody>

                </table>
            

            @endforeach


        </div>

        @endif
                

</div>

@endsection