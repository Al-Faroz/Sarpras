<?php

namespace App\Models;

use CodeIgniter\Model;

class LaporanModel extends Model
{
    protected $table      = 'laporan';
    protected $primaryKey = 'id_laporan';

    protected $allowedFields = [
        'nama_pelapor',
        'unit_kerja',
        'id_kategori_jenis',
        'nama_barang',
        'keterangan_kerusakan',
        'foto_bukti_awal',
        'tindakan',
        'foto_bukti_akhir',
        'status',
        'id_user_petugas',
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // READ — Ambil data laporan beserta relasi kategori & petugas
    // =========================================================================

    /**
     * Ambil semua laporan dengan JOIN ke master_kategori_jenis dan users.
     * Dipakai oleh: halaman display, export PDF/Excel.
     */
    public function getLaporan(): array
    {
        return $this->db->table('laporan l')
            ->join('master_kategori_jenis k', 'k.id_kategori_jenis = l.id_kategori_jenis', 'left')
            ->join('users u',                 'u.id_user = l.id_user_petugas', 'left')
            ->select('
                l.id_laporan,
                l.nama_pelapor,
                l.unit_kerja,
                l.id_kategori_jenis,
                k.nilai_kategori,
                l.nama_barang,
                l.keterangan_kerusakan,
                l.foto_bukti_awal,
                l.tindakan,
                l.foto_bukti_akhir,
                l.status,
                l.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->orderBy('l.id_laporan', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Ambil satu laporan berdasarkan id.
     */
    public function getLaporanById(int $id): ?array
    {
        $row = $this->db->table('laporan l')
            ->join('master_kategori_jenis k', 'k.id_kategori_jenis = l.id_kategori_jenis', 'left')
            ->join('users u',                 'u.id_user = l.id_user_petugas', 'left')
            ->select('l.*, k.nilai_kategori, u.nama_lengkap AS nama_petugas')
            ->where('l.id_laporan', $id)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // SEARCH — Pencarian untuk tabel display admin
    // =========================================================================

    /**
     * Cari laporan berdasarkan keyword di kolom:
     * nama_pelapor, unit_kerja, nilai_kategori, nama_barang, status.
     */
    public function searchLaporan(string $keyword): array
    {
        return $this->db->table('laporan l')
            ->join('master_kategori_jenis k', 'k.id_kategori_jenis = l.id_kategori_jenis', 'left')
            ->join('users u',                 'u.id_user = l.id_user_petugas', 'left')
            ->select('l.*, k.nilai_kategori, u.nama_lengkap AS nama_petugas')
            ->groupStart()
                ->like('l.nama_pelapor', $keyword)
                ->orLike('l.unit_kerja', $keyword)
                ->orLike('k.nilai_kategori', $keyword)
                ->orLike('l.nama_barang', $keyword)
                ->orLike('l.status', $keyword)
                ->orLike('l.keterangan_kerusakan', $keyword)
            ->groupEnd()
            ->orderBy('l.id_laporan', 'DESC')
            ->get()->getResultArray();
    }

    // =========================================================================
    // STATISTIK — Untuk dashboard
    // =========================================================================

    /**
     * Hitung laporan per status.
     * Return: ['Masuk' => N, 'Proses' => N, 'Selesai' => N]
     */
    public function getStatistikStatus(): array
    {
        $rows = $this->db->table('laporan')
            ->select('status, COUNT(*) as total')
            ->groupBy('status')
            ->get()->getResultArray();

        $result = ['Masuk' => 0, 'Proses' => 0, 'Selesai' => 0];
        foreach ($rows as $row) {
            $result[$row['status']] = (int) $row['total'];
        }

        return $result;
    }
}
