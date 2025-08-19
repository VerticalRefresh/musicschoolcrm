<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    /** @use HasFactory<\Database\Factories\AddressFactory> */
    use HasFactory;

    protected $fillable = ['line1', 'line2', 'city', 'region', 'postal_code', 'country_code'];
    public function addressable()       { return $this->morphTo(); }
}
