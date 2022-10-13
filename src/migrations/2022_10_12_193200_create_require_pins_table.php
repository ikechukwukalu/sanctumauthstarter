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
        Schema::create('require_pins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('uuid')->unique();
            $table->string('ip');
            $table->string('device');
            $table->string('method');
            $table->text('route_arrested');
            $table->longText('payload');
            $table->text('redirect_to')->nullable()->default(config('sanctumauthstarter.pin.redirect_to', null));
            $table->text('pin_validation_url');
            $table->timestamp('approved_at', $precision = 0)->nullable();
            $table->timestamp('cancelled_at', $precision = 0)->nullable();
            $table->timestamp('expires_at')->default( \DB::raw("DATE_ADD(now(), INTERVAL " . config('sanctumauthstarter.pin.duration', 120) . " SECOND)"));
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
        Schema::dropIfExists('require_pins');
    }
};
