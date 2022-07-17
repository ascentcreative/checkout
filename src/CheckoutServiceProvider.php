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
            
        // dd($segment.'.approval.recall');
        Route::get('/basket/add/' . $segment . '/{sku}', function($sku) use ($class, $return) {
            
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
    
                $item = $class::bySku($sku)->first();
    
            }
            
            
            
            if($item) {
                basket()->add($item);
                // if this was an ajax request / modalLink, we should return a modal?
                if ($return) {
                    // customised return
                    // - allow both a string (URI), or a callback Closure
                } else {
                    return redirect()->back();
                }

            } else {
                abort(404);
            }

        })->name($segment . '.basket.add');

        // dd($segment.'.approval.recall');
        Route::get('/basket/add/' . $segment . '/{sku}/qty/{qty}', function($sku, $qty) use ($class, $return) {
            
            $item = $class::where('sku', $sku)->first();
            if($item) {
                basket()->add($item, $qty);
                if ($return) {
                    // customised return
                    // - allow both a string (URI), or a callback Closure
                } else {
                    return redirect()->back();
                }

            } else {
                abort(404);
            }

        })->name($segment . '.basket.add.qty');

      
    });

  }




  public function boot()
  {

    // Register the helpers php file which includes convenience functions:
    require_once (__DIR__.'/Helpers/checkout.php');
    
    $this->bootDirectives();
    $this->bootComponents();
    $this->bootPublishes();
    $this->bootAssets();


    $this->loadViewsFrom(__DIR__.'/../resources/views', 'checkout');

    $this->loadRoutesFrom(__DIR__.'/../routes/web.php');

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    
  }

  // register the components
  public function bootComponents() {

    // Blade::component('project-element', 'AscentCreative\Projecct\View\Components\Element');
  

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
    \Livewire::component('checkout', \AscentCreative\Checkout\Livewire\Checkout::class);

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



}