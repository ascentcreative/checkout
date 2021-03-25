<?php

namespace AscentCreative\Checkout\Contracts;

interface Sellable {

    public function getItemPrice();

    public function getItemName();

    public function isPhysical();

    public function getItemWeight();

    public function isDownload();

    public function getDownloadUrl();

}