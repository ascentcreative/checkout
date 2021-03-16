@push('scripts')
<script>
$(document).on('basketUpdated', function(e) {
    console.log('update event captured');
    $.get('/checkout/api/basket/countAll')
    .done(function(data) {
        $('#checkout-basket-count').html(data);
    }).fail(function(data) {
        console.log('unable to access basket data: ' + data);
    });
    
});
</script>
@endpush

<span id="checkout-basket-count">{{ basket()->countAll() }}</span>