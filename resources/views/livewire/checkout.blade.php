<div>
    <H1 style="font-size: 2rem;">Your Basket</H1>

    <div class="grid" style="display: grid; gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))">


        <div style="grid-column: 1 / span 2;">
    
            <div class="border bg-white p-5">

                @include('checkout::basket.livewire.contents')
                
                @if(!basket()->isEmpty)
                <div class="text-right mt-3">
                    <button wire:click="clear" class="button btn btn-secondary btn-sm">Clear Basket</button>
                </div>
                @endif
                {{-- @yield('basket.contentstable') --}}
            </div>

        </div>
    
    
        @if(!basket()->isEmpty)
        <div>
            
            @include('checkout::basket.livewire.sidebar')
         
        </div>
        @endif
    
    </div>


    @livewireScripts


</div>
