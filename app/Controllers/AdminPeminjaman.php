<?php

namespace App\Controllers;

use App\Models\PeminjamanModel;

class AdminPeminjaman extends BaseController
{
    private int $perPage = 15;

    public function index()
    {
        $model   = new PeminjamanModel();
        $keyword = $this->request->getGet('search');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));

        $semua   = $keyword ? $model->searchPeminjaman($keyword) : $model->getPeminjaman();
        $total   = count($semua);
        $totalPage = (int)ceil($total / $this->perPage);
        $page    = min($page, max(1, $totalPage ?: 1));

        return view('admin/peminjaman_display', [
            'peminjaman' => array_slice($semua, ($page - 1) * $this->perPage, $this->perPage),
            'keyword'    => $keyword ?? '',
            'page'       => $page,
            'perPage'    => $this->perPage,
            'total'      => $total,
            'totalPage'  => $totalPage,
        ]);
    }

    public function edit($id)
    {
        $model = new PeminjamanModel();
        $data['pinjam'] = $model->getPeminjamanById((int)$id);

        if (empty($data['pinjam'])) {
            session()->setFlashdata('error', 'Data peminjaman tidak ditemukan!');
            return redirect()->to('/admin/peminjaman');
        }

        return view('admin/peminjaman_edit', $data);
    }

    public function update($id)
    {
        $model  = new PeminjamanModel();
        $pinjam = $model->find((int)$id);

        if (empty($pinjam)) {
            session()->setFlashdata('error', 'Data peminjaman tidak ditemukan!');
            return redirect()->to('/admin/peminjaman');
        }

        if (!$this->validate(['status_approval' => 'required|in_list[Pending,Disetujui,Ditolak]'])) {
            session()->setFlashdata('error', 'Status approval tidak valid!');
            return redirect()->to('/admin/peminjaman/edit/' . $id);
        }

        $inputData = [
            'status_approval'    => $this->request->getPost('status_approval'),
            'tanggal_kembalikan' => $this->request->getPost('tanggal_kembalikan') ?: null,
            'id_user_petugas'    => session()->get('id_user'),
        ];

        $model->updatePeminjaman((int)$id, $inputData);
        $this->_logAktifitas('Update peminjaman ID#' . $id . ' → ' . $inputData['status_approval']);

        session()->setFlashdata('pesan', 'Status peminjaman berhasil diperbarui!');
        return redirect()->to('/admin/peminjaman');
    }

    public function hapus($id)
    {
        $model  = new PeminjamanModel();
        $pinjam = $model->find((int)$id);

        if ($pinjam) {
            $model->delete((int)$id);
            $this->_logAktifitas('Hapus peminjaman ID#' . $id);
            session()->setFlashdata('pesan', 'Data peminjaman berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Data tidak ditemukan!');
        }

        return redirect()->to('/admin/peminjaman');
    }

    private function _logAktifitas(string $aksi): void
    {
        $idUser = session()->get('id_user');
        if (!$idUser) return;
        \Config\Database::connect()->table('log_aktifitas')->insert(['id_user' => $idUser, 'aksi' => $aksi]);
    }
}
