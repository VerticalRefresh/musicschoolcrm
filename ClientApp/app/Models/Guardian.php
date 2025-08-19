<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guardian extends Model
{
    /** @use HasFactory<\Database\Factories\GuardianFactory> */
    use HasFactory;

    protected $fillable = ['first_name', 'last_name', 'email', 'phone'];

    public function students()          { return $this->hasMany(Student::class); }
    public function address()           { return $this->morphOne(Address::class, 'addressable'); }
}
