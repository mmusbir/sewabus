<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role', 32)->default('user')->after('email')->index();
        });

        DB::table('users')->where('id', 1)->update(['role' => 'super_admin']);

        $adminEmails = array_map('strtolower', config('auth.admin_emails', []));
        if (!empty($adminEmails)) {
            DB::table('users')
                ->whereIn('email', $adminEmails)
                ->update(['role' => 'super_admin']);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
