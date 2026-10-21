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
class FinVendorInvoiceDetail extends Model
{

    protected $table = 'fin_vendor_invoice_detail';
    protected $fillable = ['invoice_id','vendor_id','coa_id','item_desc','tax','price','total','merge'];
    public $timestamps = false;



}
