<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantAlert;
use Database\Factories\OrchestratorConnectionFactory;

class OrchestratorConnectionSeeder extends Seeder
{

    private function addTenants(OrchestratorConnectionFactory $factory)
    {
        return $factory->has(
            OrchestratorConnectionTenant::factory()->count(rand(env('MIN_NUMBER_OF_TENANTS'), env('MAX_NUMBER_OF_TENANTS')))
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->read(),
                    'alerts',
                )
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->becauseOfJobFaulted(),
                    'alerts',
                )
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->becauseOfQueueItemTransactionFailedAppException(),
                    'alerts',
                )
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->becauseOfQueueItemTransactionFailedBizException(),
                    'alerts',
                )
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->randomError(),
                    'alerts',
                )
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->randomWarn(),
                    'alerts',
                ),
            'tenants',
        );
    }

    private function addTenantsWithClientIdAndSecret(OrchestratorConnectionFactory $factory)
    {
        return $factory->has(
            OrchestratorConnectionTenant::factory()
                ->count(rand(env('MIN_NUMBER_OF_TENANTS'), env('MAX_NUMBER_OF_TENANTS')))
                ->withClientIdAndSecret()
                ->has(
                    OrchestratorConnectionTenantAlert::factory()
                        ->count(rand(env('MIN_NUMBER_OF_ALERTS_PER_TYPE'), env('MAX_NUMBER_OF_ALERTS_PER_TYPE')))
                        ->read(),
                    'alerts',
                ),
            'tenants',
        );
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenants($factory)
            ->hostedOnCloud()
            ->isDevelopmentInstance()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenants($factory)
            ->hostedOnCloud()
            ->isTestInstance()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenants($factory)
            ->hostedOnCloud()
            ->isProductionInstance()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenants($factory)
            ->hostedOnCloud()
            ->isHybridInstance()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenants($factory)
            ->hostedOnPremise()
            ->isDevelopmentInstance()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenantsWithClientIdAndSecret($factory)
            ->hostedOnPremise()
            ->isProductionInstance()
            ->withElasticSearchEnabledWithAuthentication()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenantsWithClientIdAndSecret($factory)
            ->hostedOnPremise()
            ->isHybridInstance()
            ->withElasticSearchEnabledWithAuthentication()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenantsWithClientIdAndSecret($factory)
            ->hostedOnPremise()
            ->isDevelopmentInstance()
            ->withElasticSearchEnabledWithoutAuthentication()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenantsWithClientIdAndSecret($factory)
            ->hostedOnPremise()
            ->isProductionInstance()
            ->withElasticSearchEnabledWithAuthentication()
            ->withKibanaEnabled()
            ->create();

        $factory = OrchestratorConnection::factory()
            ->count(rand(env('MIN_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE'), env('MAX_NUMBER_OF_ORCHESTRATOR_CONNECTIONS_PER_TYPE')));
        $this->addTenantsWithClientIdAndSecret($factory)
            ->hostedOnPremise()
            ->isTestInstance()
            ->withElasticSearchEnabledWithoutAuthentication()
            ->withKibanaEnabled()
            ->create();
    }
}
