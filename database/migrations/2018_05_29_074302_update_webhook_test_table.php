<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateWebhookTestTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('webhook_test_receive', function (Blueprint $table) {
            $table->uuid('payload_uuid')->index()->default('00000000-0000-0000-0000-000000000000');
            $table->uuid('request_uuid')->index()->default('00000000-0000-0000-0000-000000000000');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('webhook_test_receive', function (Blueprint $table) {
            $table->dropColumn('payload_uuid');
            $table->dropColumn('request_uuid');
        });
    }
}
