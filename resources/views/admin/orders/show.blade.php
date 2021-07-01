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

<div class="cms-screenblock bg-white rounded shadow" style="">

    <table class="table table-hover">

        <thead>

            <th>Item</th>
            <th width="100" class="text-right">Unit Price</th>
            <th width="100" class="text-right">Qty</th>
            <th width="100" class="text-right">Total</th>

        </thead>

        <tbody>

            @foreach($model->items as $item)

            <tr>
                <td>{{ $item->title }}</td>
                <td class="text-right">&pound;{{ number_format($item->purchasePrice, 2) }}</td>
                <td class="text-right">{{ $item->qty }}</td>
                <td class="text-right">&pound;{{ number_format($item->purchasePrice * $item->qty, 2) }}</td>
            </tr>

            @endforeach

        </tbody>

        <tfoot>
            <th colspan="2" class="text-right">Total:</th>
            <th class="text-right">{{ $model->totalQuantity }}</th>
            <th class="text-right">&pound;{{ number_format($model->total, 2) }}</th>
        </tfoot>

    </table>

</div>

@endsection