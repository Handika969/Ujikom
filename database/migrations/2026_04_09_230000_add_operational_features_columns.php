<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->tinyInteger('is_tiket_hilang')->default(0)->after('gateway_out_status');
            $table->decimal('denda_tiket_hilang', 12, 0)->default(0)->after('is_tiket_hilang');
            $table->string('payment_ref', 120)->nullable()->after('denda_tiket_hilang');
            $table->string('payment_proof_path', 255)->nullable()->after('payment_ref');
        });

        Schema::table('tb_topup', function (Blueprint $table) {
            $table->string('payment_proof_path', 255)->nullable()->after('ref_gateway');
            $table->text('catatan_verifikasi')->nullable()->after('payment_proof_path');
        });
    }

    public function down(): void
    {
        Schema::table('tb_topup', function (Blueprint $table) {
            $table->dropColumn(['payment_proof_path', 'catatan_verifikasi']);
        });

        Schema::table('tb_transaksi', function (Blueprint $table) {
            $table->dropColumn(['is_tiket_hilang', 'denda_tiket_hilang', 'payment_ref', 'payment_proof_path']);
        });
    }
};
