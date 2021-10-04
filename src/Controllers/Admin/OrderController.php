<?php

namespace AscentCreative\Checkout\Controllers\Admin;

use AscentCreative\CMS\Controllers\AdminBaseController;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;
use AscentCreative\CMS\Filters\DateFilter;

class OrderController extends AdminBaseController
{

    static $modelClass = 'AscentCreative\Checkout\Models\Order';
    static $bladePath = "checkout::admin.orders";

    public $indexSearchFields = ['customer.name', 'customer.email', 'items.title'];

    public $allowDeletions = false;

    public $indexSort = [
        ['confirmed_at', 'desc']
    ];

    public function __construct() {
        parent::__construct();
    }

    public function rules($request, $model=null) {

       return [
            'title' => 'required',
        ]; 

    }


    public function autocomplete(Request $request, string $term) {

        echo $term;

    }



}