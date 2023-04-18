<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ManagerSeeder::class);
        $this->call(PermissionProfilesSeeder::class);
        $this->call(DocumentItemsSeeder::class);
    }
}
