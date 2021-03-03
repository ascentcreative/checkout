<?php

namespace AscentCreative\Checkout\Contracts;

interface Sellable {

    public function getItemPrice();

    public function getItemName();

}