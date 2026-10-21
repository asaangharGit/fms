<?php

namespace App\Models;

use Eloquent;

class AssetDepreciation extends Eloquent
{
    public $timestamps = false;
    public $table = 'asset_depreciation';
    protected $fillable = ['item_id','asset_date','year','dep_amount','impairment','total','dep_percentage','percent_of_amount',
        'jan','feb','mar','apr','may','june','july','aug','sept','oct','nov','dec','nrv','created_on','timestamp'];


    public function item()
    {
        return $this->belongsTo(Items::class, 'item_id');
    }


}
