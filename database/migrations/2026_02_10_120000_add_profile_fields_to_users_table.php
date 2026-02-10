<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('jabatan')->nullable()->after('email');
            $table->string('no_handphone', 50)->nullable()->after('jabatan');
            $table->string('no_ktp', 50)->nullable()->after('no_handphone');
            $table->string('lokasi_kerja')->nullable()->after('no_ktp');
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
            $table->dropColumn(['jabatan', 'no_handphone', 'no_ktp', 'lokasi_kerja']);
        });
    }
}
