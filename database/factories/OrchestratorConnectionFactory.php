<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrchestratorConnection>
 */
class OrchestratorConnectionFactory extends Factory
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

    public function hostedOnCloud()
    {
        return $this->state(function (array $attributes) {
            return [
                'hosting_type' => 'cloud',
                'client_id' => fake(app()->getLocale())->uuid(),
                'client_secret' => fake(app()->getLocale())->word(),
                'url' => null,
                'organization_name' => fake(app()->getLocale())->word(),
            ];
        });
    }

    public function hostedOnPremise()
    {
        return $this->state(function (array $attributes) {
            return [
                'hosting_type' => 'on_premise',
                'organization_name' => null,
                'url' => fake(app()->getLocale())->url(),
            ];
        });
    }

    public function isDevelopmentInstance()
    {
        return $this->state(function (array $attributes) {
            return [
                'environment_type' => 'development',
            ];
        });
    }

    public function isTestInstance()
    {
        return $this->state(function (array $attributes) {
            return [
                'environment_type' => 'test',
            ];
        });
    }

    public function isProductionInstance()
    {
        return $this->state(function (array $attributes) {
            return [
                'environment_type' => 'production',
            ];
        });
    }

    public function isHybridInstance()
    {
        return $this->state(function (array $attributes) {
            return [
                'environment_type' => 'hybrid',
            ];
        });
    }

    public function withElasticSearchEnabledWithAuthentication()
    {
        return $this->state(function (array $attributes) {
            return [
                'elasticsearch_enabled' => true,
                'elasticsearch_index_configuration' => '${event-properties:item=indexName}-${date:format=yyyy.MM}',
                'elasticsearch_url' => fake(app()->getLocale())->url(),
                'elasticsearch_anonymous_authentication' => false,
                'elasticsearch_username' => fake(app()->getLocale())->userName(),
                'elasticsearch_password' => fake(app()->getLocale())->password(),
            ];
        });
    }

    public function withElasticSearchEnabledWithoutAuthentication()
    {
        return $this->state(function (array $attributes) {
            return [
                'elasticsearch_enabled' => true,
                'elasticsearch_anonymous_authentication' => true,
                'elasticsearch_index_configuration' => '${event-properties:item=indexName}-${date:format=yyyy.MM}',
                'elasticsearch_url' => fake(app()->getLocale())->url(),
            ];
        });
    }

    public function withKibanaEnabled()
    {
        return $this->state(function (array $attributes) {
            return [
                'kibana_enabled' => true,
                'kibana_url' => fake(app()->getLocale())->url(),
            ];
        });
    }
}
