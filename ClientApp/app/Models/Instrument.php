<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    /** @use HasFactory<\Database\Factories\InstrumentFactory> */
    use HasFactory;

    protected $fillable = ['name', 'category'];

    public function tutors()
    {
        return $this->belongsToMany(Tutor::class)
            ->withPivot(['proficiency', 'years', 'is_primary'])
            ->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(Student::class)
            ->withPivot(['level', 'is_primary', 'started_on'])
            ->withTimestamps();
    }

}
