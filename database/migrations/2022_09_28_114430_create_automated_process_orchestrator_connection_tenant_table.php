<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('automated_process_orchestrator_connection_tenant', function (Blueprint $table) {
            $table->unsignedBigInteger('automated_process_id');
            $table->unsignedBigInteger('tenant_id');
            $table->boolean('built_in_alerts')->default(false);
            $table->boolean('processes_supervision')->default(false);
            $table->boolean('machines_supervision')->default(false);
            $table->boolean('queues_supervision')->default(false);
            $table->boolean('kibana_built_in_alerts')->default(false);
            $table->timestamps();
            $table
                ->foreign('automated_process_id', 'apoct_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('tenant_id', 'apoct_t_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('automated_process_orchestrator_connection_tenant');
    }
};
