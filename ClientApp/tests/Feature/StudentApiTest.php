<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Fluent\AssertableJson;
use App\Models\{Student, Tutor, Guardian, Franchise};

class StudentApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_index_includes_owned_relationships(): void
    {
        //Load dependent items
        $franchise  = Franchise::factory()->create(['email' => 'store@example.com']);
        
        $tutor      = Tutor::factory()->create([
            'first_name'    => 'Tom',
            'last_name'     => 'Teacher',
            'franchise_id'  => $franchise->id,
        ]);
        
        $guardian   = Guardian::factory()->create([
            'first_name'    => 'Gina',
            'last_name'     => 'Guardian',
        ]);

        //Create student
        $student    = Student::factory()->create([
            'first_name'    => 'Ava',
            'last_name'     => 'Ng',
            'email'         => 'ava@example.com',
            'tutor_id'      => $tutor->id,
            'guardian_id'   => $guardian->id,
            'franchise_id'  => $franchise->id,
        ]);

        //Give address
        $student->address()->create([
            'line1'         => '123 Main St',
            'line2'         => null,
            'city'          => 'Albany',
            'state'         => 'NY',
            'zip'           => '12207',
            'country'       => 'US',
        ]);

        //Act
        $res = $this->getJson('/api/students');

        //Assert structure and relationships
        $res->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'first_name', 'last_name', 'email', 'phone', 'subscription', 'balance', 'birthday',
                        'tutor'     => ['id', 'first_name', 'last_name'],
                        'guardian'  => ['id', 'first_name', 'last_name'],
                        'franchise' => ['id', 'email'],
                        'address'   => ['line1', 'line2', 'city', 'state', 'zip', 'country'],
                    ]
                ],
                'links'     => ['first', 'last', 'prev', 'next'],
                'meta'      => ['current_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.email', 'ava@example.com')
            ->assertJsonPath('data.0.tutor.id', $tutor->id)
            ->assertJsonPath('data.0.franchise.email', 'store@example.com')
            ->assertJsonPath('data.0.guardian.first_name', 'Gina')
            ->assertJsonPath('data.0.address.city', 'Albany');
    }
}
