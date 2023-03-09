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
        Schema::create('orchestrator_connections', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name')->unique();
            $table->string('hosting_type');
            $table->string('environment_type');
            $table->string('client_id')->nullable();
            $table->string('client_secret')->nullable();
            $table->string('url')->nullable(true)->unique();
            $table->string('organization_name')->nullable()->unique();
            $table->boolean('elasticsearch_enabled')->default(false);
            $table->string('elasticsearch_index_configuration')->nullable();
            $table->string('elasticsearch_url')->nullable();
            $table->boolean('elasticsearch_anonymous_authentication')->default(false);
            $table->string('elasticsearch_username')->nullable();
            $table->string('elasticsearch_password')->nullable();
            $table->boolean('kibana_enabled')->default(false);
            $table->string('kibana_url')->nullable();
            $table->boolean('verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orchestrator_connections');
    }
};
