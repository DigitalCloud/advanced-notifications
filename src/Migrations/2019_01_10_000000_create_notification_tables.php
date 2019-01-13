<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotificationTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::create('notification_notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('type');
            $table->boolean('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('notification_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('notification_channels', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->boolean('status');
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('notifiables', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('notifiable_id');
            $table->string('notifiable_type');
            $table->unsignedBigInteger('model_id')->nullable();
            $table->string('model_type');
            $table->boolean('status');
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
        Schema::drop('notification_notifications');
        Schema::drop('notification_groups');
        Schema::drop('notification_channels');
        Schema::drop('notifiables');
    }
}
