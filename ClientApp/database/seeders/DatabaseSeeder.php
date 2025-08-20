<?php

namespace Database\Seeders;

use App\Models\{
    User, Franchise, Employee, Tutor, Student, Guardian, Instrument, Address, StoreHour, Holiday
};
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        // Seed initial user, hashing done with cast
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'secret123',
            'email_verified_at' => now(),
            'remember_token' => Str::random(10),
        ]);

        //Seed franchises
        $owner = Employee::factory()->create([

        ]);
        $franchises = Franchise::factory()->count(2)->create([
            'owner_id' => $owner->id,
            'timezone' => 'America/New_York',
        ]);

        //Seed instruments
        $catalog = [
            ['name' => 'Guitar', 'category' => 'Strings'],
            ['name' => 'Violin', 'category' => 'Strings'],
            ['name' => 'Cello', 'category' => 'Strings'],
            ['name' => 'Piano', 'category' => 'Keys'],
            ['name' => 'Flute', 'category' => 'Woodwind'],
            ['name' => 'Trumpet', 'category' => 'Brass'],
            ['name' => 'Drums', 'category' => 'Percussion'],
            ['name' => 'Saxophone', 'category' => 'Woodwind'],
        ];

        \App\Models\Instrument::upsert($catalog, ['name', 'category']);
        $instruments = \App\Models\Instrument::all();

        //Seed franchise dependent data
        foreach ($franchises as $franchise) {
            //Employee, owner and emergency contact info
            $employees = Employee::factory()->count(3)->create();
            foreach ($employees as $e) { $e->update(['franchise_id' => $franchise->id]); }

            $franchise->update([
                'owner_id' => $employees->first()->id,
                'emergency_contact_id' => $employees->get(1)->id ?? $employees->first()->id,
            ]);
            
            //Address (Polymorphic 1:1)
            $franchise->address()->create(Address::factory()->make()->toArray());

            //Store Hours (7 unique weekdays)
            foreach (range(0, 6) as $d) {
                $franchise->storeHours()->create([
                    'weekday'   => $d,
                    'opens_at'  => '09:00:00',
                    'closes_at' => '17:00:00',
                    'notes'     => null,
                ]);
            }

            //Holidays
            $franchise->holidays()->createMany([
                ['name'=>'Labor Day', 'closed'=>true, 'notes'=>null, 'date'=>now()->addWeeks(2)->toDateString()],
                ['name'=>'Thanksgiving', 'closed'=>true, 'notes'=>'Family day', 'date'=>now()->setDate(now()->year, 11, 28)->toDateString()],
            ]);

            //Tutors
            $tutors = Tutor::factory()->count(3)->create(['franchise_id' => $franchise->id]);
            foreach ($tutors as $tutor) {
                //Give tutors addresses
                $tutor->address()->create(Address::factory()->make()->toArray());

                //Give tutors instruments
                $pick = $instruments->random(rand(1,3));
                foreach ($pick as $inst) {
                    $tutor->instruments()->attach($inst->id, [
                        'proficiency'   => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
                        'years'         => fake()->numberBetween(1, 12),
                        'is_primary'    => fake()->boolean(30),
                    ]);
                }
            }

            //Guardians and Students
            //Guardians
            $guardians = Guardian::factory()->count(4)->create();
            foreach ($guardians as $g) {
                //Add addresses
                $g->address()->create(Address::factory()->make()->toArray());

                //Each Guardian gets a student or two
                $students = Student::factory()->count(rand(1, 2))->create([
                    'guardian_id'   =>$g->id,
                    'franchise_id'  =>$franchise->id,
                    'tutor_id'      =>$tutors->random()->id,
                ]);
                foreach ($students as $s) {
                    //Student addresses
                    $s->address()->create(Address::factory()->make()->toArray());

                    //Instruments for students
                    $pick = $instruments->random(rand(1,2));
                    foreach ($pick as $inst) {
                        $s->instruments()->attach($inst->id, [
                            'level'         => fake()->randomElement(['beginner', 'intermediate', 'advanced']),
                            'is_primary'    => fake()->boolean(50),
                            'started_on'    => fake()->dateTimeBetween('-2 years', '-1 month')->format('Y-m-d'),
                        ]);
                    }
                }
            }
        }
    }
}
