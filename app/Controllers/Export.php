<?php

namespace App\Controllers;

use Dompdf\Dompdf;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use App\Models\InventarisModel;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class Export extends BaseController
{
    // =========================================================================
    // 1. INVENTARIS
    // =========================================================================

    public function inventaris_pdf()
    {
        $model           = new \App\Models\InventarisModel();
        $data['inventaris'] = $model->getInventaris();

        $dompdf = new Dompdf();
        $html   = view('admin/export/inventaris_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Data_Inventaris_' . date('Ymd') . '.pdf', ['Attachment' => true]);
    }

    public function inventaris_excel()
    {
        $model = new \App\Models\InventarisModel();
        $data  = $model->getInventaris();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Inventaris');

        // --- Header utama ---
        $sheet->mergeCells('A1:J1');
        $sheet->setCellValue('A1', 'DATA INVENTARIS SARANA PRASARANA - MTsN 4 JOMBANG');
        $sheet->mergeCells('A2:J2');
        $sheet->setCellValue('A2', 'Per Tanggal: ' . date('d F Y'));

        // --- Header kolom ---
        $headers = ['No', 'No. Inventaris', 'Jenis Barang', 'Merk', 'Sumber Dana',
                    'Tahun', 'No. Urut', 'Kondisi', 'Lokasi', 'Petugas'];

        foreach ($headers as $i => $header) {
            $col = chr(65 + $i); // A, B, C, ...
            $sheet->setCellValue($col . '3', $header);
        }

        // --- Isi data ---
        $row = 4;
        foreach ($data as $no => $item) {
            $sheet->setCellValue('A' . $row, $no + 1);
            $sheet->setCellValue('B' . $row, $item['no_inv']);
            $sheet->setCellValue('C' . $row, $item['jenis_barang']);
            $sheet->setCellValue('D' . $row, $item['merk']);
            $sheet->setCellValue('E' . $row, InventarisModel::getLabelDana($item['kode_dana']));
            $sheet->setCellValue('F' . $row, $item['tahun']);
            $sheet->setCellValue('G' . $row, str_pad($item['no_urut'], 4, '0', STR_PAD_LEFT));
            $sheet->setCellValue('H' . $row, $item['kondisi']);
            $sheet->setCellValue('I' . $row, $item['lokasi']);
            $sheet->setCellValue('J' . $row, $item['nama_petugas']);
            $row++;
        }

        // --- Styling ---
        $this->_styleHeaderExcel($sheet, 'A1:J2', 'A3:J3', $row - 1);

        // Auto width
        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->_downloadExcel($spreadsheet, 'Data_Inventaris_' . date('Ymd'));
    }

    // =========================================================================
    // 2. PEMINJAMAN
    // =========================================================================

    public function peminjaman_pdf()
    {
        $model              = new \App\Models\PeminjamanModel();
        $data['peminjaman'] = $model->getPeminjaman();

        $dompdf = new Dompdf();
        $html   = view('admin/export/peminjaman_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Data_Peminjaman_' . date('Ymd') . '.pdf', ['Attachment' => true]);
    }

    public function peminjaman_excel()
    {
        $model = new \App\Models\PeminjamanModel();
        $data  = $model->getPeminjaman();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Peminjaman');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'DATA PEMINJAMAN ASET - MTsN 4 JOMBANG');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Per Tanggal: ' . date('d F Y'));

        $headers = ['No', 'No. Inventaris', 'Jenis Barang', 'Merk',
                    'Nama Peminjam', 'Keterangan', 'Tgl Pinjam', 'Status', 'Tgl Kembali', 'Bukti Pengembalian'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . '3', $header);
        }

        $tinggiBarisGambar = 80; // pixel
        $row = 4;
        foreach ($data as $no => $item) {
            $sheet->setCellValue('A' . $row, $no + 1);
            $sheet->setCellValue('B' . $row, $item['no_inv']);
            $sheet->setCellValue('C' . $row, $item['jenis_barang']);
            $sheet->setCellValue('D' . $row, $item['merk']);
            $sheet->setCellValue('E' . $row, $item['nama_peminjam']);
            $sheet->setCellValue('F' . $row, $item['keterangan']);
            $sheet->setCellValue('G' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('H' . $row, $item['status_approval']);
            $sheet->setCellValue('I' . $row, $item['tanggal_kembalikan']
                ? date('d/m/Y', strtotime($item['tanggal_kembalikan']))
                : '—');

            // Embed gambar bukti pengembalian
            $fotoPath = FCPATH . 'uploads/peminjaman/' . ($item['foto_bukti_pengembalian'] ?? '');
            if (!empty($item['foto_bukti_pengembalian']) && file_exists($fotoPath)) {
                $drawing = new Drawing();
                $drawing->setPath($fotoPath);
                $drawing->setHeight($tinggiBarisGambar);
                $drawing->setCoordinates('J' . $row);
                $drawing->setOffsetX(2);
                $drawing->setOffsetY(2);
                $drawing->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight($tinggiBarisGambar * 0.75);
            } else {
                $sheet->setCellValue('J' . $row, '—');
            }

            $row++;
        }

        $this->_styleHeaderExcel($sheet, 'A1:J2', 'A3:J3', $row - 1);
        $sheet->getColumnDimension('J')->setWidth(18);

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->_downloadExcel($spreadsheet, 'Data_Peminjaman_' . date('Ymd'));
    }

    // =========================================================================
    // 3. PEMELIHARAAN
    // =========================================================================

    public function pemeliharaan_pdf()
    {
        $model                 = new \App\Models\PemeliharaanModel();
        $data['pemeliharaan']  = $model->getPemeliharaan();

        $dompdf = new Dompdf();
        $html   = view('admin/export/pemeliharaan_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Data_Pemeliharaan_' . date('Ymd') . '.pdf', ['Attachment' => true]);
    }

    public function pemeliharaan_excel()
    {
        $model = new \App\Models\PemeliharaanModel();
        $data  = $model->getPemeliharaan();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Pemeliharaan');

        $sheet->mergeCells('A1:G1');
        $sheet->setCellValue('A1', 'DATA PEMELIHARAAN ASET - MTsN 4 JOMBANG');
        $sheet->mergeCells('A2:G2');
        $sheet->setCellValue('A2', 'Per Tanggal: ' . date('d F Y'));

        $headers = ['No', 'Nama Pemeliharaan', 'No. Aset', 'Keterangan', 'Tanggal', 'Biaya (Rp)', 'Petugas'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . '3', $header);
        }

        $row = 4;
        foreach ($data as $no => $item) {
            $sheet->setCellValue('A' . $row, $no + 1);
            $sheet->setCellValue('B' . $row, $item['nama_pemeliharaan']);
            $sheet->setCellValue('C' . $row, $item['no_aset'] ?: '—');
            $sheet->setCellValue('D' . $row, $item['keterangan']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($item['tanggal'])));
            $sheet->setCellValue('F' . $row, number_format((int)$item['biaya'], 0, ',', '.'));
            $sheet->setCellValue('G' . $row, $item['nama_petugas']);
            $row++;
        }

        $this->_styleHeaderExcel($sheet, 'A1:G2', 'A3:G3', $row - 1);

        foreach (range('A', 'G') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->_downloadExcel($spreadsheet, 'Data_Pemeliharaan_' . date('Ymd'));
    }

    // =========================================================================
    // 4. LAPORAN KERUSAKAN
    // =========================================================================

    public function laporan_pdf()
    {
        $model           = new \App\Models\LaporanModel();
        $data['laporan'] = $model->getLaporan();

        $dompdf = new Dompdf();
        $html   = view('admin/export/laporan_pdf', $data);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();
        $dompdf->stream('Data_Laporan_Kerusakan_' . date('Ymd') . '.pdf', ['Attachment' => true]);
    }

    public function laporan_excel()
    {
        $model = new \App\Models\LaporanModel();
        $data  = $model->getLaporan();

        $spreadsheet = new Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Laporan Kerusakan');

        $sheet->mergeCells('A1:I1');
        $sheet->setCellValue('A1', 'DATA LAPORAN KERUSAKAN - MTsN 4 JOMBANG');
        $sheet->mergeCells('A2:I2');
        $sheet->setCellValue('A2', 'Per Tanggal: ' . date('d F Y'));

        $headers = ['No', 'Tgl Laporan', 'Nama Pelapor', 'Unit Kerja', 'Nama Barang',
                    'Kategori', 'Detail Kerusakan', 'Status', 'Tindakan', 'Petugas',
                    'Foto Bukti Awal', 'Foto Bukti Akhir'];

        foreach ($headers as $i => $header) {
            $sheet->setCellValue(chr(65 + $i) . '3', $header);
        }

        $tinggiBarisGambar = 80;
        $row = 4;
        foreach ($data as $no => $item) {
            $sheet->setCellValue('A' . $row, $no + 1);
            $sheet->setCellValue('B' . $row, $item['created_at'] ? date('d/m/Y', strtotime($item['created_at'])) : '—');
            $sheet->setCellValue('C' . $row, $item['nama_pelapor']);
            $sheet->setCellValue('D' . $row, $item['unit_kerja']);
            $sheet->setCellValue('E' . $row, $item['nama_barang']);
            $sheet->setCellValue('F' . $row, $item['nilai_kategori']);
            $sheet->setCellValue('G' . $row, $item['keterangan_kerusakan']);
            $sheet->setCellValue('H' . $row, $item['status']);
            $sheet->setCellValue('I' . $row, $item['tindakan'] ?: '—');
            $sheet->setCellValue('J' . $row, $item['nama_petugas'] ?: '—');

            // Embed gambar bukti awal
            $pathAwal = FCPATH . 'uploads/laporan/' . ($item['foto_bukti_awal'] ?? '');
            if (!empty($item['foto_bukti_awal']) && file_exists($pathAwal)) {
                $dwg1 = new Drawing();
                $dwg1->setPath($pathAwal);
                $dwg1->setHeight($tinggiBarisGambar);
                $dwg1->setCoordinates('K' . $row);
                $dwg1->setOffsetX(2)->setOffsetY(2);
                $dwg1->setWorksheet($sheet);
            } else {
                $sheet->setCellValue('K' . $row, '—');
            }

            // Embed gambar bukti akhir
            $pathAkhir = FCPATH . 'uploads/laporan/' . ($item['foto_bukti_akhir'] ?? '');
            if (!empty($item['foto_bukti_akhir']) && file_exists($pathAkhir)) {
                $dwg2 = new Drawing();
                $dwg2->setPath($pathAkhir);
                $dwg2->setHeight($tinggiBarisGambar);
                $dwg2->setCoordinates('L' . $row);
                $dwg2->setOffsetX(2)->setOffsetY(2);
                $dwg2->setWorksheet($sheet);
                $sheet->getRowDimension($row)->setRowHeight($tinggiBarisGambar * 0.75);
            } else {
                $sheet->setCellValue('L' . $row, '—');
            }

            $row++;
        }

        $this->_styleHeaderExcel($sheet, 'A1:L2', 'A3:L3', $row - 1);
        $sheet->getColumnDimension('K')->setWidth(18);
        $sheet->getColumnDimension('L')->setWidth(18);

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $this->_downloadExcel($spreadsheet, 'Data_Laporan_Kerusakan_' . date('Ymd'));
    }

    // =========================================================================
    // PRIVATE HELPERS
    // =========================================================================

    /**
     * Terapkan styling Excel: judul, header kolom, border data.
     */
    private function _styleHeaderExcel($sheet, string $mergeTitleRange, string $headerRange, int $lastDataRow): void
    {
        // Style judul
        $sheet->getStyle($mergeTitleRange)->applyFromArray([
            'font'      => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E3A5F']],
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 12],
        ]);

        // Style header kolom (baris 3)
        $sheet->getStyle($headerRange)->applyFromArray([
            'font'      => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']],
            'borders'   => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
        ]);

        // Border semua data
        if ($lastDataRow >= 4) {
            [$startCol] = explode(':', $headerRange);
            $startCol   = preg_replace('/\d/', '', $startCol);
            [, $endCol] = explode(':', $headerRange);
            $endCol     = preg_replace('/\d/', '', $endCol);

            $sheet->getStyle($startCol . '4:' . $endCol . $lastDataRow)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN]],
            ]);
        }
    }

    /**
     * Download file Excel ke browser.
     */
    private function _downloadExcel(Spreadsheet $spreadsheet, string $filename): void
    {
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
