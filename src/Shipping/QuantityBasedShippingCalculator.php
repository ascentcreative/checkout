<?php

namespace AscentCreative\Checkout\Shipping;

use AscentCreative\Checkout\Contracts\ShippingCalculator;

use AscentCreative\Checkout\Models\Shipping\WeightBand;
use AscentCreative\Checkout\Models\Shipping\Service;
use AscentCreative\Checkout\Models\OrderBase;

class QuantityBasedShippingCalculator implements ShippingCalculator {


    static function getCost(Service $svc, OrderBase $basket) {

        // ?Revised code
        // feels like we should get the list of the codes for the bands (whether fixed or SKU-based)
        // and then calculate them all. 

        // Other big question is whether we should be doing an incremental cost (amount to add per item),
        // or simple banding (return the price for that qty). Incremental is better for data entry, I think...
        // Once the code is written, we can stick with it.

        // Also need to remember that we may need to filter what services / subservices are applicable to the items.
        // Ideally, we'd do that at the basket level rather than here with some form of scope.



        // do we ditch the idea of SKUs for this, and make it more explicit? 
        // - use product groups (which can be arbitrary)
        // - also allow a model to customise its relationship? (i.e. PrintInstance to actually get the bands for the print spec, whereas most models would be linked directly or via a group).

        // each model must only be in one group. 

        // this feels like a more user-friendly way to approach it - rather than having people need to enter working regexes (or even assuming SKUs will be regexable)


        // also doesn't need to worry about subservices (necessarily) as that's more to do with weight boundaries.
        // (although, those could be kept as informational - i.e. once a certain band is hit, the size would make it a parcel etc)

        // return null;
        $total = null;

       
        // get the 'shipper' for each item as this is the key for the actual bands.
        $shippers = $basket->items()->with('sellable')->get()
                        ->where('sellable.itemWeight', '=', 0)
                        ->groupBy('sellable.shipper.shipperKey')
                        ->map(function($items) {
                            return [
                                'shipper' => $items[0]->sellable->shipper,
                               'qty' => $items->sum('qty'),
                            ];
                        });
                        
         // if there are no items which used qty bands, return 0 (not null);
        if ($shippers->count() == 0) {
            return 0;
        }

        // ok, we now have an array of the classes linked to the bands, and the Qty for each one.

        foreach($shippers as $group) {

            // get the bands for this service:
            $bands = $group['shipper']->quantityShippingBands()
                                ->where('minQty', '<=', $group['qty'])
                                ->where('service_id', $svc->id)
                                ->orderBy('minQty', 'asc')->get();

                // dump($bands);
            if($bands->count() > 0) {
                for($iQty = 1; $iQty <= $group['qty']; $iQty++) {
                   $total += $bands->where('minQty', '<=', $iQty)->last()->cost_each ?? 0;
                }
            }
        

        }


        return $total;


        // initial version::

        $total = 0;
        // get all the non-weighted items from the basket

        // group by SKU (and total qty for each)
        //  NB - need to use the group by on the fetched collection, 
        //  not the query, in case the models have an accessor for the sku rather than a column
        $skus = $basket->items()->with('sellable')->get()->groupBy('sellable.sku');
        $skuAndQty = $skus->map(function($items) {
            return [
                'sellable' => $items[0]->sellable,
                'qty' => $items->sum('qty'),
            ];
        });

        dd($skus);


        // hmm... need to group based on the patterns matched...

        // maybe...:
        // - get direct matches first (via shippable - links a single model instance to a rate.
        // this will be a straight query based on the number of that item in the basket.
        foreach($skuAndQty as $sku=>$item) {

            $bands = $item['sellable']->quantityShippingBands()
                        ->where('minQty', '<=', $item['qty'])
                        ->orderBy('minQty', 'asc')->get();

            // dump($item['sellable'] . ' : ' . $item['qty']);

            for($iQty = 1; $iQty <= $item['qty']; $iQty++) {
                // dump($iQty);
                // dump($bands->where('minQty', '<=', $iQty)->last()->cost_each ?? 0);
                $total += $bands->where('minQty', '<=', $iQty)->last()->cost_each ?? 0;
                // dump($total);
            }

           

        }

        // dd($total);

        // - then search against wildcards if not matched.
        // - what if it matches multiple? Old version just takes the first. Do that for now.

        // - Store the pattern as the key, and increment the total qty.

        // (loop to next item)

        //  - after loop finished, query for wildcard/pattern matched totals and add to shipping total.



        // return final cost
       return $total;
        



    }

}