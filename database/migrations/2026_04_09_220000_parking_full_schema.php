<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tb_user', function (Blueprint $table) {
            $table->integer('id_user')->autoIncrement();
            $table->string('nama_lengkap', 100);
            $table->string('username', 50)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'petugas', 'owner']);
            $table->tinyInteger('status_aktif')->default(1);
            $table->timestamps();
        });

        Schema::create('tb_tarif', function (Blueprint $table) {
            $table->integer('id_tarif')->autoIncrement();
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);
            $table->decimal('tarif_per_jam', 12, 0);
            $table->timestamps();
        });

        Schema::create('tb_area_parkir', function (Blueprint $table) {
            $table->integer('id_area')->autoIncrement();
            $table->string('nama_area', 100);
            $table->integer('kapasitas');
            $table->integer('terisi')->default(0);
            $table->timestamps();
        });

        Schema::create('tb_member', function (Blueprint $table) {
            $table->integer('id_member')->autoIncrement();
            $table->string('nama_member', 100);
            $table->string('username_member', 50)->nullable()->unique();
            $table->string('password_member', 255)->nullable();
            $table->string('no_hp', 12)->nullable();
            $table->string('alamat', 150)->nullable();
            $table->decimal('saldo', 12, 0)->default(0);
            $table->tinyInteger('status_aktif')->default(1);
            $table->timestamps();
        });

        Schema::create('tb_kendaraan', function (Blueprint $table) {
            $table->integer('id_kendaraan')->autoIncrement();
            $table->string('plat_nomor', 15)->unique();
            $table->enum('jenis_kendaraan', ['motor', 'mobil', 'lainnya']);
            $table->string('pemilik', 100)->nullable();
            $table->integer('id_member')->nullable();
            $table->foreign('id_member')->references('id_member')->on('tb_member')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('tb_transaksi', function (Blueprint $table) {
            $table->integer('id_parkir')->autoIncrement();
            $table->integer('id_kendaraan');
            $table->integer('id_tarif');
            $table->integer('id_user');
            $table->integer('id_area');
            $table->dateTime('waktu_masuk');
            $table->dateTime('waktu_keluar')->nullable();
            $table->integer('durasi_jam')->default(0);
            $table->decimal('biaya_total', 12, 0)->default(0);
            $table->enum('status', ['masuk', 'keluar'])->default('masuk');
            $table->enum('metode_bayar', ['tunai', 'saldo'])->nullable();
            $table->string('kode_qr_tiket', 50)->nullable();
            $table->string('gateway_in_status', 30)->nullable();
            $table->string('gateway_out_status', 30)->nullable();
            $table->timestamps();

            $table->foreign('id_kendaraan')->references('id_kendaraan')->on('tb_kendaraan');
            $table->foreign('id_tarif')->references('id_tarif')->on('tb_tarif');
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_area')->references('id_area')->on('tb_area_parkir');
        });

        Schema::create('tb_topup', function (Blueprint $table) {
            $table->integer('id_topup')->autoIncrement();
            $table->integer('id_member');
            $table->decimal('nominal', 12, 0);
            $table->enum('metode', ['cash', 'qris', 'va', 'ewallet']);
            $table->enum('status', ['pending', 'success', 'failed'])->default('pending');
            $table->string('ref_gateway', 100)->nullable();
            $table->integer('verified_by')->nullable();
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('tb_member')->cascadeOnDelete();
            $table->foreign('verified_by')->references('id_user')->on('tb_user')->nullOnDelete();
        });

        Schema::create('tb_saldo_mutasi', function (Blueprint $table) {
            $table->integer('id_mutasi')->autoIncrement();
            $table->integer('id_member');
            $table->enum('tipe', ['debit', 'kredit']);
            $table->decimal('nominal', 12, 0);
            $table->string('sumber', 40);
            $table->integer('id_ref')->nullable();
            $table->decimal('saldo_sebelum', 12, 0);
            $table->decimal('saldo_sesudah', 12, 0);
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('tb_member')->cascadeOnDelete();
        });

        Schema::create('tb_notifikasi_member', function (Blueprint $table) {
            $table->integer('id_notifikasi')->autoIncrement();
            $table->integer('id_member');
            $table->string('judul', 120);
            $table->text('isi');
            $table->timestamp('dibaca_at')->nullable();
            $table->timestamps();
            $table->foreign('id_member')->references('id_member')->on('tb_member')->cascadeOnDelete();
        });

        Schema::create('tb_log_aktivitas', function (Blueprint $table) {
            $table->integer('id_log')->autoIncrement();
            $table->integer('id_user');
            $table->string('aktivitas', 150);
            $table->dateTime('waktu_aktivitas')->useCurrent();
            $table->foreign('id_user')->references('id_user')->on('tb_user')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tb_log_aktivitas');
        Schema::dropIfExists('tb_notifikasi_member');
        Schema::dropIfExists('tb_saldo_mutasi');
        Schema::dropIfExists('tb_topup');
        Schema::dropIfExists('tb_transaksi');
        Schema::dropIfExists('tb_kendaraan');
        Schema::dropIfExists('tb_member');
        Schema::dropIfExists('tb_area_parkir');
        Schema::dropIfExists('tb_tarif');
        Schema::dropIfExists('tb_user');
    }
};
