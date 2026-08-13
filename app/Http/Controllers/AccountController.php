<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    protected $firebaseService;
    protected $collection = 'users';

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Menampilkan daftar akun pengguna.
     */
    public function index()
    {
        $accounts = $this->firebaseService->getCollection($this->collection);
        return view('superadmin.administration.accounts', compact('accounts'));
    }

    /**
     * Menyimpan data akun baru (dengan hashing).
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'role' => 'required|in:superadmin,petugas,user',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            // Cek apakah email sudah dipakai (Loop secara manual karena kita pakai REST Firebase kustom)
            $existing = $this->firebaseService->getCollection($this->collection);
            foreach ($existing as $user) {
                if (($user['email'] ?? '') === $request->email) {
                    return redirect()->back()->with('error', 'Email ini sudah terdaftar pada akun lain.');
                }
            }

            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password), // WAJIB DI HASH
                'role' => $request->role,
                'phone' => $request->phone ?? '-',
                'created_at' => time(),
                'updated_at' => time(),
            ];

            // Generate custom ID (contoh: user_1234abcd)
            $customId = 'user_' . Str::random(8);

            $this->firebaseService->saveDocument($this->collection, $data, $customId);
            
            return redirect()->back()->with('success', 'Akun berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan akun: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui profil akun (tanpa password).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'role' => 'required|in:superadmin,petugas,user',
            'phone' => 'nullable|string|max:20',
        ]);

        try {
            $doc = $this->firebaseService->getDocument($this->collection, $id);
            if (!$doc) {
                return redirect()->back()->with('error', 'Data akun tidak ditemukan.');
            }

            // Cek ketersediaan email jika email diubah
            if ($doc['email'] !== $request->email) {
                $existing = $this->firebaseService->getCollection($this->collection);
                foreach ($existing as $user) {
                    if (($user['email'] ?? '') === $request->email && $user['id'] !== $id) {
                        return redirect()->back()->with('error', 'Email ini sudah terdaftar pada akun lain.');
                    }
                }
            }

            $doc['name'] = $request->name;
            $doc['email'] = $request->email;
            $doc['role'] = $request->role;
            $doc['phone'] = $request->phone ?? '-';
            $doc['updated_at'] = time();

            $this->firebaseService->saveDocument($this->collection, $doc, $id);
            
            return redirect()->back()->with('success', 'Profil akun berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui akun: ' . $e->getMessage());
        }
    }

    /**
     * Mereset password akun secara manual dari Admin.
     */
    public function resetPassword(Request $request, $id)
    {
        $request->validate([
            'password' => 'required|string|min:6'
        ]);

        try {
            $doc = $this->firebaseService->getDocument($this->collection, $id);
            if (!$doc) {
                return redirect()->back()->with('error', 'Data akun tidak ditemukan.');
            }

            $doc['password'] = Hash::make($request->password);
            $doc['updated_at'] = time();

            $this->firebaseService->saveDocument($this->collection, $doc, $id);
            
            return redirect()->back()->with('success', 'Kata sandi akun berhasil direset!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal mereset kata sandi: ' . $e->getMessage());
        }
    }

    /**
     * Mencabut akses / Menghapus akun.
     */
    public function destroy($id)
    {
        try {
            $this->firebaseService->deleteDocument($this->collection, $id);
            return redirect()->back()->with('success', 'Akses akun berhasil dicabut dan dihapus secara permanen!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus akun: ' . $e->getMessage());
        }
    }
}
