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
        Schema::table('orchestrator_connection_tenant_alerts', function (Blueprint $table) {
            $table->unsignedBigInteger('trigger_id')->nullable();
            $table
                ->foreign('trigger_id', 'octa_tr_id_foreign')
                ->references('id')
                ->on('a_i_based_alert_triggers')
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
        Schema::table('orchestrator_connection_tenant_alerts', function (Blueprint $table) {
            //
        });
    }
};
