<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;

class Company extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'registration_number',
        'foundation_date',
        'activity',
        'active'
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'foundation_date' => 'date',
        'active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->isDirty('foundation_date') && $model->getOriginal('foundation_date') !== null && $model->exists) {
                throw ValidationException::withMessages(['foundation_date' => ['The foundation_date field cannot be modified once it has a value.'],]);
            }
        });
    }

    /** @return HasMany<CompanyAddress> */
    public function address()
    {
        return $this->hasMany(CompanyAddress::class);
    }

    /** @return HasMany<CompanyEmployee> */
    public function employee()
    {
        return $this->hasMany(CompanyEmployee::class);
    }

    /** @return HasMany<CompanyOwner> */
    public function owner()
    {
        return $this->hasMany(CompanyOwner::class);
    }

    /**
     *
     * @param Builder<Company> $query
     * @return Builder<Company>
     */
    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['address', 'owner', 'employee']);
    }

    public function getFoundationDateAttribute(?string $value): string
    {
        return Carbon::parse($value)->toDateString();
    }
}
