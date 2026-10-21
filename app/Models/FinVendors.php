<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;

class FinVendors extends Model
{
    public $timestamps = false;
    public $table = 'fin_vendors';
    protected $fillable = ['name','ntn','contact','address','city','status','timestamp'];


    public function invoices()
    {
        return $this->hasMany(FinVendorInvoice::class, 'vendor_id', 'id');
    }

}
