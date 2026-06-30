<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_code')->unique(); // e.g., TRX-2605-001
            $table->string('member_name');
            $table->string('type'); // Zakat, Infaq, Wakaf
            $table->decimal('amount', 15, 2);
            $table->string('method'); // Transfer, QRIS, Auto-Debit
            $table->string('organization'); // BAZNAS, Dompet Dhuafa
            $table->string('status')->default('Diproses'); // Diproses, Tersalur, Gagal
            $table->string('kwitansi_number')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};