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
        Schema::create('automated_process_orchestrator_connection_tenant_alert', function (Blueprint $table) {
            $table->unsignedBigInteger('automated_process_id');
            $table->unsignedBigInteger('orchestrator_connection_tenant_alert_id');
            $table->timestamps();
            $table
                ->foreign('automated_process_id', 'apocta_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('orchestrator_connection_tenant_alert_id', 'apocta_octa_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_alerts')
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
        Schema::dropIfExists('automated_process_orchestrator_connection_tenant_alert');
    }
};
