<?php

namespace Fc9\Net\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Fc9\Net\Entities\NodeUnilevel;

class NodeUnilevelTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $node = improve(config('net.node.status'));
        $bonus = improve(config('net.bonus.status'));

        NodeUnilevel::insert([
            [
                'node_id' => 1,
                'parent_node_id' => null,
                'lineage' => null,
                'node_status' => $node->added,
                'bonus_status' => $bonus->active,
            ],
            [
                'node_id' => 2,
                'parent_node_id' => 1,
                'lineage' => '1',
                'node_status' => $node->added,
                'bonus_status' => $bonus->active,
            ],
        ]);
    }
}
