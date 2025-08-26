<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany, MorphOne};

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'tutor_id', 'franchise_id', 'guardian_id',
        'subscription', 'balance', 'birthday'
    ];

    protected $casts = [
        'birthday'      => 'date',
        'subscription'  => 'decimal:2',
        'balance'       => 'decimal:2',
    ];

    //Owned entities
    public function tutor(): BelongsTo         { return $this->belongsTo(Tutor::class); }
    public function franchise(): BelongsTo     { return $this->belongsTo(Franchise::class); }
    public function guardian(): BelongsTo      { return $this->belongsTo(Guardian::class); }

    public function instruments(): BelongsToMany
    {
        return $this->belongsToMany(Instrument::class)
            ->withPivot(['level', 'is_primary', 'started_on'])
            ->withTimestamps();
    }

    public function address(): MorphOne        { return $this->morphOne(Address::class, 'addressable'); }

    /**
     * Search function across fields
     */

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') return $query;

        $driver = $query->getModel()->getConnection()->getDriverName();

        return $query->where(function (Builder $qq) use ($term, $driver){
            if ($driver === 'pgsql') {
                $qq->where('first_name', 'ILIKE', "%{$term}%")
                    ->orWhere('last_name', 'ILIKE', "%{$term}%")
                    ->orWhere('email', 'ILIKE', "%{$term}%");
            } else {
            // Portable fallback (MySQL/SQLite)
                $qq->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name',  'like', "%{$term}%")
                    ->orWhere('email',      'like', "%{$term}%");
            }
        });
    }

    /**
     * Franchise search
     */
    public function scopeOfFranchise(Builder $query, $id): Builder { return $id ? $query->where('franchise_id', $id) : $query; }

    /**
     * Tutor search
     */
    public function scopeOfTutor(Builder $query, $id): Builder { return $id ? $query->where('tutor_id', $id): $query; }
    /**
     * Instrument search
     */
    public function scopeWithInstrument (Builder $query, $id): Builder
    {
        return $id ? $query->whereHas('instruments', fn($w) => $w->where('instruments.id', $id)) : $query; 
    }
}
