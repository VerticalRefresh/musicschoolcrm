<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Franchise extends Model
{
    /** @use HasFactory<\Database\Factories\FranchiseFactory> */
    use HasFactory;

    protected $fillable = ['owner_id', 'phone', 'email', 'emergency_contact_id', 'timezone'];

    //Owned entities
    public function owner()             { return $this->belongsTo(Employee::class, 'owner_id'); }
    public function emergencyContact()  { return $this->belongsTo(Employee::class, 'emergency_contact_id'); }
    public function employees()         { return $this->hasMany(Employee::class); }
    public function tutors()            { return $this->hasMany(Tutor::class); }
    public function students()          { return $this->hasMany(Student::class); }
    public function storeHours()        { return $this->hasMany(StoreHour::class); }
    public function holidays()          { return $this->hasMany(Holiday::class); }
    public function address()           { return $this->morphOne(Address::class, 'addressable'); }
}
