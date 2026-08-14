<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirebaseService;

class FishermanController extends Controller
{
    protected $firebaseService;
    protected $collection = 'fishermen';

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    /**
     * Menampilkan daftar nelayan.
     */
    public function index()
    {
        $fishermen = $this->firebaseService->getCollection($this->collection);
        $fleet = $this->firebaseService->getCollection('fleet');
        $users = $this->firebaseService->getCollection('users');
        
        $totalFleet = count($fleet);
        $totalPetugas = collect($users)->filter(function($u) {
            return ($u['role'] ?? '') === 'petugas';
        })->count();

        return view('superadmin.administration.fishermen', compact('fishermen', 'totalFleet', 'totalPetugas'));
    }

    /**
     * Menyimpan data nelayan baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:20',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'boat_name' => 'nullable|string|max:255',
            'node_id' => 'nullable|string|max:255',
        ]);

        try {
            $data = $request->only(['name', 'nik', 'phone', 'address', 'boat_name', 'node_id']);
            $data['created_at'] = time();

            $this->firebaseService->saveDocument($this->collection, $data);
            
            return redirect()->back()->with('success', 'Data Nelayan berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan data: ' . $e->getMessage());
        }
    }

    /**
     * Memperbarui data nelayan.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'nik' => 'required|string|max:20',
            'phone' => 'nullable|string|max:15',
            'address' => 'nullable|string',
            'boat_name' => 'nullable|string|max:255',
            'node_id' => 'nullable|string|max:255',
        ]);

        try {
            // Ambil dokumen lama
            $doc = $this->firebaseService->getDocument($this->collection, $id);
            if (!$doc) {
                return redirect()->back()->with('error', 'Data nelayan tidak ditemukan.');
            }

            // Perbarui nilainya
            $doc['name'] = $request->name;
            $doc['nik'] = $request->nik;
            $doc['phone'] = $request->phone;
            $doc['address'] = $request->address;
            $doc['boat_name'] = $request->boat_name;
            $doc['node_id'] = $request->node_id;
            $doc['updated_at'] = time();

            $this->firebaseService->saveDocument($this->collection, $doc, $id);
            
            return redirect()->back()->with('success', 'Data Nelayan berhasil diperbarui!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui data: ' . $e->getMessage());
        }
    }

    /**
     * Menghapus data nelayan.
     */
    public function destroy($id)
    {
        try {
            $this->firebaseService->deleteDocument($this->collection, $id);
            return redirect()->back()->with('success', 'Data Nelayan berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
