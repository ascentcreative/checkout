<?php

namespace AscentCreative\Checkout;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Route;

use AscentCreative\Checkout\Contracts\Sellable;
use AscentCreative\Checkout\Providers\EventServiceProvider;


use AscentCreative\Checkout\Models\Basket;

class CheckoutServiceProvider extends ServiceProvider
{
  public function register()
  {
    //
    $this->mergeConfigFrom(
        __DIR__.'/../config/checkout.php', 'checkout'
    );


    $this->app->register(EventServiceProvider::class);


    $this->app->bind('checkout:shippingcalculator',function(){
        $cls = config('checkout.shippingcalculator');
        return new $cls();
    });

    $this->app->singleton('checkout:sellables',function(){
        return new \AscentCreative\Checkout\Registries\Sellables();
    });

    $this->registerRouteMacros();
   
  }


  /**
   * 
   * Route macros to allow sites to create utility routes (i.e. adding to basket)
   * 
   * @return [type]
   */
  public function registerRouteMacros() {

    \Illuminate\Support\Facades\Route::macro('basket', function($segment, $class, $return = null) {



            // Also
            // how do we add product options to the basket?
            // - Is the combination defined by the SKU?
            // - or does the SKU point to the product only and we pass in an array of additional options in the request, 
            //      which are then saved as attributes to the item? (JSON? Child Table?)

            // Some of this may depend on whether the product has defined variants (combination of option values)
            // or if it's a free for all (user selection). 
            // - Defined variant could/should have SKU, and would be added as a direct addition to the basket
            // - option bag as attributes in the URL. 

            // Physical VS Download
            // Is this just an option? Maybe specified as such in the add
            // But it might affect the operation of the system (i.e. delivery method / costs) so is more universal than just "what colour"
            // Still pass in a flag (download / physical) as above
            // But perhaps stored as flag on order item, rather than elsewhere.

            // so - product can still have phys/digi options on admin form.

            // if format not specified, check product. Throw error if both formats allowed.


         // Maybe the quantity, like the format, should be a special attribute, so that options / variants can also be supplied.
        //  Route::get('/basket/add/' . $segment . '/{sku}/qty/{qty}', function($sku, $qty) use ($class, $return) {
            
        //     dd($qty);

        //     $item = $class::where('sku', $sku)->first();
        //     if($item) {
        //         basket()->add($item, $qty);
        //         if ($return) {
        //             // customised return
        //             // - allow both a string (URI), or a callback Closure
        //         } else {
        //             return redirect()->back();
        //         }

        //     } else {
        //         abort(404);
        //     }

        // })->name($segment . '.basket.add.qty');

            
        Route::match(['get', 'post'], '/basket/add/' . $segment . '/{sku}/{options?}', function($sku, $options=null) use ($class, $return) {

            if(is_null($options)) {
                // options may have been passed as URL params
                $options = request()->all();
            } else {
                // split options out of the url segments
                $split = explode('/', $options);
                $options = [];
                for($i=0; $i < count($split); $i = $i+2) {
                    // alternate key and value
                    $options[$split[$i]] = $split[$i+1] ?? null;
                }
            }
          

            /*
            * Handle special options:
            */

            // "format" option = physical vs download (if the object allows for both)
            if (isset($options['format'])) {
                $format = $options['format'];
                unset($options['format']);
            }

            // the qty to add to the basket
            if (isset($options['qty'])) {
                $qty = $options['qty'];
                unset($options['qty']);
            } else {
                $qty = 1;
            }


            // resolve the 'product' object:
            $object = new $class();

            // some items may not have an sku column, but may need to resolve it via their own method
            // detect an SKU column on the model's table
            // 
            // perhaps deflect this into a Sellable Trait?
            if ($object->getConnection()
                        ->getSchemaBuilder()
                        ->hasColumn($object->getTable(), 'sku')) {
                        // required update here
    
                $item = $class::where('sku', $sku)->first();
            
            } else {
    
                // actually, maybe the safer route is to check for the existence of this method
                // and only look for the fixed column if it's not found. 
                $item = $class::bySku($sku)->first();
    
            }
            

            if($item) {

                // when adding, we should also be logging the options... if any
                basket()->add($item, $qty);


                // if this was an ajax request / modalLink, we should return a modal?
                if ($return) {
                    // customised return
                    // - allow both a string (URI), or a callback Closure
                } else {

                    if(request()->headers->get('ModalLink')) {
                        return view('checkout::basket.modal.updated');
                    } else {
                        return redirect()->back();
                    }
                    
                }

            } else {
                abort(404);
            }

        })->where('options', '(.*)')->name($segment . '.basket.add');

       

      
    });

  }




  public function boot()
  {

    // Register the helpers php file which includes convenience functions:
    require_once (__DIR__.'/Helpers/checkout.php');
    
    $this->bootDirectives();
    $this->bootComponents();
    $this->bootCommands();
    $this->bootPublishes();
    $this->bootAssets();


    $this->loadViewsFrom(__DIR__.'/../resources/views', 'checkout');

    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

    
  }

  // register the components
  public function bootComponents() {

    \Livewire::component('checkout', \AscentCreative\Checkout\Livewire\Checkout::class);
    \Livewire::component('basket-summary', \AscentCreative\Checkout\Livewire\BasketSummary::class);

  }


  public function bootListeners() {

    Event::listen(
        BasketUpdated::class,
        [StripeBasketListener::class, 'handle']
    );

  }



  // create custom / convenience Blade @Directives 
  public function bootDirectives() {

    //
   

  }


  public function bootAssets() {
      app(\AscentCreative\CMS\Helpers\PackageAssets::class)
        ->addStylesheet('/vendor/ascent/checkout/css/ascentcreative-checkout.css');
  }
  

    public function bootPublishes() {

      $this->publishes([
        __DIR__.'/Assets' => public_path('vendor/ascent/checkout'),
    
      ], 'public');


      $this->publishes([
        __DIR__.'/../config/checkout.php' => config_path('checkout.php'),
      ]);


    }


    public function bootCommands() {
        $this->commands([
            \AscentCreative\Checkout\Commands\ObfuscateCustomers::class,
            \AscentCreative\Checkout\Commands\ZendImport::class,
        ]);
    }



}