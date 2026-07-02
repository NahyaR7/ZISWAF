<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class AnggotaExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    public function collection()
    {
        return User::where('role', 'nasabah')
            ->withCount('transactions')
            ->withSum(['transactions as total_disetor' => function ($q) {
                $q->where('status', 'Tersalur');
            }], 'amount')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Username',
            'Email',
            'No. HP',
            'Jumlah Transaksi',
            'Total Disetor (Rp)',
        ];
    }

    public function map($user): array
    {
        return [
            $user->name,
            $user->username,
            $user->email,
            $user->no_hp ?? '-',
            $user->transactions_count,
            $user->total_disetor ?? 0,
        ];
    }
}
