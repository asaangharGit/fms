<?php

namespace App\Models;

use Eloquent;

class LeaseObligationYears extends Eloquent
{
    public $timestamps = false;
    public $table = 'lease_obligation_years';
    protected $fillable = ['lease_id','commencement_date','expire_date','months','rent'];




}
