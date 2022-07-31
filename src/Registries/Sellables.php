<?php
namespace AscentCreative\Checkout\Registries;

class Sellables {

    public $registry = [];
    public $group_registry = [];

    public function __construct() {
        // dump("created registry");
    }

    public function register($class) {
        // dump("register " . $class);
        $this->registry[] = $class;
        // dump($this->registry);
    }   

    public function registerGroup($class) {
        $this->group_registry[] = $class;
    }

    public function getRegistry() {
        return $this->registry;
    }

    public function getGroupRegistry() {
        return $this->group_registry;
    }

}