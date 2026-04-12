<?php

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $input = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($input)) {
            $request->session()->regenerate();
            $user = Auth::user();
            if ((int) $user->status_aktif !== 1) {
                Auth::logout();

                return back()->withErrors(['username' => 'Akun belum aktif.']);
            }

            LogAktivitas::create([
                'id_user' => $user->id_user,
                'aktivitas' => 'Login ke sistem',
                'waktu_aktivitas' => now(),
            ]);

            return match ($user->role) {
                'admin' => redirect()->route('admin.dashboard'),
                'petugas' => redirect()->route('petugas.dashboard'),
                'owner' => redirect()->route('owner.dashboard'),
                default => redirect('/login'),
            };
        }

        $usernameMember = strtolower(str_replace(' ', '', trim((string) $input['username'])));
        $member = Member::where('username_member', $usernameMember)->first();
        if (! $member || ! $member->password_member || ! Hash::check($input['password'], $member->password_member)) {
            return back()->withErrors(['username' => 'Username atau password salah.']);
        }

        if ((int) $member->status_aktif !== 1) {
            return back()->withErrors(['username' => 'Akun belum aktif.']);
        }

        $request->session()->put('member_id', $member->id_member);

        return redirect()->route('member.dashboard');
    }

    public function logout(Request $request)
    {
        if (Auth::check()) {
            LogAktivitas::create([
                'id_user' => Auth::user()->id_user,
                'aktivitas' => 'Logout dari sistem',
                'waktu_aktivitas' => now(),
            ]);
        }
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
