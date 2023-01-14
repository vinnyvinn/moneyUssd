<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUssdNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ussd_notifications', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('sessionId');
            $table->string('serviceCode');
            $table->string('networkCode');
            $table->string('phoneNumber');
            $table->string('status');
            $table->string('cost');
            $table->string('durationInMillis');
            $table->string('hopsCount');
            $table->string('input');
            $table->string('lastAppResponse');
            $table->string('errorMessage');
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
        Schema::dropIfExists('ussd_notifications');
    }
}
