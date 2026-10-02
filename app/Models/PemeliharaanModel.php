<?php

namespace App\Models;

use CodeIgniter\Model;

class PemeliharaanModel extends Model
{
    protected $table      = 'pemeliharaan';
    protected $primaryKey = 'id_pemeliharaan';

    protected $allowedFields = [
        'nama_pemeliharaan',
        'no_aset',
        'keterangan',
        'tanggal',
        'biaya',
        'id_user_petugas',
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // READ — Ambil data pemeliharaan beserta relasi users
    // =========================================================================

    /**
     * Ambil semua pemeliharaan dengan JOIN ke users.
     * Dipakai oleh: halaman display, export PDF/Excel.
     */
    public function getPemeliharaan(): array
    {
        return $this->db->table('pemeliharaan p')
            ->join('users u', 'u.id_user = p.id_user_petugas')
            ->select('
                p.id_pemeliharaan,
                p.nama_pemeliharaan,
                p.no_aset,
                p.keterangan,
                p.tanggal,
                p.biaya,
                p.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->orderBy('p.id_pemeliharaan', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Ambil satu data pemeliharaan berdasarkan id.
     */
    public function getPemeliharaanById(int $id): ?array
    {
        $row = $this->db->table('pemeliharaan p')
            ->join('users u', 'u.id_user = p.id_user_petugas')
            ->select('p.*, u.nama_lengkap AS nama_petugas')
            ->where('p.id_pemeliharaan', $id)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // SEARCH — Pencarian untuk tabel display admin
    // =========================================================================

    /**
     * Cari pemeliharaan berdasarkan keyword di kolom:
     * nama_pemeliharaan, no_aset, keterangan, tanggal, nama_petugas.
     */
    public function searchPemeliharaan(string $keyword): array
    {
        return $this->db->table('pemeliharaan p')
            ->join('users u', 'u.id_user = p.id_user_petugas')
            ->select('p.*, u.nama_lengkap AS nama_petugas')
            ->groupStart()
                ->like('p.nama_pemeliharaan', $keyword)
                ->orLike('p.no_aset', $keyword)
                ->orLike('p.keterangan', $keyword)
                ->orLike('p.tanggal', $keyword)
                ->orLike('u.nama_lengkap', $keyword)
            ->groupEnd()
            ->orderBy('p.id_pemeliharaan', 'DESC')
            ->get()->getResultArray();
    }

    // =========================================================================
    // STATISTIK — Untuk dashboard
    // =========================================================================

    /**
     * Hitung total biaya pemeliharaan bulan ini.
     */
    public function getTotalBiayaBulanIni(): int
    {
        $row = $this->db->table('pemeliharaan')
            ->selectSum('biaya', 'total')
            ->where('MONTH(tanggal)', date('m'), false)
            ->where('YEAR(tanggal)', date('Y'), false)
            ->get()->getRowArray();

        return isset($row['total']) ? (int) $row['total'] : 0;
    }

    /**
     * Hitung total seluruh catatan pemeliharaan.
     */
    public function getTotalPemeliharaan(): int
    {
        return $this->countAll();
    }
}
