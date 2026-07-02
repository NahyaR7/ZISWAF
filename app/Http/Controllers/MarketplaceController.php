<?php

namespace App\Http\Controllers;

use App\Models\JenisZiswaf;
use App\Models\Payment;
use App\Models\Program;
use App\Models\Transaction;
use App\Notifications\ZiswafNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MarketplaceController extends Controller
{
    public function index(Request $request)
    {
        $query = Program::query();

        if ($request->filled('filter') && $request->filter !== 'semua') {
            $query->where('tipe', $request->filter);
        }

        $programs = $query->latest()->get();

        return view('pages.marketplace', compact('programs'));
    }

    public function donasi(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'nama' => 'required|string|max:255',
            'nominal' => 'required|integer|min:10000',
            'metode' => 'required|string',
        ]);

        $program = Program::findOrFail($validated['program_id']);
        $jenisNama = match ($program->tipe) {
            'zakat' => 'Zakat',
            'wakaf' => 'Wakaf',
            default => 'Infaq',
        };
        $jenis = JenisZiswaf::firstOrCreate(['nama_jenis' => $jenisNama]);

        $payment = Payment::create([
            'metode' => $validated['metode'],
            'status_payment' => ($validated['metode'] === 'Transfer Bank') ? 'Pending' : 'Success',
            'tanggal_payment' => now(),
        ]);

        $status = ($validated['metode'] === 'Transfer Bank') ? 'Diproses' : 'Tersalur';
        $kwitansiNumber = ($status === 'Tersalur') ? 'KW-' . date('Y') . '-' . rand(1000, 9999) : null;

        $transaction = Transaction::create([
            'transaction_code' => 'TRX-' . date('ym') . '-' . strtoupper(Str::random(4)),
            'user_id' => auth()->id(),
            'jenis_ziswaf_id' => $jenis->id,
            'program_id' => $program->id,
            'payment_id' => $payment->id,
            'amount' => $validated['nominal'],
            'organization' => $program->organisasi,
            'status' => $status,
            'kwitansi_number' => $kwitansiNumber,
        ]);

        if ($status === 'Tersalur') {
            auth()->user()->notify(new ZiswafNotification(
                "Donasi Anda untuk program {$program->nama} Rp " . number_format($validated['nominal'], 0, ',', '.') . " berhasil. Kwitansi: " . $kwitansiNumber
            ));
        }

        return response()->json([
            'success' => true,
            'transaction_code' => $transaction->transaction_code,
            'kwitansi_number' => $transaction->kwitansi_number,
            'status' => $transaction->status,
        ]);
    }

    public function storeProgram(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'organisasi' => 'required|string|max:255',
            'tipe' => 'required|in:zakat,infaq,wakaf,sosial',
            'icon' => 'nullable|string|max:10',
            'target' => 'required|numeric|min:0',
        ]);

        Program::create($request->only('nama', 'organisasi', 'tipe', 'icon', 'target'));

        return redirect()->back()->with('success', 'Program donasi berhasil ditambahkan!');
    }

    public function destroyProgram($id)
    {
        Program::destroy($id);

        return redirect()->back()->with('success', 'Program donasi berhasil dihapus!');
    }
}
