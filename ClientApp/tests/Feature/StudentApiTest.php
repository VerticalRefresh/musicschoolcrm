<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Foundation\Fluent\AssertableJson;
use App\Models\{Student, Tutor, Guardian, Franchise, Employee, Address, Instrument};

class StudentApiTest extends TestCase
{
    /**
     * A basic feature test example.
     */

    use RefreshDatabase;

    public function test_index_includes_owned_relationships(): void //Index test
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

    public function test_index_supports_case_insensitive_search(): void
    {
        //Create two students for filtering
        $franchise = Franchise::factory()->create();
        $tutor     = Tutor::factory()->create(['franchise_id' => $franchise->id]);

        $match = Student::factory()->create([
            'first_name'   => 'Ava',
            'last_name'    => 'Ng',
            'email'        => 'ava@example.com',
            'franchise_id' => $franchise->id,
            'tutor_id'     => $tutor->id,
        ]);

        $other = Student::factory()->create([
            'first_name'   => 'Evan',
            'last_name'    => 'Stone',
            'email'        => 'evan@example.com',
            'franchise_id' => $franchise->id,
            'tutor_id'     => $tutor->id,
        ]);

        //Get filtered results
        $res = $this->getJson('/api/students?q=ng&per_page=50');

        //Assert 1 result
        $res->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.id', $match->id);
    }

    public function test_index_filters_by_franchise_tutor_and_instrument(): void //Further filter testing, franchise tutor and instrument
    {
        //Two franchises to filter by
        $frA = Franchise::factory()->create();
        $frB = Franchise::factory()->create();

        //Two tutors to filter by
        $tutorA = Tutor::factory()->create(['franchise_id' => $frA->id]);
        $tutorB = Tutor::factory()->create(['franchise_id' => $frB->id]);

        //Guardian for required field
        $g = Guardian::factory()->create();

        //Two instruments to filter by
        $instGuitar = Instrument::factory()->create(['name' => 'Guitar']);
        $instPiano  = Instrument::factory()->create(['name' => 'Piano']);

        //Students to filter
        $s1 = Student::factory()->create([
            'franchise_id' => $frA->id, 'tutor_id' => $tutorA->id, 'guardian_id' => $g->id,
        ]);
        $s2 = Student::factory()->create([
            'franchise_id' => $frA->id, 'tutor_id' => $tutorB->id, 'guardian_id' => $g->id,
        ]);
        $s3 = Student::factory()->create([
            'franchise_id' => $frB->id, 'tutor_id' => $tutorB->id, 'guardian_id' => $g->id,
        ]);

        // Attach instruments for pivot table
        $s1->instruments()->sync($instGuitar->id, ['level' => 3, 'is_primary' => true]);
        $s2->instruments()->sync($instPiano->id,  ['level' => 2, 'is_primary' => false]);
        $s3->instruments()->sync($instGuitar->id, ['level' => 4, 'is_primary' => false]);

        // Filter by franchise A and assert 2 results
        $this->getJson("/api/students?franchise_id={$frA->id}&per_page=50")
             ->assertOk()
             ->assertJsonCount(2, 'data');

        // Filter by tutor B and assert 2 results
        $this->getJson("/api/students?tutor_id={$tutorB->id}&per_page=50")
             ->assertOk()
             ->assertJsonCount(2, 'data');

        // Filter by instrument Guitar and assert 2 results
        $res = $this->getJson("/api/students?instrument_id={$instGuitar->id}&per_page=50")
                    ->assertOk()
                    ->assertJsonCount(2, 'data');
        $ids = collect($res->json('data'))->pluck('id')->sort()->values()->all();
        $this->assertEqualsCanonicalizing([$s1->id, $s3->id], $ids);

    }

    public function index_supports_multi_column_sort(): void //Test for sortation and - prefix for desc
    {
        //Create necessary fields for students
        $fr = Franchise::factory()->create();
        $tu = Tutor::factory()->create(['franchise_id' => $fr->id]);

        // Create 3 students with balances
        $sA = Student::factory()->create([
            'first_name' => 'Amy', 'last_name' => 'Zed', 'balance' => '50.00',
            'franchise_id' => $fr->id, 'tutor_id' => $tu->id,
        ]);
        $sB = Student::factory()->create([
            'first_name' => 'Bob', 'last_name' => 'Alpha', 'balance' => '200.00',
            'franchise_id' => $fr->id, 'tutor_id' => $tu->id,
        ]);
        $sC = Student::factory()->create([
            'first_name' => 'Cara', 'last_name' => 'Omega', 'balance' => '200.00',
            'franchise_id' => $fr->id, 'tutor_id' => $tu->id,
        ]);

        // Sort by -balance, then last_name, Assert order, (-) prefix sets desc order
        $res = $this->getJson('/api/students?sort=-balance,last_name&per_page=50');

        $res->assertOk()
            ->assertJsonPath('data.0.id', $sB->id)   // 200, Alpha
            ->assertJsonPath('data.1.id', $sC->id)   // 200, Omega
            ->assertJsonPath('data.2.id', $sA->id);  // 50, Zed
    }

    public function test_store_creates_student_and_returns_201(): void  //Store Test
    {
        //Create student payload
        $franchise = Franchise::factory()->create();
        $tutor = Tutor::factory()->create();
        $guardian = Guardian::factory()->create();
        $payload = [
            'first_name'        => 'Ava',
            'last_name'         => 'Ng',
            'email'             => 'example@email.com',
            'phone'             => '555-555-5555',
            'subscription'      => 10.99,
            'balance'           => 0,
            'birthday'          => '2015-01-01',
            'tutor_id'          => $tutor->id,
            'guardian_id'       => $guardian->id,
            'franchise_id'      => $franchise->id,
        ];

        //Post payload and assert path and created 
        $this->postJson('/api/students', $payload)
            ->assertCreated()
            ->assertJsonPath('data.first_name', 'Ava');

        //Check DB for student to assure posting
        $this->assertDatabaseHas('students', [
            'first_name'    => 'Ava',
            'last_name'     => 'Ng',
        ]);
    }
}
