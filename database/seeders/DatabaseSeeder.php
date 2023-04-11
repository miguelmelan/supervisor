<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\OrchestratorConnection;
use App\Models\OrchestratorConnectionTenant;
use App\Models\OrchestratorConnectionTenantAlert;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->withPersonalTeam()->create([
            'name' => env('APP_ADMIN_NAME', 'Admin User'),
            'email' => env('APP_ADMIN_EMAIL', 'admin@example.com'),
            'password' => Hash::make(env('APP_ADMIN_PASSWORD', 'password')),
        ]);

        if (env('DEFAULT_ORCHESTRATOR_CONNECTIONS_SEEDER_ACTIVE', false)) {
            $cloudOrchestrator = OrchestratorConnection::factory()->create([
                'code' => env('CLOUD_ORCHESTRATOR_CONNECTION_NAME'),
                'name' => env('CLOUD_ORCHESTRATOR_CONNECTION_CODE'),
                'environment_type' => env('CLOUD_ORCHESTRATOR_CONNECTION_ENVIRONMENT_TYPE'),
                'hosting_type' => env('CLOUD_ORCHESTRATOR_CONNECTION_HOSTING_TYPE'),
                'organization_name' => env('CLOUD_ORCHESTRATOR_CONNECTION_ORGANIZATION_NAME'),
                'client_id' => env('CLOUD_ORCHESTRATOR_CONNECTION_CLIENT_ID'),
                'client_secret' => env('CLOUD_ORCHESTRATOR_CONNECTION_CLIENT_SECRET'),
            ]);
            $cloudOrchestrator->attachTags(explode(',', env('CLOUD_ORCHESTRATOR_CONNECTION_TAGS')));
            $tenant = OrchestratorConnectionTenant::factory()->for($cloudOrchestrator)
                ->create([
                    'name' => env('CLOUD_ORCHESTRATOR_CONNECTION_TENANT_NAME'),
                ]);
            $this->addAlertsToTenant($tenant);

            $onPremiseOrchestrator = OrchestratorConnection::factory()->create([
                'code' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_CODE'),
                'name' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_NAME'),
                'environment_type' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_ENVIRONMENT_TYPE'),
                'hosting_type' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_HOSTING_TYPE'),
                'url' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_URL'),
            ]);
            $onPremiseOrchestrator->attachTags(explode(',', env('ON_PREMISE_ORCHESTRATOR_CONNECTION_TAGS')));
            $tenant = OrchestratorConnectionTenant::factory()->for($onPremiseOrchestrator)
                ->create([
                    'name' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_TENANT_NAME'),
                    'client_id' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_TENANT_CLIENT_ID'),
                    'client_secret' => env('ON_PREMISE_ORCHESTRATOR_CONNECTION_TENANT_CLIENT_SECRET'),
                ]);
            $this->addAlertsToTenant($tenant);
        }

        if (env('ADDITIONAL_ORCHESTRATOR_CONNECTIONS_SEEDER_ACTIVE', false)) {
            $this->call([
                OrchestratorConnectionSeeder::class,
            ]);
        }

        /* $this->call([
            AutomatedProcessSeeder::class,
            PropertyKeySeeder::class,
        ]); */
    }

    private function addAlertsToTenant($tenant)
    {

        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfJobFaulted()
            ->read()->create();

        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfQueueItemTransactionFailedAppException()
            ->read()->create();

        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfQueueItemTransactionFailedBizException()
            ->read()->create();
        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfJobFaulted()->create();
        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfQueueItemTransactionFailedAppException()->create();
        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->becauseOfQueueItemTransactionFailedBizException()->create();
        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->randomError()->create();
        OrchestratorConnectionTenantAlert::factory()->for($tenant, 'tenant')->count(
            rand(
                env('MIN_NUMBER_OF_ALERTS_PER_TYPE'),
                env('MAX_NUMBER_OF_ALERTS_PER_TYPE')
            )
        )
            ->randomWarn()->create();
    }
}
