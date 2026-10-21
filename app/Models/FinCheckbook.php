<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FinCheckbook
 *
 * @property $id
 * @property $checkbook_title
 * @property $bank_id
 * @property $checknum_start
 * @property $checknum_end
 * @property $created_at
 * @property $updated_at
 *
 * @package App
 * @mixin \Illuminate\Database\Eloquent\Builder
 */
class FinCheckbook extends Model
{
    
    static $rules = [
    ];

    protected $perPage = 10;

    /**
     * Attributes that should be mass-assignable.
     *
     * @var array
     */
    protected $fillable = ['bank_id','chequebook_name','checknum_start','checknum_end'];



    public function banks(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(FinBanksAccount::class, 'bank_id');
    }
}
