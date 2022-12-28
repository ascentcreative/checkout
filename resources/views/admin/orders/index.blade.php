@extends('cms::admin.base.index')


@section('indextable-head')

  
        <tr>
            
            <th class="text-nowrap" width="">Order&nbsp;#</th>

            <th class="text-nowrap">Status</th>

            <th class="text-nowrap">Date</th>

            <th width="">Customer</th>

            <th class="text-right">Items</th>

            <th class="text-right">Value</th>
            <th class="text-right">Fees</th>
            <th class="text-right">Nett</th>

            {{-- <th></th> --}}

           
          
        </tr>

@endsection

@section('indextable-body')
  
    
    @foreach ($models as $item)
    
        <tr class="indexitem">

            <td class="title text-nowrap"><a href="{{ action([controller(), 'show'], [$modelInject => $item->id]) }}">{{$item->orderNumber}}</a></td>

            <td class="text-nowrap">{{ $item->status }}</td>

            <td class="title text-nowrap">{{ $item->formatOrderDate('d F Y, H:i') }}</td>
            
            <td class="title" xxwidth="100%">{{ $item->customer->name }}</td>

            <td class="title text-right" xwidth="100%">{{ $item->items->sum('qty') }}</td>

            <td class="title text-right" >&pound;{{ number_format($item->total, 2) }}</a></td>

            <td class="title text-right" >&pound;{{ number_format($item->fees, 2) }}</a></td>

            <td class="title text-right" >&pound;{{ number_format($item->nett, 2) }}</a></td>

            <td width="0" align="right" style="width: 0 !important"> 
                {{-- <div class="btn-group dropleft"> --}}
                    <A class="dropdown-toggle dropdown-toggle-dots" href="#" id="rowactions" data-toggle="dropdown" ></A>
                    <div class="dropdown-menu dropdown-menu-right" style="" aria-labelledby="rowactions">
                        <a class="dropdown-item text-sm btn-delete modal-link" href="{{ action([controller(), 'resendConfirmation'], [$modelInject => $item->id]) }}">Resend Confirmation</a> 
                        <a class="dropdown-item text-sm btn-delete modal-link" href="{{ action([controller(), 'resendNotification'], [$modelInject => $item->id]) }}">Resend Notification</a> 

                        @if($item->hasUnshippedItems()) 
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item text-sm btn-delete modal-link" href="{{ route('checkout.orders.logshipment', ['order'=>$item]) }}">Log Shipment</a> 
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-sm btn-delete modal-link" href="{{ action([controller(), 'delete'], [$modelInject => $item->id]) }}">Delete</a> 
                        
                    </div>
              {{-- </div> --}}
            </td>

        </tr> 
     @endforeach

@endsection