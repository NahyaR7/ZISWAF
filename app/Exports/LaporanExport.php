<?php

namespace App\Exports;

use App\Models\Transaction;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class LaporanExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    /**
    * Mengambil data transaksi yang statusnya "Tersalur"
    */
    public function collection()
    {
        return Transaction::with(['user', 'jenisZiswaf', 'payment'])
            ->where('status', 'Tersalur')
            ->latest()
            ->get();
    }

    /**
    * Menentukan Judul Kolom (Header) di Excel
    */
    public function headings(): array
    {
        return [
            'ID Transaksi',
            'Tanggal',
            'Muzakki / Donatur',
            'Jenis ZISWAF',
            'Nominal (Rp)',
            'Metode Pembayaran',
            'Lembaga Penyalur',
            'No. Kwitansi'
        ];
    }

    /**
    * Memetakan data dari database ke masing-masing kolom Excel
    */
    public function map($trx): array
    {
        return [
            $trx->transaction_code,
            $trx->created_at->format('d M Y H:i'),
            $trx->user->name ?? 'Hamba Allah',
            $trx->jenisZiswaf->nama_jenis ?? 'Zakat',
            $trx->amount,
            $trx->payment->metode ?? 'Transfer Bank',
            $trx->organization,
            $trx->kwitansi_number ?? '-'
        ];
    }
}