<div class="centralise p-0">
    <H1 class="mb-4" style="font-size: x2rem;">Your Basket</H1>

    <div class="grid" style="display: grid; gap: 2rem; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr))">

        <div style="grid-column: 1 / span 2;">
    
            <div class="border bg-white p-3">
               
                @include('checkout::basket.livewire.contents')
                
                @if(!basket()->isEmpty)

                <div class="flex flex-between mt-3">


                    <div>
                        {{-- If there are offers with codes, show the code field: --}}
                        @if(\AscentCreative\Offer\Models\Offer::whereNotNull('code')->exists())

                            <form wire:submit.prevent="setCode(Object.fromEntries(new FormData($event.target)))">
                                <div class="input-group">
                                {{-- <input type="text" placeholder="Add Code..." /> --}}
                                <x-forms-fields-input type="text" name="code" value="" label="code" wrapper="none" placeholder="Enter Code" />
                                <div class="input-group-append">
                                    <button class="btn btn-primary btn-sm">Apply</button>
                                </div>
                                </div>
                            </form>

                        @endif

                    </div>
        
                    <div class="text-right">
                        <button wire:click="clear" class="button btn btn-secondary btn-sm">Clear Basket</button>
                        {{-- <button wire:click="update" class="button btn btn-secondary btn-sm">Update Basket</button> --}}
                    </div>

                </div>
                @endif
              
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
