<?php

namespace AscentCreative\Checkout\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class Checkout extends Component
{

    public $current_tab = 'details';

    public $tab_status = [
        'details'=>'incomplete',
        'shipping'=>'incomplete',
        'payment'=>'incomplete'
    ];

    public $country;

    public function mount() {
        if(!basket()->hasPhysicalItems()) {
            unset($this->tab_status['shipping']);
        }
    }

    public function setDetails($data) {

        // validate:
        Validator::make($data, [
            'name'=>'required',
            'email'=>'required|email',
        ])->validate();

        // populate info on basket:
        basket()->customer->name = $data['name'];
        basket()->customer->email = $data['email'];
        basket()->customer->save();

        // set the next tab:
        $this->tab_status['details'] = 'complete';

        $this->current_tab = 'payment';

    }

    public function setShipping($data) {

        // set the next tab:
        $this->tab_status['shipping'] = 'complete';

        $this->current_tab = 'payment';

    }


    public function setCurrentTab($tab) {
        $this->current_tab = $tab;
    }

    public function clear() {
        basket()->clear();
    }

    public function remove($uuid) {
        basket()->remove($uuid);
    }

    public function render()
    {
        return view('checkout::livewire.checkout');
    }
}
