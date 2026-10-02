<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;

class LandingPage extends BaseController
{
    // =========================================================================
    // BERANDA
    // =========================================================================

    public function index()
    {
        return view('landing/beranda');
    }

    // =========================================================================
    // FORM PINJAM — Auto-fill dari QR Scan
    // =========================================================================

    /**
     * Menampilkan form peminjaman.
     *
     * Jika ada flashdata 'auto_fill_id_inventaris' (dari ScanController),
     * data aset diambil dari database dan dikirim ke view untuk auto-fill.
     * User hanya perlu mengisi: nama_peminjam, keterangan, tanggal.
     */
    public function form_pinjam()
    {
        $data = [
            'aset'    => null,   // data aset untuk auto-fill (null = form kosong)
            'error'   => null,
        ];

        // Cek apakah ada id_inventaris dari hasil scan QR
        $idInventaris = session()->getFlashdata('auto_fill_id_inventaris');

        if ($idInventaris) {
            $model = new PeminjamanModel();
            $aset  = $model->getDetailAsetUntukPinjam((int)$idInventaris);

            if ($aset) {
                // Kondisi harus Baik agar bisa dipinjam
                if ($aset['kondisi'] !== 'Baik') {
                    $data['error'] = 'Aset ini tidak dapat dipinjam karena kondisinya: <strong>' .
                        esc($aset['kondisi']) . '</strong>. Silakan hubungi petugas Sarpras.';
                } else {
                    $data['aset'] = $aset;
                }
            } else {
                $data['error'] = 'Data aset tidak ditemukan. QR Code mungkin sudah tidak valid.';
            }
        }

        return view('landing/form_pinjam', $data);
    }

    /**
     * Proses kirim permohonan peminjaman.
     * id_inventaris wajib ada (dari hidden input yang diisi saat auto-fill).
     */
    public function kirim_pinjam()
    {
        // Validasi input
        if (!$this->validate([
            'id_inventaris' => 'required|integer',
            'nama_peminjam' => 'required|max_length[150]',
            'keterangan'    => 'required',
            'tanggal'       => 'required|valid_date',
        ])) {
            // Kembalikan ke form dengan error
            $model = new PeminjamanModel();
            $aset  = $model->getDetailAsetUntukPinjam(
                (int)$this->request->getPost('id_inventaris')
            );

            return view('landing/form_pinjam', [
                'aset'   => $aset,
                'errors' => $this->validator->getErrors(),
                'old'    => $this->request->getPost(),
                'error'  => null,
            ]);
        }

        // Verifikasi aset masih valid & kondisi Baik
        $idInventaris = (int)$this->request->getPost('id_inventaris');
        $model        = new PeminjamanModel();
        $aset         = $model->getDetailAsetUntukPinjam($idInventaris);

        if (!$aset) {
            session()->setFlashdata('error', 'Data aset tidak valid. Silakan scan ulang QR Code.');
            return redirect()->to('/landing/form_pinjam');
        }

        if ($aset['kondisi'] !== 'Baik') {
            session()->setFlashdata('error',
                'Aset tidak dapat dipinjam. Kondisi saat ini: ' . $aset['kondisi']
            );
            return redirect()->to('/landing/form_pinjam');
        }

        // Simpan permohonan peminjaman
        $inputData = [
            'id_inventaris' => $idInventaris,
            'nama_peminjam' => $this->request->getPost('nama_peminjam'),
            'keterangan'    => $this->request->getPost('keterangan'),
            'tanggal'       => $this->request->getPost('tanggal'),
        ];

        $model->simpanPeminjaman($inputData);

        session()->setFlashdata('pesan',
            'Permohonan peminjaman <strong>' .
            esc($aset['jenis_barang']) . ' — ' . esc($aset['merk']) .
            '</strong> berhasil dikirim! Silakan tunggu konfirmasi dari petugas Sarpras.'
        );
        return redirect()->to('/');
    }

    // =========================================================================
    // FORM LAPOR KERUSAKAN — Tidak berubah (berdiri sendiri)
    // =========================================================================

    public function form_lapor()
    {
        $db = \Config\Database::connect();
        $data['kategori_jenis'] = $db->table('master_kategori_jenis')->get()->getResultArray();

        return view('landing/form_lapor', $data);
    }

    public function kirim_lapor()
    {
        $db = \Config\Database::connect();

        if (!$this->validate([
            'nama_pelapor'         => 'required|max_length[150]',
            'unit_kerja'           => 'required|max_length[100]',
            'id_kategori_jenis'    => 'required|integer',
            'nama_barang'          => 'required|max_length[255]',
            'keterangan_kerusakan' => 'required',
        ])) {
            $data['kategori_jenis'] = $db->table('master_kategori_jenis')->get()->getResultArray();
            $data['errors']         = $this->validator->getErrors();
            $data['old']            = $this->request->getPost();
            return view('landing/form_lapor', $data);
        }

        $insertData = [
            'nama_pelapor'         => $this->request->getPost('nama_pelapor'),
            'unit_kerja'           => $this->request->getPost('unit_kerja'),
            'id_kategori_jenis'    => $this->request->getPost('id_kategori_jenis'),
            'nama_barang'          => $this->request->getPost('nama_barang'),
            'keterangan_kerusakan' => $this->request->getPost('keterangan_kerusakan'),
            'status'               => 'Masuk',
            'tindakan'             => null,
            'foto_bukti_akhir'     => null,
            'id_user_petugas'      => null,
        ];

        // Upload foto bukti awal
        $foto = $this->request->getFile('foto_bukti_awal');

        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            // Validasi tipe file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($foto->getMimeType(), $allowedTypes)) {
                session()->setFlashdata('error', 'Format foto tidak didukung. Gunakan JPG atau PNG.');
                return redirect()->to('/landing/form_lapor');
            }

            $namaFoto = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/laporan/', $namaFoto);
            $insertData['foto_bukti_awal'] = $namaFoto;
        } else {
            $insertData['foto_bukti_awal'] = '';
        }

        $db->table('laporan')->insert($insertData);

        session()->setFlashdata('pesan',
            'Laporan kerusakan berhasil dikirim! Petugas Sarpras akan segera menindaklanjutinya.'
        );
        return redirect()->to('/');
    }

    // =========================================================================
    // SCANNER — Halaman kamera QR publik
    // =========================================================================

    public function scanner()
    {
        return view('landing/scanner');
    }
}
