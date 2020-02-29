<?php

namespace Fc9\Net\Entities;

use Illuminate\Database\Eloquent\Model;

class NodeUnilevel extends Model
{
    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'node_unilevel';

    public $primaryKey = 'node_id';

    protected $fillable = [
        'node_id',
        'parent_node_id',
        'lineage',
        'node_status',
        'bonus_status',
        'created_at',
        'updated_at',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    public static function numberOfIndications($node_id)
    {
        return NodeUnilevel::where('parent_node_id', $node_id)->count();
    }
}
