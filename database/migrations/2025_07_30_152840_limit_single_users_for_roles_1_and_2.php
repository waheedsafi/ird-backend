<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Unique index for role_id = 1
        DB::statement("
            CREATE UNIQUE INDEX one_user_with_role_id_1
            ON users ((role_id))
            WHERE role_id = 1;
        ");

        // Unique index for role_id = 2
        DB::statement("
            CREATE UNIQUE INDEX one_user_with_role_id_2
            ON users ((role_id))
            WHERE role_id = 2;
        ");
    }

    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS one_user_with_role_id_1;");
        DB::statement("DROP INDEX IF EXISTS one_user_with_role_id_2;");
    }
};
