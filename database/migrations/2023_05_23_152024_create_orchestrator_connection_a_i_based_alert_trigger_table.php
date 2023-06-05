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
        Schema::create('orchestrator_connection_a_i_based_alert_trigger', function (Blueprint $table) {
            $table->unsignedBigInteger('orchestrator_connection_id');
            $table->unsignedBigInteger('a_i_based_alert_trigger_id');
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_id', 'ocaibat_oc_id_foreign')
                ->references('id')
                ->on('orchestrator_connections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('a_i_based_alert_trigger_id', 'ocaibat_aibat_id_foreign')
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
        Schema::dropIfExists('orchestrator_connection_a_i_based_alert_trigger');
    }
};
