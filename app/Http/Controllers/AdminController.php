<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemesanan;
use App\Models\Config;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    private $adminPassword = '@isc2026';
    private $adminEmail = 'armawanome47@gmail.com';

    public function handle(Request $request)
    {
        $data = json_decode($request->getContent(), true);
        $action = $data['action'] ?? '';

        if ($action === 'login') {
            if (($data['password'] ?? '') === $this->adminPassword) {
                $otp = rand(100000, 999999);
                Config::updateOrCreate(['key' => 'admin_otp'], ['value' => $otp]);
                Config::updateOrCreate(['key' => 'admin_otp_expiry'], ['value' => now()->addMinutes(5)->timestamp]);
                
                try {
                    Mail::raw("Kode OTP Anda adalah: $otp\n\nBerlaku selama 5 menit.", function ($message) {
                        $message->to($this->adminEmail)
                                ->subject("Kode OTP Login Admin PDH ISC");
                    });
                    return response()->json(['success' => true, 'message' => 'OTP terkirim']);
                } catch (\Exception $e) {
                    return response()->json(['success' => false, 'message' => 'Gagal mengirim email: ' . $e->getMessage()]);
                }
            }
            return response()->json(['success' => false, 'message' => 'Password salah']);
        }

        if ($action === 'verify_otp') {
            if (($data['password'] ?? '') === $this->adminPassword) {
                $storedOtp = Config::where('key', 'admin_otp')->value('value');
                $expiry = Config::where('key', 'admin_otp_expiry')->value('value');

                if (now()->timestamp > $expiry) {
                    return response()->json(['success' => false, 'message' => 'OTP kadaluarsa']);
                }

                if (($data['otp'] ?? '') == $storedOtp) {
                    Config::whereIn('key', ['admin_otp', 'admin_otp_expiry'])->delete();
                    return response()->json(['success' => true, 'message' => 'Login berhasil']);
                }
                return response()->json(['success' => false, 'message' => 'OTP salah']);
            }
            return response()->json(['success' => false, 'message' => 'Password salah']);
        }

        if (($data['password'] ?? '') !== $this->adminPassword) {
            return response()->json(['success' => false, 'message' => 'Tidak memiliki izin']);
        }

        if ($action === 'update_status') {
            $pemesanan = Pemesanan::find($data['rowId']);
            if (!$pemesanan) return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan']);

            if ($data['type'] === 'bayar') {
                $pemesanan->update(['status_bayar' => $data['value']]);
            } else if ($data['type'] === 'proses') {
                $pemesanan->update(['status_proses' => $data['value']]);
            } else if ($data['type'] === 'validasi') {
                $pemesanan->update(['validasi' => $data['value']]);
            }
            return response()->json(['success' => true, 'message' => 'Status berhasil diperbarui']);
        }

        if ($action === 'update_config') {
            $config = $data['config'] ?? [];
            if (isset($config['isOpen'])) Config::updateOrCreate(['key' => 'isOpen'], ['value' => $config['isOpen']]);
            if (isset($config['openTime'])) Config::updateOrCreate(['key' => 'openTime'], ['value' => $config['openTime']]);
            if (isset($config['closeTime'])) Config::updateOrCreate(['key' => 'closeTime'], ['value' => $config['closeTime']]);
            return response()->json(['success' => true, 'message' => 'Pengaturan jadwal berhasil disimpan!']);
        }

        if ($action === 'delete_order') {
            Pemesanan::where('id', $data['rowId'])->delete();
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil dihapus']);
        }

        if ($action === 'edit_order') {
            $pemesanan = Pemesanan::find($data['rowId']);
            if ($pemesanan) {
                $editData = $data['editData'] ?? [];
                $pemesanan->update([
                    'nama' => $editData['nama'] ?? $pemesanan->nama,
                    'ukuran' => $editData['ukuran'] ?? $pemesanan->ukuran,
                    'jabatan' => $editData['jabatan'] ?? $pemesanan->jabatan,
                    'divisi' => $editData['divisi'] ?? $pemesanan->divisi,
                    'jenis_pdh' => $editData['jenisPdh'] ?? $pemesanan->jenis_pdh,
                    'volume' => $editData['volume'] ?? $pemesanan->volume,
                    'no_wa' => $editData['noWa'] ?? $pemesanan->no_wa
                ]);
            }
            return response()->json(['success' => true, 'message' => 'Data berhasil diupdate']);
        }

        return response()->json(['success' => false, 'message' => 'Aksi tidak valid']);
    }
}
