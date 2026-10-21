<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

class LoanList extends Model
{
    public $timestamps = false;
    public $table = 'loan_list';
    protected $fillable = ['id','account_no','total_amount','principal','profit','name','disbursed_date','upcoming_installment_date','upcoming_installment_payment'];


    public function installments()
    {
        return $this->hasMany(RecievableAmount::class, 'loan_id', 'id');
    }

}
