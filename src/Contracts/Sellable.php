<?php

namespace AscentCreative\Checkout\Contracts;

interface Sellable {

    public function getItemPrice();

    public function getItemName();

    public function isPhysical();

    public function getItemWeightAttribute(); // maybe item weight should be a trait applied to weight-based products only?

    public function isDownload();

    public function getDownloadUrl();

}