<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function indexAdmin()
    {
        // Mengambil semua data transaksi terbaru
        $recentTransactions = Transaction::latest()->take(5)->get();
        
        return view('pages.dashboard', [
            'recentTransactions' => $recentTransactions
        ]);
    }

    public function indexUser()
    {
        return view('pages.dashboard-user');
    }
}