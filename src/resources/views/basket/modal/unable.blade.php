@extends('cms::modal')

@section('modalTitle', "")

@section('modalContent')

    You already have that item in your basket.    

@endsection


@section('modalButtons')

<button type="button" data-dismiss="modal">Keep browsing</button>
<button type="button" onclick="location.href='/basket'">View Basket & Checkout</button>

@endsection