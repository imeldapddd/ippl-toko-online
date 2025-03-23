<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
Use Modules\Shop\Database\Seeders\ShopDatabaseSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        //User::factory()->create([
        //    'name' => 'Test User',
        //    'email' => 'test@example.com',
        // ]);

        if ($this->command->confirm('Do you want to refresh migration befire sending, it will clear all old data ?')){
            $this->command->call('migrate:refresh');
            $this->command->info('Data cleared, starting from blank database');
        }

        User::factory()->create();
        $this->command->info('sample user seeded.');

        if ($this->command->confirm('Do you want to seed sample product ?')){
            $this->call(ShopDatabaseSeeder::class);
        }   
    }
}