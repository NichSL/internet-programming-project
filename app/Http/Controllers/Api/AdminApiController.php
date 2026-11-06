<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminApiController extends Controller
{
    public function indexUsers(Request $request)
    {
        // strtolower() mengubah "Admin" atau "ADMIN" menjadi "admin" agar selalu cocok
        if (strtolower($request->user()->role) !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Hanya Admin yang diizinkan.'], 403);
        }

        return response()->json(['success' => true, 'data' => User::latest()->get()]);
    }

    public function showUserHistory(Request $request, $id)
    {
        if (strtolower($request->user()->role) !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Hanya Admin yang diizinkan.'], 403);
        }

        $user = User::with('purchases.product')->findOrFail($id);
        return response()->json(['success' => true, 'data' => $user]);
    }

    public function showStoreHistory(Request $request, $id)
    {
        if (strtolower($request->user()->role) !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Akses Ditolak! Hanya Admin yang diizinkan.'], 403);
        }

        $seller = User::where('role', 'seller')->with(['products', 'sales.product', 'sales.buyer'])->findOrFail($id);
        return response()->json(['success' => true, 'data' => $seller]);
    }
}