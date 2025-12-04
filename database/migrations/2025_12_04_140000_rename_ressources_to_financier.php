<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Rename 'ressources' to 'financier' in types_documents_pays
        DB::table('types_documents_pays')
            ->where('code', 'ressources')
            ->update(['code' => 'financier']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore 'financier' to 'ressources'
        DB::table('types_documents_pays')
            ->where('code', 'financier')
            ->update(['code' => 'ressources']);
    }
};
