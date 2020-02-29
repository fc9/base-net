<?php

namespace Fc9\Net;

use App;
use Fc9\Net\Entities\Node;
use Fc9\Net\Entities\NodeBinary;
use Fc9\Net\Entities\NodeUnilevel;

class Network
{
    public static function register($user)
    {
        $node = Node::create(['id', $user->id]);

        $parent_node = NodeUnilevel::fill($user->indicator_id);
        NodeUnilevel::create([
            'node_id' => $node->id,
            'parent_node_id' => $parent_node->id,
            'lineage' => $parent_node->lineage . '/' . $parent_node->id,
            'status' => config('net.node.status.added'),
        ]);

        NodeBinary::create([
            'node_id' => $node->id,
            'parent_node_id' => null,
            'lineage' => null,
            'fixed_leg' => config('net.binary.leg.undefined'),
            //'work_leg' => config('net.binary.leg.balanced'),
            //'status' => config('net.node.status.aspirant'),
        ]);

        return $node;
    }

    public static function setWorkLeg($node_id, $work_leg)
    {
        NodeBinary::where('node_id', $node_id)->update(compact('work_leg'));
    }

    public static function insertBinary($node_id, $node_indicator_id)
    {
//        NodeBinary::add(
//            $node_id,
//            $node_indicator_id,
//            config('net.node.status.added'), /* node_status */
//            config('net.bonus.status.active'), /* bonus_status */
//            config('net.binary.leg.balanced') /* work_leg */
//        );
    }
}
