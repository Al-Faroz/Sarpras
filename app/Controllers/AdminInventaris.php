<?php

namespace App\Controllers;

use App\Models\InventarisModel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class AdminInventaris extends BaseController
{
    private int $perPage = 15;

    // =========================================================================
    // DISPLAY — Daftar inventaris + search + pagination
    // =========================================================================

    public function index()
    {
        $model   = new InventarisModel();
        $keyword = $this->request->getGet('search');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));

        $semua   = $keyword ? $model->searchInventaris($keyword) : $model->getInventaris();
        $total   = count($semua);
        $totalPage = (int)ceil($total / $this->perPage);
        $page    = min($page, max(1, $totalPage ?: 1));
        $inventaris = array_slice($semua, ($page - 1) * $this->perPage, $this->perPage);

        return view('admin/inventaris_display', [
            'inventaris' => $inventaris,
            'keyword'    => $keyword ?? '',
            'page'       => $page,
            'perPage'    => $this->perPage,
            'total'      => $total,
            'totalPage'  => $totalPage,
        ]);
    }

    // =========================================================================
    // TAMBAH
    // =========================================================================

    public function tambah()
    {
        $model = new InventarisModel();
        return view('admin/inventaris_form', [
            'daftar_tahun' => InventarisModel::getDaftarTahun(),
            'sumber_dana'  => InventarisModel::$sumberDana,
            'kode_sakti'   => $model->getKodeSaktiAll(),
        ]);
    }

    public function simpan()
    {
        $model    = new InventarisModel();
        $kodeDana = $this->request->getPost('kode_dana');
        $isDipa   = $kodeDana === 'A';

        $rules = [
            'kode'      => 'required',
            'merk'      => 'required|max_length[255]',
            'kode_dana' => 'required|in_list[A,B,D,E]',
            'tahun'     => 'required',
            'kondisi'   => 'required|in_list[Baik,Rusak Ringan,Rusak Berat]',
            'lokasi'    => 'required|in_list[Statis,Mobile]',
        ];
        if ($isDipa) $rules['no_inv_dipa'] = 'required|max_length[100]';

        if (!$this->validate($rules)) {
            $model2 = new InventarisModel();
            return view('admin/inventaris_form', [
                'daftar_tahun' => InventarisModel::getDaftarTahun(),
                'sumber_dana'  => InventarisModel::$sumberDana,
                'kode_sakti'   => $model2->getKodeSaktiAll(),
                'errors'       => $this->validator->getErrors(),
                'old'          => $this->request->getPost(),
            ]);
        }

        $kode = $this->request->getPost('kode');
        if (!$model->getKodeSaktiByKode($kode)) {
            session()->setFlashdata('error', 'Kode barang tidak valid!');
            return redirect()->to('/admin/inventaris/tambah');
        }

        $inputData = [
            'kode'            => $kode,
            'merk'            => $this->request->getPost('merk'),
            'kode_dana'       => $kodeDana,
            'tahun'           => $this->request->getPost('tahun'),
            'kondisi'         => $this->request->getPost('kondisi'),
            'lokasi'          => $this->request->getPost('lokasi'),
            'id_user_petugas' => session()->get('id_user'),
        ];
        if ($isDipa) $inputData['no_inv'] = $this->request->getPost('no_inv_dipa');

        $result = $model->simpanInventaris($inputData);

        if (!$result['success']) {
            session()->setFlashdata('error', 'Gagal menyimpan: ' . $result['error']);
            return redirect()->to('/admin/inventaris/tambah');
        }

        $this->_logAktifitas('Menambah inventaris: ' . $result['no_inv']);
        session()->setFlashdata('pesan', 'Aset <strong>' . esc($result['no_inv']) . '</strong> berhasil ditambahkan!');
        return redirect()->to('/admin/inventaris');
    }

    // =========================================================================
    // EDIT
    // =========================================================================

    public function edit($id)
    {
        $model  = new InventarisModel();
        $barang = $model->getInventarisById((int)$id);

        if (empty($barang)) {
            session()->setFlashdata('error', 'Data aset tidak ditemukan!');
            return redirect()->to('/admin/inventaris');
        }

        return view('admin/inventaris_edit', [
            'barang'       => $barang,
            'daftar_tahun' => InventarisModel::getDaftarTahun(),
            'sumber_dana'  => InventarisModel::$sumberDana,
        ]);
    }

    public function update($id)
    {
        $model  = new InventarisModel();
        $barang = $model->find((int)$id);

        if (empty($barang)) {
            session()->setFlashdata('error', 'Data aset tidak ditemukan!');
            return redirect()->to('/admin/inventaris');
        }

        $kodeDana = $this->request->getPost('kode_dana');
        $isDipa   = $kodeDana === 'A';

        $rules = [
            'merk'      => 'required|max_length[255]',
            'kode_dana' => 'required|in_list[A,B,D,E]',
            'tahun'     => 'required',
            'kondisi'   => 'required|in_list[Baik,Rusak Ringan,Rusak Berat]',
            'lokasi'    => 'required|in_list[Statis,Mobile]',
        ];
        if ($isDipa) $rules['no_inv_dipa'] = 'required|max_length[100]';

        if (!$this->validate($rules)) {
            session()->setFlashdata('error', implode(' ', $this->validator->getErrors()));
            return redirect()->to('/admin/inventaris/edit/' . $id);
        }

        $inputData = [
            'merk'            => $this->request->getPost('merk'),
            'kode_dana'       => $kodeDana,
            'tahun'           => $this->request->getPost('tahun'),
            'kondisi'         => $this->request->getPost('kondisi'),
            'lokasi'          => $this->request->getPost('lokasi'),
            'id_user_petugas' => session()->get('id_user'),
        ];
        if ($isDipa) $inputData['no_inv'] = $this->request->getPost('no_inv_dipa');

        $result = $model->updateInventaris((int)$id, $inputData);

        if (!$result['success']) {
            session()->setFlashdata('error', $result['error']);
            return redirect()->to('/admin/inventaris/edit/' . $id);
        }

        $this->_logAktifitas('Update inventaris ID#' . $id);
        session()->setFlashdata('pesan', 'Data aset berhasil diperbarui!');
        return redirect()->to('/admin/inventaris');
    }

    // =========================================================================
    // HAPUS
    // =========================================================================

    public function hapus($id)
    {
        $model  = new InventarisModel();
        $barang = $model->find((int)$id);

        if ($barang) {
            $db    = \Config\Database::connect();
            $aktif = $db->table('peminjaman')
                ->where('id_inventaris', $id)
                ->where('status_approval', 'Disetujui')
                ->where('tanggal_kembalikan IS NULL', null, false)
                ->countAllResults();

            if ($aktif > 0) {
                session()->setFlashdata('error', 'Aset tidak dapat dihapus karena sedang dipinjam!');
                return redirect()->to('/admin/inventaris');
            }

            $model->delete((int)$id);
            $this->_logAktifitas('Menghapus inventaris: ' . $barang['no_inv']);
            session()->setFlashdata('pesan', 'Data aset berhasil dihapus!');
        } else {
            session()->setFlashdata('error', 'Data aset tidak ditemukan!');
        }

        return redirect()->to('/admin/inventaris');
    }

    // =========================================================================
    // CETAK QR + BATCH
    // =========================================================================

    public function cetak_qr($id)
    {
        $model = new InventarisModel();
        $aset  = $model->getInventarisById((int)$id);

        if (empty($aset)) {
            session()->setFlashdata('error', 'Data aset tidak ditemukan!');
            return redirect()->to('/admin/inventaris');
        }

        $qrCode = new QrCode(data: base_url('scan/' . $id), size: 300, margin: 5);
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        return view('admin/export/inventaris_qr', [
            'aset'            => $aset,
            'qr_image_base64' => $result->getDataUri(),
            'label_dana'      => InventarisModel::getLabelDana($aset['kode_dana']),
        ]);
    }

    public function scanner()
    {
        return view('admin/scanner');
    }

    // =========================================================================
    // PRINT QR BATCH — dengan pagination
    // =========================================================================

    public function print_qr_batch()
    {
        $model   = new InventarisModel();
        $keyword = $this->request->getGet('search');
        $page    = max(1, (int)($this->request->getGet('page') ?? 1));

        $semua   = $keyword ? $model->searchInventaris($keyword) : $model->getInventaris();
        $total   = count($semua);
        $totalPage = (int)ceil($total / $this->perPage);
        $page    = min($page, max(1, $totalPage ?: 1));
        $inventaris = array_slice($semua, ($page - 1) * $this->perPage, $this->perPage);

        return view('admin/print_qr_batch', [
            'inventaris' => $inventaris,
            'keyword'    => $keyword ?? '',
            'page'       => $page,
            'perPage'    => $this->perPage,
            'total'      => $total,
            'totalPage'  => $totalPage,
        ]);
    }

    public function proses_print_qr()
    {
        $ids = $this->request->getPost('id_inventaris');

        if (empty($ids) || !is_array($ids)) {
            session()->setFlashdata('error', 'Pilih minimal 1 aset untuk dicetak!');
            return redirect()->to('/admin/inventaris/print_qr_batch');
        }

        $ids      = array_slice($ids, 0, 63);
        $model    = new InventarisModel();
        $writer   = new PngWriter();
        $asetList = [];

        foreach ($ids as $id) {
            $aset = $model->getInventarisById((int)$id);
            if (!$aset) continue;

            $qrCode = new QrCode(data: base_url('scan/' . $id), size: 300, margin: 5);
            $result = $writer->write($qrCode);

            $asetList[] = [
                'aset'            => $aset,
                'qr_image_base64' => $result->getDataUri(),
                'label_dana'      => InventarisModel::getLabelDana($aset['kode_dana']),
            ];
        }

        if (empty($asetList)) {
            session()->setFlashdata('error', 'Tidak ada data aset yang valid!');
            return redirect()->to('/admin/inventaris/print_qr_batch');
        }

        $this->_logAktifitas('Cetak QR batch: ' . count($asetList) . ' label');
        return view('admin/export/inventaris_qr_batch', ['aset_list' => $asetList, 'total' => count($asetList)]);
    }

    // =========================================================================
    // IMPORT DIPA
    // =========================================================================

    public function import_dipa()
    {
        return view('admin/inventaris_import_dipa');
    }

    public function proses_import_dipa()
    {
        $file = $this->request->getFile('file_excel');

        if (!$file || !$file->isValid()) {
            session()->setFlashdata('error', 'File tidak valid atau tidak ditemukan.');
            return redirect()->to('/admin/inventaris/import_dipa');
        }

        $ekstensi = strtolower($file->getClientExtension());
        if (!in_array($ekstensi, ['xlsx', 'xls'])) {
            session()->setFlashdata('error', 'Format file harus .xlsx atau .xls');
            return redirect()->to('/admin/inventaris/import_dipa');
        }

        $tmpPath = WRITEPATH . 'uploads/import_dipa_' . time() . '.' . $ekstensi;
        $file->move(WRITEPATH . 'uploads/', basename($tmpPath));

        try {
            $spreadsheet = IOFactory::load($tmpPath);
            $sheet       = $spreadsheet->getActiveSheet();
            $rows        = $sheet->toArray(null, true, true, false);
            @unlink($tmpPath);

            if (count($rows) < 2) {
                session()->setFlashdata('error', 'File Excel kosong atau hanya berisi header.');
                return redirect()->to('/admin/inventaris/import_dipa');
            }

            $header   = array_map(fn($h) => strtolower(trim((string)$h)), $rows[0]);
            $missing  = array_diff(['no_inv', 'kode', 'merk', 'tahun'], $header);

            if (!empty($missing)) {
                session()->setFlashdata('error', 'Kolom wajib tidak ditemukan: ' . implode(', ', $missing));
                return redirect()->to('/admin/inventaris/import_dipa');
            }

            $colMap    = array_flip($header);
            $dataBaris = [];

            for ($i = 1; $i < count($rows); $i++) {
                $r    = $rows[$i];
                $isiRow = array_filter($r, fn($v) => $v !== null && $v !== '');
                if (empty($isiRow)) continue;

                $dataBaris[] = [
                    'no_inv'  => isset($colMap['no_inv'])  ? trim((string)($r[$colMap['no_inv']]  ?? '')) : '',
                    'kode'    => isset($colMap['kode'])    ? trim((string)($r[$colMap['kode']]    ?? '')) : '',
                    'merk'    => isset($colMap['merk'])    ? trim((string)($r[$colMap['merk']]    ?? '')) : '',
                    'tahun'   => isset($colMap['tahun'])   ? trim((string)($r[$colMap['tahun']]   ?? '')) : '',
                    'kondisi' => isset($colMap['kondisi']) ? trim((string)($r[$colMap['kondisi']] ?? '')) : 'Baik',
                    'lokasi'  => isset($colMap['lokasi'])  ? trim((string)($r[$colMap['lokasi']]  ?? '')) : 'Statis',
                ];
            }

            if (empty($dataBaris)) {
                session()->setFlashdata('error', 'Tidak ada baris data valid di file Excel.');
                return redirect()->to('/admin/inventaris/import_dipa');
            }

            $model  = new InventarisModel();
            $hasil  = $model->importDipaBatch($dataBaris, (int)session()->get('id_user'));
            $totalOk   = count(array_filter($hasil, fn($h) => $h['status'] === 'ok'));
            $totalSkip = count(array_filter($hasil, fn($h) => $h['status'] === 'skip'));
            $totalErr  = count(array_filter($hasil, fn($h) => $h['status'] === 'error'));

            $this->_logAktifitas("Import DIPA: {$totalOk} ok, {$totalSkip} skip, {$totalErr} error.");

            session()->setFlashdata('import_hasil', $hasil);
            session()->setFlashdata('import_summary', [
                'ok' => $totalOk, 'skip' => $totalSkip,
                'error' => $totalErr, 'total' => count($dataBaris),
            ]);

            return redirect()->to('/admin/inventaris/hasil_import_dipa');

        } catch (\Exception $e) {
            @unlink($tmpPath);
            session()->setFlashdata('error', 'Gagal membaca file Excel: ' . $e->getMessage());
            return redirect()->to('/admin/inventaris/import_dipa');
        }
    }

    public function hasil_import_dipa()
    {
        $hasil   = session()->getFlashdata('import_hasil');
        $summary = session()->getFlashdata('import_summary');

        if ($hasil === null) return redirect()->to('/admin/inventaris/import_dipa');

        return view('admin/inventaris_hasil_import', ['hasil' => $hasil, 'summary' => $summary]);
    }

    public function template_dipa()
    {
        $model      = new InventarisModel();
        $kodeList   = $model->getKodeSaktiAll();
        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Import DIPA');

        $sheet->mergeCells('A1:F1');
        $sheet->setCellValue('A1', 'TEMPLATE IMPORT INVENTARIS DANA DIPA — MTsN 4 Jombang');
        $sheet->getStyle('A1')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headers = ['no_inv', 'kode', 'merk', 'tahun', 'kondisi', 'lokasi'];
        $labels  = ['No. Inventaris (DIPA)', 'Kode SAKTI', 'Merk / Identitas Unit', 'Tahun Pengadaan', 'Kondisi', 'Lokasi'];

        foreach ($headers as $i => $col) {
            $colLetter = chr(65 + $i);
            $sheet->setCellValue($colLetter . '2', $col);
            $sheet->setCellValue($colLetter . '3', $labels[$i]);
        }

        $sheet->getStyle('A2:F2')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => '1E3A5F']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        foreach (range('A', 'F') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheetRef = $spreadsheet->createSheet();
        $sheetRef->setTitle('Referensi Kode SAKTI');
        $sheetRef->setCellValue('A1', 'Kode');
        $sheetRef->setCellValue('B1', 'Jenis Barang');
        $sheetRef->getStyle('A1:B1')->applyFromArray(['font' => ['bold' => true]]);

        $r = 2;
        foreach ($kodeList as $k) {
            $sheetRef->setCellValue('A' . $r, $k['kode']);
            $sheetRef->setCellValue('B' . $r, $k['jenis_barang']);
            $r++;
        }
        $sheetRef->getColumnDimension('A')->setAutoSize(true);
        $sheetRef->getColumnDimension('B')->setAutoSize(true);

        $spreadsheet->setActiveSheetIndex(0);
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Template_Import_DIPA_MTsN4.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }

    // =========================================================================
    // AJAX
    // =========================================================================

    public function ajax_kode()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);
        $keyword = trim($this->request->getGet('q') ?? '');
        $model   = new InventarisModel();
        if (strlen($keyword) < 2) return $this->response->setJSON([]);

        $hasil = $model->searchKodeSakti($keyword);
        return $this->response->setJSON(array_map(function ($row) {
            return ['id' => $row['kode'], 'text' => $row['kode'] . ' — ' . $row['jenis_barang'], 'jenis_barang' => $row['jenis_barang']];
        }, $hasil));
    }

    public function ajax_preview_no_inv()
    {
        if (!$this->request->isAJAX()) return $this->response->setStatusCode(403);
        $kode  = $this->request->getGet('kode')  ?? '';
        $dana  = $this->request->getGet('dana')  ?? '';
        $tahun = $this->request->getGet('tahun') ?? '';

        if (!$kode || !$dana || !$tahun) return $this->response->setJSON(['no_inv' => '—', 'is_dipa' => false]);

        $model   = new InventarisModel();
        $preview = $model->previewNoInv($kode, $dana, $tahun);
        return $this->response->setJSON(['no_inv' => $preview, 'is_dipa' => $dana === 'A']);
    }

    // =========================================================================
    // PRIVATE HELPER
    // =========================================================================

    private function _logAktifitas(string $aksi): void
    {
        $idUser = session()->get('id_user');
        if (!$idUser) return;
        \Config\Database::connect()->table('log_aktifitas')->insert(['id_user' => $idUser, 'aksi' => $aksi]);
    }
}
