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
class FinVendorInvoice extends Model
{

    protected $table = 'fin_vendor_invoice';
    protected $fillable = ['vendor_id','invoice_no','invoice_date','due_date','invoice_amount','approval_status'];
    public $timestamps = true;

    public function vendors()
    {
        return $this->belongsTo(FinVendors::class, 'vendor_id', 'id');

    }
    public function invoice_detail()
    {
        return $this->hasMany(FinVendorInvoiceDetail::class, 'invoice_id', 'id');

    }


}
