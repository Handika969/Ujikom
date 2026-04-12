<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MemberAuthController extends Controller
{
    public function checkUsername(Request $request)
    {
        $username = strtolower(str_replace(' ', '', trim((string) $request->query('username_member', ''))));
        if ($username === '') {
            return response()->json(['ok' => false, 'message' => 'Username kosong']);
        }

        $exists = Member::where('username_member', $username)->exists();

        return response()->json([
            'ok' => true,
            'available' => ! $exists,
            'message' => $exists ? 'Username sudah dipakai' : 'Username tersedia',
        ]);
    }

    public function showRegister()
    {
        return view('member.auth.register');
    }

    public function register(Request $request)
    {
        $request->merge([
            'username_member' => strtolower(str_replace(' ', '', trim((string) $request->input('username_member')))),
        ]);

        $v = $request->validate([
            'nama_member' => 'required|max:100',
            'username_member' => ['required', 'max:50', 'alpha_num', 'unique:tb_member,username_member'],
            'no_hp' => ['required', 'regex:/^08\d{10}$/'],
            'alamat' => 'nullable|max:150',
            'password' => 'required|min:4|confirmed',
        ], [
            'no_hp.regex' => 'No HP harus 12 digit dan diawali 08.',
            'username_member.alpha_num' => 'Username hanya boleh huruf dan angka tanpa spasi.',
        ]);

        $member = Member::create([
            'nama_member' => $v['nama_member'],
            'username_member' => $v['username_member'],
            'password_member' => Hash::make($v['password']),
            'no_hp' => $v['no_hp'],
            'alamat' => $v['alamat'] ?? null,
            'kode_qr_member' => 'MBR-'.Str::upper(Str::random(10)),
            'status_aktif' => 1,
            'saldo' => 0,
        ]);

        return redirect()->route('member.login')->with('success', 'Akun member berhasil dibuat. Silakan login.');
    }

    public function showLogin()
    {
        return view('member.auth.login');
    }

    public function login(Request $request)
    {
        $request->merge([
            'username_member' => strtolower(str_replace(' ', '', trim((string) $request->input('username_member')))),
        ]);
        $v = $request->validate(['username_member' => 'required', 'password' => 'required']);
        $member = Member::where('username_member', $v['username_member'])->first();
        if (! $member || ! $member->password_member || ! Hash::check($v['password'], $member->password_member)) {
            return back()->withErrors(['username_member' => 'Login member gagal']);
        }
        $request->session()->put('member_id', $member->id_member);

        return redirect()->route('member.dashboard');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('member_id');

        return redirect()->route('member.login');
    }
}
