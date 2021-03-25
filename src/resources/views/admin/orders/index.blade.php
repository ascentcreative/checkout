@extends('cms::admin.base.index')


@section('indextable-head')

  
        <tr>
            
            <th class="text-nowrap" width="">Order&nbsp;#</th>

            <th class="text-nowrap">Date</th>

            <th width="">Customer</th>

            <th class="text-right">Items</th>

            <th class="text-right">Value</th>

            {{-- <th></th> --}}

           
          
        </tr>

@endsection

@section('indextable-body')
  
    
    @foreach ($models as $item)
    
        <tr class="indexitem">

            <td class="title text-nowrap"><a href="{{ action([controller(), 'show'], [$modelInject => $item->id]) }}">{{$item->orderNumber}}</a></td>

            <td class="title text-nowrap">{{ $item->formatOrderDate('d F Y, H:i') }}</td>
            
            <td class="title" xxwidth="100%">{{ $item->customer->name }}</td>

            <td class="title text-right" xwidth="100%">{{ $item->items->count() }}</td>

            <td class="title text-right" >&pound;{{$item->total}}</a></td>

            <td width="0" align="right" style="width: 0 !important"> 
                <div class="btn-group dropleft">
                    <A class="dropdown-toggle dropdown-toggle-dots" href="#" data-toggle="dropdown" ></A>
                    <div class="dropdown-menu dropdown-menu-right" style="">
                        <a class="dropdown-item text-sm btn-delete modal-link" href="{{ action([controller(), 'delete'], [$modelInject => $item->id]) }}">Delete</a> 
                    </div>
              </div>
            </td>

        </tr> 
     @endforeach

@endsection