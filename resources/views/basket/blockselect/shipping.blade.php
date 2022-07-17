<div class="d-flex flex-between">
    <div class="text-left">
        <div>{{ $option->title }}</div>
        <div class="small">{{ $option->description }}</div>
    </div>
    <div>
        @if($option->cost == 0) 
            FREE
        @else 
            £{{ number_format($option->cost, 2) }}
        @endif
    </div>
</div>