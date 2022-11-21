<?php

namespace AscentCreative\Checkout\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Validator;

use Illuminate\Support\Arr;

use AscentCreative\Checkout\Models\Shipping\Service;

class Checkout extends Component
{

    public $current_tab = 'details';
// 
    // public $current_tab = 'shipping';
    

    public $tab_status = [
        'details'=>'incomplete',
        'shipping'=>'incomplete',
        'payment'=>'incomplete'
    ];

    public $country = ''; //1188';;
    public $shipping_service = null;

    public $country_id;

    public $address = [
        'country_id'=>null,
    ];


    protected $listeners = ['setShippingService'];

    public function mount() {
        if(!basket()->hasPhysicalItems()) {
            unset($this->tab_status['shipping']);
        }
    }

    public function setCode($data) {
        basket()->setCode($data['code']);
    }

    public function setDetails($data) {

        // validate:
        Validator::make($data, [
            'name'=>'required',
            'email'=>'required|email',
        ])->validate();

        // populate info on basket:
        if(!basket()->customer instanceof \App\Models\User) {
            basket()->customer->update([
                'name'=>$data['name'],
                'email'=>$data['email']
            ]);
        }

        // dd($basket()->customer)

        // set the next tab:
        $this->tab_status['details'] = 'complete';

        $this->current_tab = 'shipping';

    }

    public function setShippingCountry($data) {
       
        $this->country = $data;
        // dump(basket()->id);
        // dd(basket()->address);

        $addr = basket()->address()->first();
        $addr->country_id = $data;
        // ->country_id = $data;
        basket()->address()->save($addr);

    }

    public function setShippingService($svc) {
        // dump($svc);
        basket()->shipping_service()->associate(Service::find($svc));
        basket()->save();

    }

    public function setShipping($data) {
      
        foreach ($data as $key => $value) {
            $key = str_replace(['[', ']'], ['.',''], $key);
            Arr::set($data, $key, $value);
        }

        $rules = [
            'address.country_id'=>'required',
            'shipping_service_id'=>'required'
        ];

        if(basket()->needs_address) {

            $rules = array_merge(
                $rules,
                [
                    'address.street1'=>'required'
                ]
                );

        }

         // validate:
         Validator::make($data,$rules)->validate();

        basket()->address->fill($data['address']);
        basket()->address->save();
        

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

    public function removeByKey($key) {
        basket()->removeByKey($key);
    }

    public function render()
    {
        $this->dispatchBrowserEvent('basketUpdated');
        return view('checkout::livewire.checkout');
    }
}
