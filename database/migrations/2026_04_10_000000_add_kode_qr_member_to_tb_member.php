<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_member', function (Blueprint $table) {
            $table->string('kode_qr_member', 50)->nullable()->unique()->after('alamat');
        });

        $members = DB::table('tb_member')->select('id_member')->get();
        foreach ($members as $member) {
            DB::table('tb_member')
                ->where('id_member', $member->id_member)
                ->update([
                    'kode_qr_member' => 'MBR-'.str_pad((string) $member->id_member, 6, '0', STR_PAD_LEFT).'-'.Str::upper(Str::random(4)),
                ]);
        }
    }

    public function down(): void
    {
        Schema::table('tb_member', function (Blueprint $table) {
            $table->dropUnique(['kode_qr_member']);
            $table->dropColumn('kode_qr_member');
        });
    }
};
