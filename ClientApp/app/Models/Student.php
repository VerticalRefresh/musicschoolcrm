<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    /** @use HasFactory<\Database\Factories\StudentFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name', 'last_name', 'email', 'phone',
        'tutor_id', 'franchise_id', 'guardian_id',
        'subscription', 'balance', 'birthday'
    ];

    //Owned entities
    public function tutor()         { return $this->belongsTo(Tutor::class); }
    public function franchise()     { return $this->belongsTo(Franchise::class); }
    public function guardian()      { return $this->belongsTo(Guardian::class); }

    public function instruments()
    {
        return $this->belongsToMany(Instrument::class)
            ->withPivot(['level', 'is_primary', 'started_on'])
            ->withTimestamps();
    }

    public function address()        { return $this->morphOne(Address::class, 'addressable'); }
}
