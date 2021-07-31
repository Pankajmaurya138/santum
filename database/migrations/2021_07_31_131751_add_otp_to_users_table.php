<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('otp')->after('registered_at')->nullable();
            $table->enum('status',[1,0])->default(0)->comment('1 for active account 0 for not active')->after('registered_at')->nullable();
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
            $table->string('otp')->after('registered_at')->comment('1 for active account 0 for not active')->nullable();
            $table->enum('status',[1,0])->default(0)->after('otp');
        });
    }
}
