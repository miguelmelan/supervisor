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
        Schema::create('orchestrator_connection_tenant_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tenant_id');
            $table->string('external_id');
            $table->string('external_folder_id');
            $table->timestamps();
            $table
                ->foreign('tenant_id', 'octr_t_id_foreign')
                ->references('id')
                ->on('orchestrator_connection_tenants')
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
        Schema::dropIfExists('orchestrator_connection_tenant_releases');
    }
};
