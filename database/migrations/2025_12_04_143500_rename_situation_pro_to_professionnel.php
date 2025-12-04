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
        // Rename 'situation_pro' to 'professionnel' in types_documents_pays
        DB::table('types_documents_pays')
            ->where('code', 'situation_pro')
            ->update(['code' => 'professionnel']);

        // Clean up any types that are not in the allowed list (just in case, though seeder handles this)
        // Allowed: identite, domicile, professionnel, financier, garant, autre, fiscal, solvabilite
        // We won't delete here to avoid data loss on production without backup, 
        // but the user asked to "supprime tout autres type". 
        // Since we are running migrate:fresh --seed, the seeder will handle the "only these types" requirement.
        // This migration is useful if we were not refreshing.
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Restore 'professionnel' to 'situation_pro'
        DB::table('types_documents_pays')
            ->where('code', 'professionnel')
            ->update(['code' => 'situation_pro']);
    }
};
