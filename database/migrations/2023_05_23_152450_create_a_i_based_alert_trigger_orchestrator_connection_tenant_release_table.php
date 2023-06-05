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
        Schema::create('a_i_based_alert_trigger_orchestrator_connection_tenant_release', function (Blueprint $table) {
            $table->unsignedBigInteger('a_i_based_alert_trigger_id');
            $table->unsignedBigInteger('release_id');
            $table->timestamps();
            $table
                ->foreign('a_i_based_alert_trigger_id', 'aibatoctr_aibat_id_foreign')
                ->references('id')
                ->on('a_i_based_alert_triggers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('release_id', 'aibatoctr_octr_id_foreign')
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
        Schema::dropIfExists('a_i_based_alert_trigger_orchestrator_connection_tenant_release');
    }
};
