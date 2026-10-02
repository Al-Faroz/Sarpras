<?php

namespace App\Controllers;

use App\Models\PemeliharaanModel;

class AdminPemeliharaan extends BaseController
{
    private int $perPage = 15;

    public function index()
    {
        $model   = new PemeliharaanModel();
        $keyword = $this->request->getGet('search');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));

        $semua   = $keyword ? $model->searchPemeliharaan($keyword) : $model->getPemeliharaan();
        $total   = count($semua);
        $totalPage = (int)ceil($total / $this->perPage);
        $page    = min($page, max(1, $totalPage ?: 1));

        return view('admin/pemeliharaan_display', [
            'pemeliharaan' => array_slice($semua, ($page - 1) * $this->perPage, $this->perPage),
            'keyword'      => $keyword ?? '',
            'page'         => $page,
            'perPage'      => $this->perPage,
            'total'        => $total,
            'totalPage'    => $totalPage,
        ]);
    }

    public function tambah()
    {
        return view('admin/pemeliharaan_form');
    }

    public function simpan()
    {
        $model = new PemeliharaanModel();

        if (!$this->validate([
            'nama_pemeliharaan' => 'required|max_length[255]',
            'keterangan'        => 'required',
            'tanggal'           => 'required|valid_date',
            'biaya'             => 'required|numeric',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/admin/pemeliharaan/tambah');
        }

        $model->insert([
            'nama_pemeliharaan' => $this->request->getPost('nama_pemeliharaan'),
            'no_aset'           => $this->request->getPost('no_aset') ?: null,
            'keterangan'        => $this->request->getPost('keterangan'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'biaya'             => $this->request->getPost('biaya'),
            'id_user_petugas'   => session()->get('id_user'),
        ]);

        $this->_logAktifitas('Tambah pemeliharaan: ' . $this->request->getPost('nama_pemeliharaan'));
        session()->setFlashdata('pesan', 'Catatan pemeliharaan berhasil ditambahkan!');
        return redirect()->to('/admin/pemeliharaan');
    }

    public function edit($id)
    {
        $model = new PemeliharaanModel();
        $data['pemeliharaan'] = $model->find((int)$id);

        if (empty($data['pemeliharaan'])) {
            session()->setFlashdata('error', 'Data pemeliharaan tidak ditemukan!');
            return redirect()->to('/admin/pemeliharaan');
        }

        return view('admin/pemeliharaan_edit', $data);
    }

    public function update($id)
    {
        $model = new PemeliharaanModel();

        if (!$model->find((int)$id)) {
            session()->setFlashdata('error', 'Data tidak ditemukan!');
            return redirect()->to('/admin/pemeliharaan');
        }

        if (!$this->validate([
            'nama_pemeliharaan' => 'required|max_length[255]',
            'keterangan'        => 'required',
            'tanggal'           => 'required|valid_date',
            'biaya'             => 'required|numeric',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/admin/pemeliharaan/edit/' . $id);
        }

        $model->update((int)$id, [
            'nama_pemeliharaan' => $this->request->getPost('nama_pemeliharaan'),
            'no_aset'           => $this->request->getPost('no_aset') ?: null,
            'keterangan'        => $this->request->getPost('keterangan'),
            'tanggal'           => $this->request->getPost('tanggal'),
            'biaya'             => $this->request->getPost('biaya'),
            'id_user_petugas'   => session()->get('id_user'),
        ]);

        $this->_logAktifitas('Update pemeliharaan ID#' . $id);
        session()->setFlashdata('pesan', 'Catatan pemeliharaan berhasil diperbarui!');
        return redirect()->to('/admin/pemeliharaan');
    }

    public function hapus($id)
    {
        $model = new PemeliharaanModel();
        if ($model->find((int)$id)) {
            $model->delete((int)$id);
            $this->_logAktifitas('Hapus pemeliharaan ID#' . $id);
            session()->setFlashdata('pesan', 'Catatan pemeliharaan berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Data tidak ditemukan!');
        }
        return redirect()->to('/admin/pemeliharaan');
    }

    private function _logAktifitas(string $aksi): void
    {
        $idUser = session()->get('id_user');
        if (!$idUser) return;
        \Config\Database::connect()->table('log_aktifitas')->insert(['id_user' => $idUser, 'aksi' => $aksi]);
    }
}
