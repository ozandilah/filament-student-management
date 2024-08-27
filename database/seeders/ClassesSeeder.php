<?php

namespace Database\Seeders;

use App\Models\Classes;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Sequence as FactoriesSequence;
use Illuminate\Database\Seeder;

class ClassesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Classes::factory()
            ->count(10)
            ->sequence(fn($sequence)=> ['name'=> 'Class' . $sequence->index +1])
            ->has(
                Section::factory()
                ->count(2)
                ->state(
                    new FactoriesSequence(
                        ['name' => 'Section A'],
                        ['name' => 'Section B']
                    )
                )
                ->has(
                    Student::factory()
                    ->count(5)
                    ->state(
                        function (array $attributes, Section $section){
                            return ['class_id' => $section->class_id];
                        }
                    )
                )
            )
            ->create();
        }
}
