<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'country',
        'zip_code',
        'city',
        'street_address',
        'latitude',
        'longitude',
        'company_id',
    ];

    /** @return BelongsTo<Company, CompanyAddress> */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
