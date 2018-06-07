<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWebhook extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('webhook_payloads', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->primary('uuid');

            $table->json('data');
            $table->string('status');

            $table->json('request_headers')->nullable();

            $table->string('request_method');
            $table->string('request_url');

            $table->string('user_agent');

            $table->timestamps();
        });

        Schema::create('webhook_requests', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->primary('uuid');

            $table->uuid('payload_uuid');
            $table->foreign('payload_uuid')->references('uuid')->on('webhook_payloads');

            $table->text('request_url');
            $table->text('request_headers')->nullable();
            $table->text('request_body')->nullable();

            $table->integer('response_status');
            $table->text('response_headers');
            $table->text('response_body')->nullable();

            $table->timestamps();
        });

        Schema::create('webhook_test_receive', function (Blueprint $table) {
            $table->uuid('uuid');
            $table->text('data');
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
        Schema::drop('webhook_test_receive');
        Schema::drop('webhook_requests');
        Schema::drop('webhook_payloads');
    }
}
