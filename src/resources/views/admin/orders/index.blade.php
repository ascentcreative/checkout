@extends('cms::admin.base.index')


@section('indextable-head')

  
        <tr>
            
            <th width="">Order&nbsp;#</th>

            <th width="">Customer</th>


            <th width="">Value</th>

            <th></th>

           
          
        </tr>

@endsection

@section('indextable-body')
  
    
    @foreach ($models as $item)
    
        <tr class="indexitem">

            <td class="title" ><a href="{{ action([controller(), 'edit'], [$modelInject => $item->id]) }}">{{$item->id}}</a></td>
            
            <td class="title" width="100%">{{ $item->customer->name }}</td>

            <td class="title" >&pound;{{$item->total}}</a></td>

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