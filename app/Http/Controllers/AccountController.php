<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|string|min:8|confirmed',
        ]);

        $user = auth()->user();

        if (! Hash::check($request->password_lama, $user->password)) {
            return redirect()->back()->withErrors(['password_lama' => 'Password lama tidak sesuai.']);
        }

        $user->update(['password' => Hash::make($request->password_baru)]);

        return redirect()->back()->with('success', 'Password berhasil diperbarui!');
    }
}
