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
        Schema::create('orchestrator_connection_property', function (Blueprint $table) {
            $table->unsignedBigInteger('orchestrator_connection_id');
            $table->unsignedBigInteger('property_id');
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_id', 'ocp_oc_id_foreign')
                ->references('id')
                ->on('orchestrator_connections')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('property_id', 'ocp_p_id_foreign')
                ->references('id')
                ->on('properties')
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
        Schema::dropIfExists('orchestrator_connection_property');
    }
};
