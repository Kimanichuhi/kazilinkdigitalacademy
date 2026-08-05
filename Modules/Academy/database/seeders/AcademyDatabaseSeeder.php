<?php

namespace Modules\Academy\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Academy\Models\Cohort;
use Modules\Academy\Models\Program;
use Modules\Academy\Models\ProgramCategory;
use Modules\Academy\Models\Trainer;

class AcademyDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ProgramCategory::factory()->count(8)->create();
        $trainers = Trainer::factory()->count(5)->create();

        $categories->each(function (ProgramCategory $category) use ($trainers) {
            Program::factory()
                ->count(2)
                ->create(['category_id' => $category->id])
                ->each(function (Program $program) use ($trainers) {
                    Cohort::factory()->create([
                        'program_id' => $program->id,
                        'trainer_id' => $trainers->random()->id,
                    ]);
                });
        });
    }
}
