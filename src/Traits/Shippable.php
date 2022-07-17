<?php

namespace AscentCreative\Checkout\Traits;

use AscentCreative\Checkout\Models\Shipping\QuantityBand;
use AscentCreative\Checkout\Models\Shipping\Group;
use AscentCreative\Checkout\Models\Shipping\GroupItem;
use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\Shipping\ServicePermission;

trait Shippable {

    public function quantityShippingBands() {

        return $this->morphMany(QuantityBand::class, 'shippable');

    }

    public function getShipperAttribute() {

        // if($this->has('shippinggroup')) {
            return $this->shippinggroup ?? $this;
        // } else {
            // return $this;
        // }
        
    }

    public function getShipperKeyAttribute() {
        return get_class($this) . '_' . $this->id;
    }

    public function getShippingGroupAttribute() {
        return $this->morphToMany(Group::class, 'shippable', 'checkout_shipping_groupitems', null,  'shippinggroup_id')->first();
    }

    public function servicepermissions(Service $svc, $action=null) {
        $q = $this->morphMany(ServicePermission::class, 'shippable')
                    ->where('service_id', $svc->id);

        if($action) {
            $q->where('action', $action);
        }

        return $q;
                    
    }


    public function getAllowedSubservicesAttribute(Service $svc) {
       
        // if there are none, return all
        $explicit_allow = $this->servicepermissions($svc, 'allow')->get();

        // dump($explicit_allow);

        $explicit_deny = $this->servicepermissions($svc, 'deny')->get();

        // dd($explicit_deny);

        if($explicit_allow->count() > 0) {
            return $svc->subservices()
                        ->whereIn('id', $explicit_allow->pluck('subservice_id'))->get();
        } else {
            return $svc->subservices()
                        ->whereNotIn('id', $this->servicepermissions($svc, 'deny')->get()->pluck('subservice_id'))->get();
        }
        

        // return $svc->subservices();
                    //->whereIn('')      

    }

}