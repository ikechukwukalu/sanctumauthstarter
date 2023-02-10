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
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->tinyInteger('socialite_signup')->default(false);
            $table->tinyInteger('form_signup')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->change();
        });

        if (Schema::hasColumn('users', 'socialite_signup'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('socialite_signup');
            });
        }

        if (Schema::hasColumn('users', 'form_signup'))
        {
            Schema::table('users', function (Blueprint $table)
            {
                $table->dropColumn('form_signup');
            });
        }
    }
};
