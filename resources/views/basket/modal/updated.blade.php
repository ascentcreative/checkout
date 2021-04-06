@extends('cms::modal')

@section('modalTitle', "Basket Updated")

@php
$modalShowFooter = true;
@endphp

@section('modalContent')

    @include('checkout::basket.contents')

<script>
    $(document).trigger('basketUpdated');
</script>

@endsection


@section('modalButtons')

<button type="button" data-dismiss="modal">Keep browsing</button>
<button type="button" onclick="location.href='/basket'">View Basket & Checkout</button>

@endsection