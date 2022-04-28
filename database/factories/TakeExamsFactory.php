<?php

namespace Database\Factories;

use App\Models\Exams;
use App\Models\RoundTeam;
use Faker\Provider\ar_EG\Text;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TakeExams>
 */
class TakeExamsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'exam_id' => Exams::all()->random()->id,
            'round_team_id' => RoundTeam::all()->random()->id,
            'final_point' => 10,
            'result_url' => $this->faker->url(),
            'status' => 2
        ];
    }
}
