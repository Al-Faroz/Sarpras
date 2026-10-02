<?php

namespace App\Controllers;

use App\Models\LaporanModel;

class AdminLaporan extends BaseController
{
    private int $perPage = 15;

    public function index()
    {
        $model   = new LaporanModel();
        $keyword = $this->request->getGet('search');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));

        $semua   = $keyword ? $model->searchLaporan($keyword) : $model->getLaporan();
        $total   = count($semua);
        $totalPage = (int)ceil($total / $this->perPage);
        $page    = min($page, max(1, $totalPage ?: 1));

        return view('admin/laporan_display', [
            'laporan'   => array_slice($semua, ($page - 1) * $this->perPage, $this->perPage),
            'keyword'   => $keyword ?? '',
            'page'      => $page,
            'perPage'   => $this->perPage,
            'total'     => $total,
            'totalPage' => $totalPage,
        ]);
    }

    public function edit($id)
    {
        $model = new LaporanModel();
        $data['laporan'] = $model->getLaporanById((int)$id);

        if (empty($data['laporan'])) {
            session()->setFlashdata('error', 'Data laporan tidak ditemukan!');
            return redirect()->to('/admin/laporan');
        }

        return view('admin/laporan_edit', $data);
    }

    public function update($id)
    {
        $model       = new LaporanModel();
        $laporanLama = $model->find((int)$id);

        if (empty($laporanLama)) {
            session()->setFlashdata('error', 'Data laporan tidak ditemukan!');
            return redirect()->to('/admin/laporan');
        }

        if (!$this->validate([
            'tindakan' => 'required',
            'status'   => 'required|in_list[Masuk,Proses,Selesai]',
        ])) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/admin/laporan/edit/' . $id);
        }

        $updateData = [
            'tindakan'        => $this->request->getPost('tindakan'),
            'status'          => $this->request->getPost('status'),
            'id_user_petugas' => session()->get('id_user'),
        ];

        $foto = $this->request->getFile('foto_bukti_akhir');
        if ($foto && $foto->isValid() && !$foto->hasMoved()) {
            $namaFoto = $foto->getRandomName();
            $foto->move(FCPATH . 'uploads/laporan/', $namaFoto);
            $updateData['foto_bukti_akhir'] = $namaFoto;

            if (!empty($laporanLama['foto_bukti_akhir']) &&
                file_exists(FCPATH . 'uploads/laporan/' . $laporanLama['foto_bukti_akhir'])) {
                unlink(FCPATH . 'uploads/laporan/' . $laporanLama['foto_bukti_akhir']);
            }
        }

        $model->update((int)$id, $updateData);
        $this->_logAktifitas('Update laporan ID#' . $id . ' → ' . $updateData['status']);

        session()->setFlashdata('pesan', 'Tindak lanjut laporan berhasil disimpan!');
        return redirect()->to('/admin/laporan');
    }

    public function hapus($id)
    {
        $model   = new LaporanModel();
        $laporan = $model->find((int)$id);

        if ($laporan) {
            // Hapus foto jika ada
            foreach (['foto_bukti_awal', 'foto_bukti_akhir'] as $field) {
                if (!empty($laporan[$field]) && file_exists(FCPATH . 'uploads/laporan/' . $laporan[$field])) {
                    unlink(FCPATH . 'uploads/laporan/' . $laporan[$field]);
                }
            }
            $model->delete((int)$id);
            $this->_logAktifitas('Hapus laporan ID#' . $id);
            session()->setFlashdata('pesan', 'Data laporan berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Data tidak ditemukan!');
        }

        return redirect()->to('/admin/laporan');
    }

    private function _logAktifitas(string $aksi): void
    {
        $idUser = session()->get('id_user');
        if (!$idUser) return;
        \Config\Database::connect()->table('log_aktifitas')->insert(['id_user' => $idUser, 'aksi' => $aksi]);
    }
}
