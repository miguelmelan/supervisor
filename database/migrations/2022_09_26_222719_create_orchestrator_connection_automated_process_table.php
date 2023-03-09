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
        Schema::create('orchestrator_connection_automated_process', function (Blueprint $table) {
            $table->unsignedBigInteger('orchestrator_connection_id');
            $table->unsignedBigInteger('automated_process_id');
            $table->boolean('built_in_alerts')->default(false);
            $table->boolean('processes_supervision')->default(false);
            $table->boolean('machines_supervision')->default(false);
            $table->boolean('queues_supervision')->default(false);
            $table->boolean('kibana_built_in_alerts')->default(false);
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_id', 'ocap_oc_id_foreign')
                ->references('id')
                ->on('orchestrator_connections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('automated_process_id', 'ocap_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
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
        Schema::dropIfExists('orchestrator_connection_automated_process');
    }
};
