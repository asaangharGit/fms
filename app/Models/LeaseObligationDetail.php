<?php

namespace App\Models;

use Eloquent;

class LeaseObligationDetail extends Eloquent
{
    public $timestamps = false;
    public $table = 'lease_obligation_detail';
    protected $fillable = ['lease_id','year','total_payments','pvf','present_value','liabality','fin_lease_cost','principal_repayment','payment_date','remaining_liabality','timestamp'];


}
