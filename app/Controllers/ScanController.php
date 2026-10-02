<?php

namespace App\Controllers;

use App\Models\InventarisModel;

class ScanController extends BaseController
{
    /**
     * Gerbang utama QR Code.
     * URL: /scan/{id_inventaris}
     *
     * Smart Routing:
     *  - Login (admin/operator) → redirect ke edit inventaris (update kondisi/lokasi)
     *  - Tidak login (publik/guru) → redirect ke form peminjaman dengan auto-fill data aset
     */
    public function index($id)
    {
        $model = new InventarisModel();
        $aset  = $model->getInventarisById((int)$id);

        // QR tidak valid atau aset sudah dihapus
        if (!$aset) {
            session()->setFlashdata('error', 'Label QR Code tidak valid atau aset telah dihapus dari sistem!');
            return redirect()->to('/');
        }

        // -------------------------------------------------------
        // SKENARIO A: Sudah login → Edit Inventaris
        // -------------------------------------------------------
        if (session()->get('id_user')) {
            session()->setFlashdata('pesan',
                'Aset terdeteksi: <strong>' . esc($aset['jenis_barang']) .
                ' — ' . esc($aset['merk']) . '</strong>. Silakan perbarui data.'
            );
            return redirect()->to('/admin/inventaris/edit/' . $id);
        }

        // -------------------------------------------------------
        // SKENARIO B: Belum login → Form Peminjaman (auto-fill)
        // -------------------------------------------------------
        // Simpan id_inventaris di session flashdata
        // LandingPage::form_pinjam() akan membacanya untuk auto-fill
        session()->setFlashdata('auto_fill_id_inventaris', (int)$id);

        return redirect()->to('/landing/form_pinjam');
    }
}
