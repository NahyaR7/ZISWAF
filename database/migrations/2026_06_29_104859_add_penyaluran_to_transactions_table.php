<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('bukti_penyaluran')->nullable();
            $table->text('keterangan_penyaluran')->nullable();
            $table->timestamp('tanggal_penyaluran')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['bukti_penyaluran', 'keterangan_penyaluran', 'tanggal_penyaluran']);
        });
    }
};