<?php

namespace Fc9\Net\Database\Seeders;

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
        $this->call(NodeTableSeeder::class);
        $this->call(NodeUnilevelTableSeeder::class);
        $this->call(NodeBinaryTableSeeder::class);
    }
}
