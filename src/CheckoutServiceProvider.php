<?php

namespace AscentCreative\Checkout;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;


class CheckoutServiceProvider extends ServiceProvider
{
  public function register()
  {
    //

  }

  public function boot()
  {
    // Register the helpers php file which includes convenience functions:
   // require_once (__DIR__.'/Helpers/ascenthelpers.php');


    $this->bootDirectives();
    $this->bootComponents();
    $this->bootPublishes();

    $this->loadViewsFrom(__DIR__.'/resources/views', 'cms');

    $this->loadRoutesFrom(__DIR__.'/routes/web.php');

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    
  }

  // register the components
  public function bootComponents() {

    // Blade::component('project-element', 'AscentCreative\Projecct\View\Components\Element');
  


  }




  // create custom / convenience Blade @Directives 
  public function bootDirectives() {

    //
  }

  

    public function bootPublishes() {

      $this->publishes([
        __DIR__.'/Assets' => public_path('vendor/ascent/checkout'),
    
      ], 'public');

    }



}