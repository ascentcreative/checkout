<?php
namespace AscentCreative\Checkout\Forms\Admin\Modal;

use AscentCreative\CMS\Forms\Admin\BaseForm;
use AscentCreative\Forms\Fields\Input;
use AscentCreative\Forms\Fields\Checkbox;
use AscentCreative\Forms\Fields\CompoundDate;
use AscentCreative\Forms\Fields\ForeignKeySelect;
use AscentCreative\CMS\Forms\Structure\Screenblock;
use AscentCreative\Forms\Structure\HTML;

class LogShipment extends BaseForm {

    public function __construct() {

        parent::__construct();

        $this->attribute('data-onsuccess', 'refresh');

        $this->children([

            CompoundDate::make('shipping_date', "Shipped Date")
                ->required(true, 'Please enter the date of the shipment')
                ->rules('before:tomorrow')
                ->messages(['before'=>'The shipment date cannot be in the future']),
            
            ForeignKeySelect::make('shipper_id', "Shipped with:")
                ->query(\AscentCreative\Checkout\Models\Shipping\Shipper::query())
                ->labelField('name')
                ->sortField('id')
                ->required(true, 'Please select a shipper'),
                
            Input::make('tracking_number', 'Tracking Number'),
                    

            /**
             * Partial Shipment will be complex - may need to split order row qtys for example. 
             *  - i.e. buyer orders 50 of item X, shipped in two blocks of 25. 
             * May already be multiple order rows for that item if different prices were in effect
             * 
             * It's almost like we need to, rather than log a shipment against the order rows, we log it against
             * the order and the sellable, but the system needs to cross check that ordered qtys and shipped qtys match.
             * 
             * so - order_items govern the pricing of the items, shipment_items govern the shipment info and the two are arbitrarily linked.
             */

            // Checkbox::make('partial_shipment', 'Partial Shipment?'),

            // HTML::make('<div data-showhide="partial_shipment" data-show-if="1">', '</div>')
            //     ->children([
            //         Input::make('test', 'Test'),
            //     // ]),
            
         
        ]);

      

    }

}
