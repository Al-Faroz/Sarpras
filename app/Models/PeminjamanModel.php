<?php

namespace App\Models;

use CodeIgniter\Model;

class PeminjamanModel extends Model
{
    protected $table      = 'peminjaman';
    protected $primaryKey = 'id_peminjaman';

    protected $allowedFields = [
        'id_inventaris',
        'nama_peminjam',
        'keterangan',
        'tanggal',
        'status_approval',
        'tanggal_kembalikan',
        'id_user_petugas',
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // READ — Ambil data peminjaman beserta relasi inventaris & master
    // =========================================================================

    /**
     * Ambil semua peminjaman dengan JOIN ke inventaris → master_kode_sakti & users.
     * Dipakai oleh: halaman display, export PDF/Excel.
     */
    public function getPeminjaman(): array
    {
        return $this->db->table('peminjaman p')
            ->join('inventaris i',       'i.id_inventaris = p.id_inventaris')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u',            'u.id_user = p.id_user_petugas', 'left')
            ->select('
                p.id_peminjaman,
                p.id_inventaris,
                i.no_inv,
                k.jenis_barang,
                i.merk,
                i.kondisi,
                i.kode_dana,
                i.tahun,
                p.nama_peminjam,
                p.keterangan,
                p.tanggal,
                p.status_approval,
                p.tanggal_kembalikan,
                p.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->orderBy('p.id_peminjaman', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Ambil satu data peminjaman berdasarkan id_peminjaman,
     * lengkap dengan detail inventaris.
     * Dipakai oleh: halaman edit peminjaman (admin).
     */
    public function getPeminjamanById(int $id): ?array
    {
        $row = $this->db->table('peminjaman p')
            ->join('inventaris i',       'i.id_inventaris = p.id_inventaris')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u',            'u.id_user = p.id_user_petugas', 'left')
            ->select('
                p.id_peminjaman,
                p.id_inventaris,
                i.no_inv,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.kondisi,
                p.nama_peminjam,
                p.keterangan,
                p.tanggal,
                p.status_approval,
                p.tanggal_kembalikan,
                p.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->where('p.id_peminjaman', $id)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // SEARCH — Pencarian untuk tabel display admin
    // =========================================================================

    /**
     * Cari peminjaman berdasarkan keyword di kolom:
     * no_inv, jenis_barang, merk, nama_peminjam, status_approval.
     * Dipakai oleh: fitur search di halaman peminjaman_display.
     */
    public function searchPeminjaman(string $keyword): array
    {
        return $this->db->table('peminjaman p')
            ->join('inventaris i',       'i.id_inventaris = p.id_inventaris')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u',            'u.id_user = p.id_user_petugas', 'left')
            ->select('
                p.id_peminjaman,
                p.id_inventaris,
                i.no_inv,
                k.jenis_barang,
                i.merk,
                i.kondisi,
                p.nama_peminjam,
                p.keterangan,
                p.tanggal,
                p.status_approval,
                p.tanggal_kembalikan,
                p.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->groupStart()
                ->like('i.no_inv', $keyword)
                ->orLike('k.jenis_barang', $keyword)
                ->orLike('i.merk', $keyword)
                ->orLike('p.nama_peminjam', $keyword)
                ->orLike('p.status_approval', $keyword)
                ->orLike('p.tanggal', $keyword)
            ->groupEnd()
            ->orderBy('p.id_peminjaman', 'DESC')
            ->get()->getResultArray();
    }

    // =========================================================================
    // CREATE — Simpan permohonan peminjaman baru (dari landing page via QR Scan)
    // =========================================================================

    /**
     * Simpan data peminjaman baru dari landing page.
     * id_inventaris sudah diketahui dari hasil scan QR.
     *
     * @param  array $inputData  id_inventaris, nama_peminjam, keterangan, tanggal
     * @return int               id_peminjaman yang baru dibuat
     */
    public function simpanPeminjaman(array $inputData): int
    {
        $insertData = [
            'id_inventaris'     => $inputData['id_inventaris'],
            'nama_peminjam'     => $inputData['nama_peminjam'],
            'keterangan'        => $inputData['keterangan'],
            'tanggal'           => $inputData['tanggal'],
            'status_approval'   => 'Pending',
            'tanggal_kembalikan'=> null,
            'id_user_petugas'   => null,
        ];

        $this->insert($insertData);
        return (int) $this->db->insertID();
    }

    // =========================================================================
    // UPDATE — Admin setujui / tolak / update tanggal kembali
    // =========================================================================

    /**
     * Update status approval, tanggal kembalikan, dan catat id petugas.
     * Dipanggil oleh AdminPeminjaman::update().
     *
     * @param  int   $id
     * @param  array $inputData  status_approval, tanggal_kembalikan, id_user_petugas
     * @return bool
     */
    public function updatePeminjaman(int $id, array $inputData): bool
    {
        $updateData = [
            'status_approval'    => $inputData['status_approval'],
            'tanggal_kembalikan' => $inputData['tanggal_kembalikan'] ?: null,
            'id_user_petugas'    => $inputData['id_user_petugas'],
        ];

        return $this->update($id, $updateData);
    }

    // =========================================================================
    // HELPER — Info inventaris untuk auto-fill form pinjam (dari QR scan)
    // =========================================================================

    /**
     * Ambil detail inventaris berdasarkan id_inventaris.
     * Dipakai oleh: LandingPage::form_pinjam() setelah redirect dari QR scan,
     * untuk mengisi field form secara otomatis.
     *
     * @param  int $idInventaris
     * @return array|null
     */
    public function getDetailAsetUntukPinjam(int $idInventaris): ?array
    {
        $row = $this->db->table('inventaris i')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->select('
                i.id_inventaris,
                i.no_inv,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.kondisi,
                i.lokasi
            ')
            ->where('i.id_inventaris', $idInventaris)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // STATISTIK — Untuk dashboard
    // =========================================================================

    /**
     * Hitung peminjaman per status.
     * Return: ['Pending' => N, 'Disetujui' => N, 'Ditolak' => N]
     */
    public function getStatistikStatus(): array
    {
        $rows = $this->db->table('peminjaman')
            ->select('status_approval, COUNT(*) as total')
            ->groupBy('status_approval')
            ->get()->getResultArray();

        $result = ['Pending' => 0, 'Disetujui' => 0, 'Ditolak' => 0];
        foreach ($rows as $row) {
            $result[$row['status_approval']] = (int) $row['total'];
        }

        return $result;
    }

    /**
     * Hitung peminjaman yang sedang aktif (Disetujui & belum dikembalikan).
     */
    public function getTotalAktif(): int
    {
        return (int) $this->db->table('peminjaman')
            ->where('status_approval', 'Disetujui')
            ->where('tanggal_kembalikan IS NULL', null, false)
            ->countAllResults();
    }
}
