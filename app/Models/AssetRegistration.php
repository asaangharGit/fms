<?php

namespace App\Models;

use Eloquent;

class AssetRegistration extends Eloquent
{
    public $timestamps = false;
    public $table = 'asset_registration';
    protected $fillable = ['item_id', 'fin_gen_id','amount','percent','active','status','created_on','timestamp'];


    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }

}
