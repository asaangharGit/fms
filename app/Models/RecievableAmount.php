<?php

namespace App\Models;

use App\User;
use Eloquent;

class RecievableAmount extends Eloquent
{
    protected $fillable = [
        'installment_id',
        'loan_id',
        'recovered_date',
        'total_amount',
        'profit',
        'principal',
        'charity',
        'penality',
        'approved_by',
        'status'];
    public $timestamps = true;
    public function user()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function loan()
    {
        return $this->belongsTo(LoanList::class,'loan_id');
    }
    
}
