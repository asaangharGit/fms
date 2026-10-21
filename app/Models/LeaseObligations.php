<?php

namespace App\Models;

use Eloquent;

class LeaseObligations extends Eloquent
{
    public $timestamps = false;
    public $table = 'lease_obligation';
    protected $fillable = ['name','annual_increase','interest','irr','opening_balance','status','timestamp'];


    public function years()
    {
        return $this->belongsTo(LeaseObligationYears::class, 'lease_id');
    }

}
