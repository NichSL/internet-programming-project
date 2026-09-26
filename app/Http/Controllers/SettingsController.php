<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use App\Models\User;

class SettingsController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter context dari URL (default: user)
        $context = $request->query('context', 'user');
        
        return view('settings', compact('context'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
        ];

        if ($user->role === 'admin') {
            $rules['username'] = ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id), 'regex:/admin/i'];
            $messages = ['username.regex' => 'Username Administrator wajib menggunakan kata "admin".'];
        } else {
            $rules['username'] = ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id), 'regex:/^((?!admin).)*$/i'];
            $messages = ['username.regex' => 'Username tidak boleh mengandung kata "admin".'];
        }
        
        $request->validate($rules, $messages);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
        ]);

        return back()->with('success_profile', 'Profil berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.']);
        }

        $user->update([
            'password' => Hash::make($request->new_password)
        ]);

        return back()->with('success_password', 'Password berhasil diperbarui!');
    }

    public function updateStore(Request $request)
    {
        if (Auth::user()->role !== 'seller') {
            abort(403, 'Anda bukan seller.');
        }

        $request->validate([
            'store_name' => 'required|string|max:255',
            'store_address' => 'required|string',
        ]);

        Auth::user()->update([
            'store_name' => $request->store_name,
            'store_address' => $request->store_address,
        ]);

        return back()->with('success_store', 'Informasi toko berhasil diperbarui!');
    }
}