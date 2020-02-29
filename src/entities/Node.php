<?php

namespace Fc9\Net\Entities;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = ['user_id'];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * The database table.
     *
     * @var string
     */
    protected $table = 'node';
}
