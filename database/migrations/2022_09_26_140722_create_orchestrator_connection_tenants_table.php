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
        Schema::create('orchestrator_connection_tenants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('orchestrator_connection_id');
            $table->string('name');
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('webhook_secret');
            $table->string('webhook_id')->nullable();
            $table->string('uuid');
            $table->boolean('verified')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table
                ->foreign('orchestrator_connection_id', 'oc_id_foreign')
                ->references('id')
                ->on('orchestrator_connections')
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
        Schema::dropIfExists('orchestrator_connection_tenants');
    }
};
