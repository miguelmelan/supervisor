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
        Schema::create('a_i_based_alert_trigger_automated_process', function (Blueprint $table) {
            $table->unsignedBigInteger('a_i_based_alert_trigger_id');
            $table->unsignedBigInteger('automated_process_id');
            $table->timestamps();
            $table
                ->foreign('a_i_based_alert_trigger_id', 'aibatap_aibat_id_foreign')
                ->references('id')
                ->on('a_i_based_alert_triggers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table
                ->foreign('automated_process_id', 'aibatap_ap_id_foreign')
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
        Schema::dropIfExists('a_i_based_alert_trigger_automated_process');
    }
};
