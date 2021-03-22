<?php

namespace AscentCreative\Checkout\Controllers\Admin;

use AscentCreative\CMS\Controllers\AdminBaseController;

use Illuminate\Http\Request;

use Illuminate\Database\Eloquent\Model;

class OrderController extends AdminBaseController
{

    static $modelClass = 'AscentCreative\Checkout\Models\Order';
    static $bladePath = "checkout::admin.orders";


    public function commitModel(Request $request, Model $model)
    {

      
    //    MenuItem::linkThis($this, $request->context_id, $request->context_type);

      
      // $model->fillExtenders($request->all());
       $model->fill($request->all());
       $model->save();

     //  dd($request->all());


       // $Fillable prevents non-model data being sent to the DB.
       // Now we've got the model saved, we can fire off the extensions to other models / relationships
       // Would really love to make this configurable (like model plugins on Zend)

       // problem is that starts to relate to adding fields directly to the edit screen with names to 
       // match expected incoming data, and that's where ModelPlugins got hugely complex.
       // But there's a reason I went that way with the strucutre...

       

        //dd($incoming);
       
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