<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('otp_code')->nullable();
             $table->timestamp('otp_expires_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('usuarios', function (Blueprint $table) {
            //
        });
    }
}
