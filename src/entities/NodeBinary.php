<?php

namespace Fc9\Net\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Register\Entities\Membership;
use Modules\Register\Entities\User;

class NodeBinary extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'node_binary';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public $primaryKey = 'node_id';

    protected $fillable = [
        'node_id',
        'parent_node_id',
        'left_child_node_id',
        'right_child_node_id',
        'lineage',
        'fixed_leg',
        'work_leg',
        'left_pv',
        'right_pv',
        'left_count',
        'right_count',
        'node_status',
        'bonus_status',
        'created_at',
        'updated_at',
    ];


    public static function add($id, $indicator_id, $node_status, $bonus_status, $fixed_leg, $work_leg)
    {
        $node_binary = self::create([
            'node_id' => $id,
            'parent_node_id' => null,
            'lineage' => null,
            'fixed_leg' => $fixed_leg,
            'work_leg' => $work_leg,
            'node_status' => $node_status,
            'bonus_status' => $bonus_status,
        ]);

        return ($node_status === config('network.node.status.added'))
            ? self::activeBinary($node_binary->node_id, $indicator_id)
            : $node_binary;
    }


    public static function activeBinary($node_id, $indicator_id)
    {
        #dd($node_id, $indicator_id);
        $indicator_node = self::find($indicator_id);
        $fixed_leg = self::getFixedLeg($indicator_node);
        $parent_node = self::getParentNode($indicator_node, $fixed_leg);
        #dd([$indicator_node, $fixed_leg, $parent_node]);

        $node = self::find($node_id);
        $node->parent_node_id = $parent_node->node_id;
        #$node->lineage = $parent_node->lineage . '/' . $parent_node->node_id; #TODO:future
        $node->lineage = $parent_node->lineage . '[' . $parent_node->node_id . ']';
        $node->fixed_leg = $fixed_leg;
        $node->node_status = config('network.node.status.added');
        $node->bonus_status = config('network.bonus.status.active'); #here
        $node->save();

        self::updateParentNode($parent_node, $fixed_leg);

        return $node;
    }

    private static function getFixedLeg($indicator_node)
    {
        return (NodeUnilevel::numberOfIndications($indicator_node->node_id) !== 0
            || $indicator_node->work_leg !== config('network.binary.leg.balanced'))
            ? $indicator_node->fixed_leg
            : self::getSmallerLeg($indicator_node);
    }


    private static function getSmallerLeg($node)
    {
        $left = config('network.binary.leg.left');
        $right = config('network.binary.leg.right');
        return self::getLegSize($node, $left) >= self::getLegSize($node, $right) ? $right : $left;
    }


    private static function getLegSize($node, $leg, $size = 0)
    {
        $bottom_node = self::where('fixed_leg', $leg)
            ->where('lineage', $node->lineage . '[' . $node->node_id . ']')
            #->where('lineage', $node->lineage . '/' . $node->node_id)
            ->first();

        return ($bottom_node !== null) ? self::getLegSize($bottom_node, $leg, ++$size) : $size;
    }


    private static function getParentNode($node, $leg)
    {
        $bottom_node = self::where('fixed_leg', $leg)
            ->where('lineage', $node->lineage . '[' . $node->node_id . ']')
            #->where('lineage', $node->lineage . '/' . $node->node_id)
            ->first();

        return ($bottom_node !== null) ? self::getParentNode($bottom_node, $leg) : $node;
    }


    private static function updateParentNode($node, $leg)
    {
        $left_count = $node->left_count;
        $right_count = $node->right_count;
        $node = self::where('node_id', $node->node_id)->first();

        if ($leg === config('network.binary.leg.left')) {
            $node->left_count = $node->left_count + 1;
        } else {
            $node->right_count = $node->right_count + 1;
        }

        $node->save();
        #dd($node->node_id, $left_count, $node->left_count, $right_count, $node->right_count);
    }


    public static function getPins($node_id)
    {
        $node[0] = (object)self::getPin($node_id);
        $leg = 1;
        $no = 0;
        for ($i = 1; $i < 32; $i++) {
            $node[$i] = $leg === 1
                ? (object)self::getPin($node[$no]->node_id_left)
                : (object)self::getPin($node[$no]->node_id_right);
            $leg += $leg === 1 ? -1 : 1;
            $no += $i % 2 === 0 ? 1 : 0;
        }
        return $node;
    }


    private static function getPin($node_id)
    {
        if ($node_id === null) {
            return [
                'id' => null,
                'username' => null,
                'node_id_right' => null,
                'node_id_left' => null,
                'graduate' => null,
            ];
        }

        $node = self::find($node_id);
        $node_left = NodeBinary::where('parent_node_id', $node_id)
            ->where('lineage', $node->lineage . '[' . $node->node_id . ']')
            ->where('fixed_leg', config('network.binary.leg.left'))
            ->first();
        $node_right = NodeBinary::where('parent_node_id', $node_id)
            ->where('lineage', $node->lineage . '[' . $node->node_id . ']')
            ->where('fixed_leg', config('network.binary.leg.right'))
            ->first();

        $pin['id'] = $node->node_id;
        $pin['username'] = User::find($node_id)->username;
        $pin['left_pv'] = $node->left_pv;
        $pin['right_pv'] = $node->right_pv;
        $pin['left_count'] = $node->left_count;
        $pin['right_count'] = $node->right_count;
        $pin['node_id_right'] = $node_right !== null ? $node_right->node_id : null;
        $pin['node_id_left'] = $node_left !== null ? $node_left->node_id : null;
        $pin['graduate'] = Membership::where('user_id', $node->node_id)->first()->graduate;

        return $pin;
    }


    public static function checkQualification($node_id)
    {
//        $order = NodeUnilevel::max('indication_order');
//        # SELECT CASE WHEN max(indication_order) IS NULL THEN 1 ELSE max(indication_order) END AS n FROM Network.node_unilevel;
//
//        NodeUnilevel::create([
//            'node_id' => $node->id,
//            'parent_node_id' => $indicator_id,
//            'indication_order' => $order !== null ? $order + 1 : 1
//        ]);
    }


    public static function addBkp($id, $indicator_id, $node_status, $bonus_status, $work_leg)
    {
        $indicator_node = NodeBinary::where('node_id', $indicator_id)->first();
        $indicators_number = NodeUnilevel::where('parent_node_id', $indicator_id)->count();

        if ($indicators_number > 1) {

            $fixed_leg = ($indicator_node->work_leg === config('network.binary.leg.balanced'))
                ? self::getSmallerLeg($indicator_node)
                : $indicator_node->work_leg;

            $c = 1;

        } else {

            $fixed_leg = NodeBinary::where('node_id', $indicator_id)->first()->fixed_leg;

            $c = 2;

        }

        #dd([$indicators_number, $indicator_node->node_id, $fixed_leg, $c]);

        $parent_node = self::getParentNode($indicator_node, $indicator_node->lineage, $fixed_leg);

        //dd([$indicators_number, $indicator_node, $fixed_leg, $c, $parent_node]);

        return self::create([
            'node_id' => $id,
            'parent_node_id' => $parent_node->node_id,
            'lineage' => $parent_node->lineage . '[' . $parent_node->node_id . ']',
            'fixed_leg' => $fixed_leg,
            'work_leg' => $work_leg,
            'node_status' => $node_status,
            'bonus_status' => $bonus_status,
        ]);
    }
}
