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
        Schema::table('employees', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_profile_id')->nullable();
            $table->string('site_id')->nullable();

            $table->foreign('permission_profile_id')
                ->references('id')
                ->on('permission_profiles')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['permission_profile_id']);
            $table->dropColumn(['permission_profile_id', 'site_id']);
        });
    }
};
