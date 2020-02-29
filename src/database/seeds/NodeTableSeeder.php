<?php

namespace Fc9\Net\Database\Seeders;

use Illuminate\Database\Seeder;
use Fc9\Net\Entities\Node;

class NodeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Node::insert([
            [
                'id' => 1,
                'user_id' => 3
            ],

            [
                'id' => 2,
                'user_id' => 4
            ],
        ]);
    }
}
