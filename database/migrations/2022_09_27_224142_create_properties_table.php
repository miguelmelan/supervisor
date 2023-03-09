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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('key_id');
            $table->unsignedBigInteger('value_id');
            $table->timestamps();
            $table
                ->foreign('key_id', 'p1_id_foreign')
                ->references('id')
                ->on('property_keys')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('value_id', 'p2_id_foreign')
                ->references('id')
                ->on('property_values')
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
        Schema::dropIfExists('properties');
    }
};
