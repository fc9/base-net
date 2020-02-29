<?php

namespace Fc9\Net\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Config;
use Fc9\Net\Entities\NodeBinary;

class NodeBinaryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fixed_leg = improve(config('net.binary.fixed_leg'));
        $work_leg = improve(config('net.binary.work_leg'));
        $status = improve(config('net.node.status'));

        NodeBinary::insert([
            [
                'node_id' => 1,
                'parent_node_id' => null,
                'lineage' => null,
                'fixed_leg' => $fixed_leg->left,
                'work_leg' => $work_leg->balanced,
                'status' => $status->added,
            ],

            [
                'node_id' => 2,
                'parent_node_id' => 1,
                'lineage' => '1',
                'fixed_leg' => $fixed_leg->left,
                'work_leg' => $work_leg->right,
                'status' => $status->added,
            ],
        ]);
    }
}
