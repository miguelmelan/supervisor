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
        Schema::create('automated_process_property', function (Blueprint $table) {
            $table->unsignedBigInteger('automated_process_id');
            $table->unsignedBigInteger('property_id');
            $table->timestamps();
            $table
                ->foreign('automated_process_id', 'app_ap_id_foreign')
                ->references('id')
                ->on('automated_processes')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('property_id', 'app_p_id_foreign')
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
        Schema::dropIfExists('automated_process_property');
    }
};
