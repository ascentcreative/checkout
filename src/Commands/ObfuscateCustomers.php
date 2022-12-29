<?php

namespace AscentCreative\Checkout\Commands;

use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Hash;

use AscentCreative\Checkout\Models\Customer;

class ObfuscateCustomers extends Command
{

    use ConfirmableTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'checkout:obfuscate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Replaces customer names and emails with fake data";

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        if (! $this->confirmToProceed()) {
            return 1;
        }

        $faker = \Faker\Factory::create();

        foreach(Customer::all() as $cust) {
            $cust->update([
                'name'=>$faker->name,
                // ?'last_name'=>$faker->lastName,
                'email'=>$faker->email,
            ]);
        }

        return 0;
    }
}
