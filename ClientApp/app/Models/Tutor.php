<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    /** @use HasFactory<\Database\Factories\TutorFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'franchise_id', 'balance', 'certification', 'age_group'
    ];

    //Owned entities
    public function franchise()         { return $this->belongsTo(Franchise::class); }
    public function students()          { return $this->hasMany(Student::class); }
    public function instruments()
    {
        return $this->belongsToMany(Instrument::class)
            ->withPivot(['proficiency', 'years', 'is_primary'])
            ->withTimestamps();
    }

    public function address()           { return $this->morphOne(Address::class, 'addressable'); }
}
