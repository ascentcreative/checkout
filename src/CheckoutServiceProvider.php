<?php

namespace AscentCreative\Checkout;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Event;

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
        __DIR__.'/config/checkout.php' => config_path('checkout.php'),
      ]);


    }



}