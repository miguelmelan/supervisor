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
        Schema::create('orchestrator_connection_tenant_alert_oct_machine', function (Blueprint $table) {
            $table->unsignedBigInteger('orchestrator_connection_tenant_alert_id');
            $table->unsignedBigInteger('orchestrator_connection_tenant_machine_id');
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_tenant_alert_id', 'octaoctm_octa_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_alerts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('orchestrator_connection_tenant_machine_id', 'octaoctm_octm_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_machines')
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
        Schema::dropIfExists('orchestrator_connection_tenant_alert_oct_machine');
    }
};
