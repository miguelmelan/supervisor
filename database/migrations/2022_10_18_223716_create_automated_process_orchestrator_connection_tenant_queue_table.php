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
        Schema::create('automated_process_orchestrator_connection_tenant_queue', function (Blueprint $table) {
            $table->unsignedBigInteger('automated_process_id');
            $table->unsignedBigInteger('queue_id');
            $table->timestamps();
            $table
                ->foreign('automated_process_id', 'apoctq_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('queue_id', 'apoctq_r_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_queues')
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
        Schema::dropIfExists('automated_process_orchestrator_connection_tenant_queue');
    }
};
