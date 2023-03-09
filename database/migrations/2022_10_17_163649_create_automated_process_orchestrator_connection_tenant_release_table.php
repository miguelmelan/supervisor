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
        Schema::create('automated_process_orchestrator_connection_tenant_release', function (Blueprint $table) {
            $table->unsignedBigInteger('automated_process_id');
            $table->unsignedBigInteger('release_id');
            $table->timestamps();
            $table
                ->foreign('automated_process_id', 'apoctr_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('release_id', 'apoctr_r_id_foreign')
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
        Schema::dropIfExists('automated_process_orchestrator_connection_tenant_release');
    }
};
