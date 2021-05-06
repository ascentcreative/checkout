@push('scripts')
<script>
$(document).on('basketUpdated', function(e) {
    
    $.get('/checkout/api/basket/summary')
    .done(function(data) {
        $('#checkout-basket-summary').html(data);
    }).fail(function(data) {
        console.log('unable to access basket data: ' + data);
    });
    
});
</script>
@endpush

<span id="checkout-basket-summary">{!! basket()->summary() !!}</span>
