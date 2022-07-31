<?php

namespace AscentCreative\Checkout\Contracts;

use Illuminate\Database\Query\Builder;

interface SellableGroup {

    /**
     * The label for the sellable group - used in selection fields etc.
     * Normally starts "Any ..." to denote that it covers a category of sellables. 
     * 
     * @return [type]
     */
    public function getSellableLabelAttribute();


    /**
     * Return a query which selects all the sellables in this group.
     * Must have two columns: sellable_type and sellable_id (polymorphic) which are the classname and id of the sellables
     * 
     * @param Builder $q
     * 
     * @return [type]
     */
    public function resolveSellables(Builder $q);

}