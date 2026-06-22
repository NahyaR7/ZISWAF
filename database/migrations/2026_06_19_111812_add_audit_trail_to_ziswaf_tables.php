<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Menambah rekam jejak di tabel transaksi
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
        });

        // Menambah rekam jejak siapa yang mencetak laporan
        Schema::table('laporan', function (Blueprint $table) {
            $table->foreignId('generated_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['verified_by']);
            $table->dropColumn(['verified_by', 'verified_at']);
        });

        Schema::table('laporan', function (Blueprint $table) {
            $table->dropForeign(['generated_by']);
            $table->dropColumn('generated_by');
        });
    }
};