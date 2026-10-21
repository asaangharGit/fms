<?php

namespace App\Models;

use Eloquent;

class AssetImpairment extends Eloquent
{
    public $timestamps = false;
    public $table = 'asset_impairment';
    protected $fillable = ['asset_register_id', 'amount','type','timestamp'];



}
