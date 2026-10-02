<?php

namespace App\Controllers;

use App\Models\InventarisModel;
use App\Models\PeminjamanModel;
use App\Models\LaporanModel;
use App\Models\PemeliharaanModel;

class AdminDashboard extends BaseController
{
    public function index()
    {
        $inventarisModel   = new InventarisModel();
        $peminjamanModel   = new PeminjamanModel();
        $laporanModel      = new LaporanModel();
        $pemeliharaanModel = new PemeliharaanModel();
        $role              = session()->get('role');

        $data = [
            'role'               => $role,

            // Statistik inventaris
            'total_aset'         => $inventarisModel->getTotalAset(),
            'kondisi_aset'       => $inventarisModel->getStatistikKondisi(),

            // Statistik peminjaman
            'total_pinjam_aktif' => $peminjamanModel->getTotalAktif(),
            'status_pinjam'      => $peminjamanModel->getStatistikStatus(),

            // Statistik laporan
            'status_laporan'     => $laporanModel->getStatistikStatus(),

            // Statistik pemeliharaan — tetap diambil untuk admin
            // operator tidak menampilkan ini di view
            'biaya_bulan_ini'    => $role === 'admin' ? $pemeliharaanModel->getTotalBiayaBulanIni()  : 0,
            'total_pemeliharaan' => $role === 'admin' ? $pemeliharaanModel->getTotalPemeliharaan()   : 0,
        ];

        return view('admin/dashboard', $data);
    }

    // =========================================================================
    // AJAX — Notifikasi bell (Peminjaman Pending + Laporan Masuk)
    // =========================================================================
    public function ajax_notifikasi()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $db = \Config\Database::connect();

        $pinjamPending = $db->table('peminjaman p')
            ->join('inventaris i',        'i.id_inventaris = p.id_inventaris')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->select('p.id_peminjaman, p.nama_peminjam, k.jenis_barang, i.merk, p.created_at')
            ->where('p.status_approval', 'Pending')
            ->orderBy('p.id_peminjaman', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        $laporanMasuk = $db->table('laporan')
            ->select('id_laporan, nama_pelapor, nama_barang, created_at')
            ->where('status', 'Masuk')
            ->orderBy('id_laporan', 'DESC')
            ->limit(10)
            ->get()->getResultArray();

        return $this->response->setJSON([
            'pinjam_pending' => count($pinjamPending),
            'laporan_masuk'  => count($laporanMasuk),
            'total'          => count($pinjamPending) + count($laporanMasuk),
            'items_pinjam'   => $pinjamPending,
            'items_laporan'  => $laporanMasuk,
        ]);
    }
}