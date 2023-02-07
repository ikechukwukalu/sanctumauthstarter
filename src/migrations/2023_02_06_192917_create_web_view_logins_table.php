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
        Schema::create('web_view_logins', function (Blueprint $table) {
            $table->id();
            $table->string('user_uuid', 45);
            $table->foreignId('user_id')->nullable();
            $table->string('email')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('password')->nullable();
            $table->string('salt')->nullable();
            $table->enum('type', ['socialite', 'twofactor']);
            $table->tinyInteger('used')->default(0);
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
        Schema::dropIfExists('web_view_logins');
    }
};
