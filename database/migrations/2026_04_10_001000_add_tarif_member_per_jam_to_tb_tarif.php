<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->decimal('tarif_member_per_jam', 12, 0)->nullable()->after('tarif_per_jam');
        });

        DB::table('tb_tarif')
            ->whereNull('tarif_member_per_jam')
            ->update(['tarif_member_per_jam' => DB::raw('tarif_per_jam')]);
    }

    public function down(): void
    {
        Schema::table('tb_tarif', function (Blueprint $table) {
            $table->dropColumn('tarif_member_per_jam');
        });
    }
};
