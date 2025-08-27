<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\{Employee, Franchise};

class EmployeeApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index_includes_owned_relationships(): void
    {
        //Load dependend items
        $franchise = Franchise::factory()->create(['email' => 'store@example.com']);
        

    }
}
