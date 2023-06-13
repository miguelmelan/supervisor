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
        Schema::create('orchestrator_connection_tenant_alert_oct_release', function (Blueprint $table) {
            $table->unsignedBigInteger('orchestrator_connection_tenant_alert_id');
            $table->unsignedBigInteger('orchestrator_connection_tenant_release_id');
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_tenant_alert_id', 'octaoctr_octa_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_alerts')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('orchestrator_connection_tenant_release_id', 'octaoctr_octr_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_releases')
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
        Schema::dropIfExists('orchestrator_connection_tenant_alert_oct_release');
    }
};
