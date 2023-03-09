<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrchestratorConnectionTenant>
 */
class OrchestratorConnectionTenantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => fake(app()->getLocale())->word(),
        ];
    }

    public function withClientIdAndSecret()
    {
        return $this->state(function (array $attributes) {
            return [
                'client_id' => fake(app()->getLocale())->uuid(),
                'client_secret' => fake(app()->getLocale())->word(),
            ];
        });
    }
}
