<div>
    <H1 style="font-size: 2rem;">Your Basket</H1>


    

    <div class="grid" style="display: grid; gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr))">

        <div style="grid-column: 1 / span 2;">
    
            <div class="border bg-white p-5">

               
                @include('checkout::basket.livewire.contents')
                
                @if(!basket()->isEmpty)

                <div class="flex flex-between mt-3">

                    <form wire:submit.prevent="setCode(Object.fromEntries(new FormData($event.target)))">
                        <div class="input-group">
                        {{-- <input type="text" placeholder="Add Code..." /> --}}
                        <x-forms-fields-input type="text" name="code" value="" label="code" wrapper="none" placeholder="Enter Code" />
                        <div class="input-group-append">
                            <button class="btn btn-primary btn-sm">Apply</button>
                        </div>
                        </div>
                    </form>
                    
        
                    <div class="text-right">
                        <button wire:click="clear" class="button btn btn-secondary btn-sm">Clear Basket</button>
                    </div>

                </div>
                @endif
                {{-- @yield('basket.contentstable') --}}
            </div>

            @dump(basket()->id)
            @dump(basket()->toArray())
            @dump(basket()->address)
            @dump(basket()->customer)
            @dump(basket()->items)

            {{-- @dump(basket()->address()->first()) --}}

        </div>
    
    
        @if(!basket()->isEmpty)
        <div>
            
            @include('checkout::basket.livewire.sidebar')
         
        </div>
        @endif
    
    </div>


    @livewireScripts


</div>
