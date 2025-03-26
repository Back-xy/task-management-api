<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $creator = User::where('role', 'product_owner')->inRandomOrder()->first();
        $developer = User::where('role', 'developer')->inRandomOrder()->first();

        return [
            'title'       => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'status'      => fake()->randomElement(['TODO', 'IN_PROGRESS', 'READY_FOR_TEST', 'PO_REVIEW', 'DONE', 'REJECTED']),
            'due_date'    => fake()->dateTimeBetween('+1 day', '+10 days'),
            'created_by'  => $creator ? $creator->id : null,
            'assigned_to' => $developer ? $developer->id : null,
            'parent_id'   => null,
        ];
    }
}
