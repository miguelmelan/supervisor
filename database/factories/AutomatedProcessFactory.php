<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AutomatedProcess>
 */
class AutomatedProcessFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $name = fake(app()->getLocale())->company();
        return [
            'code' => getAcronym($name),
            'name' => $name,
        ];
    }
}
