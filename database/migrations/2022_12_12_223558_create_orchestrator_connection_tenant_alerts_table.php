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
        Schema::create('orchestrator_connection_tenant_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->unsignedBigInteger('automated_process_id')->nullable();
            $table->unsignedBigInteger('release_id')->nullable();
            $table->unsignedBigInteger('machine_id')->nullable();
            $table->unsignedBigInteger('queue_id')->nullable();
            $table->unsignedBigInteger('read_by')->nullable();
            $table->unsignedBigInteger('locked_by')->nullable();
            $table->string('external_id')->nullable();
            $table->string('notification_name');
            $table->json('data');
            $table->string('component');
            $table->string('severity');
            $table->timestamp('creation_time');
            $table->text('deep_link_relative_url')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->integer('resolution_time_in_seconds')->nullable();
            $table->timestamp('locked_at')->nullable();
            $table->timestamps();
            $table
                ->foreign('tenant_id', 'octa_t_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenants')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('automated_process_id', 'octa_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('release_id', 'octa_r_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_releases')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('machine_id', 'octa_m_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenant_machines')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('queue_id', 'octa_q_id_foreign')
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
        Schema::dropIfExists('orchestrator_connection_tenant_alerts');
    }
};
