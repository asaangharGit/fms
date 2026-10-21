<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class HrJob
 *
 * @property $id
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class Items extends Model
{

    protected $table = 'items';
    protected $fillable = ['name','code','parent_id','status','timestamp'];
    public $timestamps = false;

}
