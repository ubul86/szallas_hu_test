<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyOwner extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'active',
        'order'
    ];

    /** @return BelongsTo<Company, CompanyOwner> */
    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
