<?php

namespace App\Models;

use CodeIgniter\Model;

class InventarisModel extends Model
{
    protected $table      = 'inventaris';
    protected $primaryKey = 'id_inventaris';

    protected $allowedFields = [
        'no_inv',
        'kode',
        'merk',
        'kode_dana',
        'tahun',
        'no_urut',
        'kondisi',
        'lokasi',
        'id_user_petugas',
    ];

    protected $useTimestamps = false;

    // =========================================================================
    // KODE SUMBER DANA
    // =========================================================================
    public static $sumberDana = [
        'A' => 'Dana DIPA',
        'B' => 'Dana BOSDA',
        'D' => 'Dana KOMITE',
        'E' => 'Dana Lainnya',
    ];

    // =========================================================================
    // READ — Ambil semua data inventaris beserta JOIN ke master & users
    // =========================================================================

    public function getInventaris(): array
    {
        return $this->db->table('inventaris i')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u', 'u.id_user = i.id_user_petugas')
            ->select('
                i.id_inventaris,
                i.no_inv,
                i.kode,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.no_urut,
                i.kondisi,
                i.lokasi,
                i.created_at,
                i.updated_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->orderBy('i.id_inventaris', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Ambil inventaris DIPA saja (kode_dana = 'A') — untuk export Excel DIPA.
     */
    public function getInventarisDipa(): array
    {
        return $this->db->table('inventaris i')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u', 'u.id_user = i.id_user_petugas')
            ->select('
                i.id_inventaris,
                i.no_inv,
                i.kode,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.no_urut,
                i.kondisi,
                i.lokasi,
                i.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->where('i.kode_dana', 'A')
            ->orderBy('i.kode', 'ASC')
            ->orderBy('i.no_urut', 'ASC')
            ->get()->getResultArray();
    }

    public function getInventarisById(int $id): ?array
    {
        $row = $this->db->table('inventaris i')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u', 'u.id_user = i.id_user_petugas')
            ->select('
                i.id_inventaris,
                i.no_inv,
                i.kode,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.no_urut,
                i.kondisi,
                i.lokasi,
                i.created_at,
                i.updated_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->where('i.id_inventaris', $id)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // SEARCH
    // =========================================================================

    public function searchInventaris(string $keyword): array
    {
        return $this->db->table('inventaris i')
            ->join('master_kode_sakti k', 'k.kode = i.kode')
            ->join('users u', 'u.id_user = i.id_user_petugas')
            ->select('
                i.id_inventaris,
                i.no_inv,
                i.kode,
                k.jenis_barang,
                i.merk,
                i.kode_dana,
                i.tahun,
                i.no_urut,
                i.kondisi,
                i.lokasi,
                i.created_at,
                u.nama_lengkap AS nama_petugas
            ')
            ->groupStart()
                ->like('i.no_inv', $keyword)
                ->orLike('k.jenis_barang', $keyword)
                ->orLike('i.merk', $keyword)
                ->orLike('i.tahun', $keyword)
                ->orLike('i.kondisi', $keyword)
                ->orLike('i.kode', $keyword)
            ->groupEnd()
            ->orderBy('i.id_inventaris', 'DESC')
            ->get()->getResultArray();
    }

    // =========================================================================
    // MASTER KODE SAKTI
    // =========================================================================

    public function getKodeSaktiAll(): array
    {
        return $this->db->table('master_kode_sakti')
            ->select('kode, jenis_barang')
            ->orderBy('kode', 'ASC')
            ->get()->getResultArray();
    }

    public function searchKodeSakti(string $keyword): array
    {
        return $this->db->table('master_kode_sakti')
            ->select('kode, jenis_barang')
            ->groupStart()
                ->like('kode', $keyword)
                ->orLike('jenis_barang', $keyword)
            ->groupEnd()
            ->orderBy('jenis_barang', 'ASC')
            ->limit(20)
            ->get()->getResultArray();
    }

    public function getKodeSaktiByKode(string $kode): ?array
    {
        $row = $this->db->table('master_kode_sakti')
            ->where('kode', $kode)
            ->get()->getRowArray();

        return $row ?: null;
    }

    // =========================================================================
    // GENERATE NO_INV — Logika inti pembentukan nomor inventaris
    // =========================================================================

    /**
     * Hitung nomor urut berikutnya.
     *
     * Dua jalur yang TIDAK saling mempengaruhi:
     * - Jalur DIPA (kode_dana = 'A')  : dihitung dari baris ber-dana 'A' saja.
     * - Jalur non-DIPA (B/D/E)        : dihitung dari baris ber-dana selain 'A'.
     *
     * @param  string $kode    Kode SAKTI, misal "3.10.01.02.002"
     * @param  bool   $isDipa  true = jalur DIPA, false = jalur non-DIPA
     * @return int             Nomor urut berikutnya
     */
    private function getNextUrut(string $kode, bool $isDipa = false): int
    {
        $builder = $this->db->table('inventaris')
            ->selectMax('no_urut', 'max_urut')
            ->where('kode', $kode);

        $isDipa ? $builder->where('kode_dana', 'A') : $builder->where('kode_dana !=', 'A');

        $row = $builder->get()->getRowArray();

        return isset($row['max_urut']) && $row['max_urut'] !== null
            ? (int) $row['max_urut'] + 1
            : 1;
    }

    private function formatUrut(int $urut): string
    {
        return str_pad($urut, 4, '0', STR_PAD_LEFT);
    }

    private function buildNoInv(string $kode, string $kodeDana, string $tahun, int $urut): string
    {
        return $kode . '.' . $kodeDana . '.' . $tahun . '.' . $this->formatUrut($urut);
    }

    // =========================================================================
    // CREATE — Simpan satu aset baru (form manual)
    // =========================================================================

    /**
     * Simpan data inventaris baru.
     *
     * Dua alur:
     * - NON-DIPA (B/D/E): no_inv & no_urut di-generate otomatis, race-condition-safe.
     * - DIPA (A)         : no_inv wajib diisi admin (nomor resmi dari aplikasi pemerintah).
     *                      no_urut tetap dihitung otomatis untuk bookkeeping internal.
     *
     * @param  array $inputData  kode, merk, kode_dana, tahun, kondisi, lokasi,
     *                           id_user_petugas, no_inv (wajib jika kode_dana = 'A')
     * @return array ['success', 'no_inv', 'id', 'error']
     */
    public function simpanInventaris(array $inputData): array
    {
        $db     = $this->db;
        $isDipa = $inputData['kode_dana'] === 'A';
        $db->transStart();

        try {
            // Lock baris relevan untuk mencegah race condition
            $lockSql = 'SELECT MAX(no_urut) FROM inventaris WHERE kode = ? AND kode_dana '
                . ($isDipa ? '= ?' : '!= ?') . ' FOR UPDATE';
            $db->query($lockSql, [$inputData['kode'], $isDipa ? 'A' : 'A']);

            $urut = $this->getNextUrut($inputData['kode'], $isDipa);

            if ($isDipa) {
                // DIPA: no_inv dari aplikasi pemerintah (diinput manual admin)
                $noInv = trim($inputData['no_inv'] ?? '');

                if ($noInv === '') {
                    $db->transRollback();
                    return ['success' => false, 'no_inv' => '', 'id' => 0,
                        'error' => 'No. Inventaris (resmi DIPA) wajib diisi untuk sumber dana DIPA.'];
                }

                if ($this->cekNoInvExists($noInv)) {
                    $db->transRollback();
                    return ['success' => false, 'no_inv' => '', 'id' => 0,
                        'error' => 'No. Inventaris "' . $noInv . '" sudah terdaftar, tidak boleh duplikat.'];
                }
            } else {
                // Non-DIPA: no_inv dibentuk otomatis dari skema KODE.DANA.TAHUN.XXXX
                $noInv = $this->buildNoInv(
                    $inputData['kode'],
                    $inputData['kode_dana'],
                    $inputData['tahun'],
                    $urut
                );
            }

            $insertData = [
                'no_inv'          => $noInv,
                'kode'            => $inputData['kode'],
                'merk'            => $inputData['merk'],
                'kode_dana'       => $inputData['kode_dana'],
                'tahun'           => $inputData['tahun'],
                'no_urut'         => $urut,
                'kondisi'         => $inputData['kondisi'],
                'lokasi'          => $inputData['lokasi'],
                'id_user_petugas' => $inputData['id_user_petugas'],
            ];

            $this->insert($insertData);
            $newId = $db->insertID();

            $db->transComplete();

            if ($db->transStatus() === false) {
                return ['success' => false, 'no_inv' => '', 'id' => 0, 'error' => 'Transaksi database gagal.'];
            }

            return ['success' => true, 'no_inv' => $noInv, 'id' => $newId, 'error' => ''];

        } catch (\Exception $e) {
            $db->transRollback();
            return ['success' => false, 'no_inv' => '', 'id' => 0, 'error' => $e->getMessage()];
        }
    }

    /**
     * Import batch aset DIPA dari array hasil parse Excel.
     *
     * Setiap baris: [no_inv, kode, nama_barang (diabaikan—kita pakai master),
     *                merk, tahun, kondisi, lokasi]
     *
     * Return array hasil per baris:
     * ['baris' => N, 'no_inv' => '...', 'status' => 'ok'|'skip'|'error', 'pesan' => '...']
     *
     * @param  array $rows           Baris data dari Excel (array of assoc array)
     * @param  int   $idUserPetugas  ID user yang melakukan import
     * @return array                 Ringkasan hasil per baris
     */
    public function importDipaBatch(array $rows, int $idUserPetugas): array
    {
        $hasil = [];

        foreach ($rows as $idx => $row) {
            $noBaris = $idx + 2; // baris Excel dimulai dari 2 (baris 1 = header)

            $noInv  = trim($row['no_inv']  ?? '');
            $kode   = trim($row['kode']    ?? '');
            $merk   = trim($row['merk']    ?? '');
            $tahun  = trim($row['tahun']   ?? '');
            $kondisi = trim($row['kondisi'] ?? 'Baik');
            $lokasi  = trim($row['lokasi']  ?? 'Statis');

            // ── Validasi wajib ──
            if ($noInv === '' || $kode === '' || $merk === '' || $tahun === '') {
                $hasil[] = [
                    'baris'  => $noBaris,
                    'no_inv' => $noInv ?: '(kosong)',
                    'status' => 'error',
                    'pesan'  => 'Kolom no_inv / kode / merk / tahun tidak boleh kosong.',
                ];
                continue;
            }

            // ── Validasi kode SAKTI ──
            $cekKode = $this->getKodeSaktiByKode($kode);
            if (!$cekKode) {
                $hasil[] = [
                    'baris'  => $noBaris,
                    'no_inv' => $noInv,
                    'status' => 'error',
                    'pesan'  => 'Kode SAKTI "' . $kode . '" tidak ditemukan di master.',
                ];
                continue;
            }

            // ── Validasi enum kondisi ──
            if (!in_array($kondisi, ['Baik', 'Rusak Ringan', 'Rusak Berat'])) {
                $kondisi = 'Baik'; // default fallback
            }

            // ── Validasi enum lokasi ──
            if (!in_array($lokasi, ['Statis', 'Mobile'])) {
                $lokasi = 'Statis';
            }

            // ── Skip kalau no_inv sudah ada ──
            if ($this->cekNoInvExists($noInv)) {
                $hasil[] = [
                    'baris'  => $noBaris,
                    'no_inv' => $noInv,
                    'status' => 'skip',
                    'pesan'  => 'No. Inventaris sudah terdaftar, dilewati (tidak duplikat).',
                ];
                continue;
            }

            // ── Simpan dengan jalur DIPA ──
            $simpan = $this->simpanInventaris([
                'no_inv'          => $noInv,
                'kode'            => $kode,
                'merk'            => $merk,
                'kode_dana'       => 'A',
                'tahun'           => $tahun,
                'kondisi'         => $kondisi,
                'lokasi'          => $lokasi,
                'id_user_petugas' => $idUserPetugas,
            ]);

            $hasil[] = [
                'baris'  => $noBaris,
                'no_inv' => $noInv,
                'status' => $simpan['success'] ? 'ok' : 'error',
                'pesan'  => $simpan['success'] ? 'Berhasil diimpor.' : $simpan['error'],
            ];
        }

        return $hasil;
    }

    public function cekNoInvExists(string $noInv, ?int $excludeId = null): bool
    {
        $builder = $this->db->table('inventaris')->where('no_inv', $noInv);
        if ($excludeId !== null) {
            $builder->where('id_inventaris !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    // =========================================================================
    // UPDATE
    // =========================================================================

    /**
     * Update data inventaris.
     * no_inv, kode, no_urut dikunci — tidak berubah setelah dibuat.
     *
     * Khusus aset DIPA: no_inv boleh diupdate (untuk koreksi nomor dari pemerintah),
     * dengan pengecekan duplikat.
     *
     * @param  int   $id
     * @param  array $inputData
     * @return array ['success', 'error']
     */
    public function updateInventaris(int $id, array $inputData): array
    {
        $isDipa = ($inputData['kode_dana'] ?? '') === 'A';

        $updateData = [
            'merk'            => $inputData['merk'],
            'kode_dana'       => $inputData['kode_dana'],
            'tahun'           => $inputData['tahun'],
            'kondisi'         => $inputData['kondisi'],
            'lokasi'          => $inputData['lokasi'],
            'id_user_petugas' => $inputData['id_user_petugas'],
        ];

        // Untuk DIPA: izinkan koreksi no_inv
        if ($isDipa && !empty($inputData['no_inv'])) {
            $noInvBaru = trim($inputData['no_inv']);
            if ($this->cekNoInvExists($noInvBaru, $id)) {
                return ['success' => false, 'error' => 'No. Inventaris "' . $noInvBaru . '" sudah dipakai aset lain.'];
            }
            $updateData['no_inv'] = $noInvBaru;
        }

        $this->update($id, $updateData);
        return ['success' => true, 'error' => ''];
    }

    public function updateKondisi(int $id, string $kondisi): bool
    {
        return $this->update($id, ['kondisi' => $kondisi]);
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    public static function getDaftarTahun(): array
    {
        $tahunSekarang = (int) date('Y');
        $daftar = [];

        for ($t = $tahunSekarang; $t >= 2016; $t--) {
            $daftar[] = (string) $t;
        }

        $daftar[] = 'pra2016';

        return $daftar;
    }

    public static function getLabelDana(string $kode): string
    {
        return self::$sumberDana[$kode] ?? 'Tidak Diketahui';
    }

    /**
     * Preview no_inv sebelum simpan.
     *
     * Non-DIPA: tampilkan format KODE.DANA.TAHUN.XXXX dengan urut perkiraan.
     * DIPA     : kembalikan string khusus (no_inv diisi manual, bukan preview).
     */
    public function previewNoInv(string $kode, string $kodeDana, string $tahun): string
    {
        if ($kodeDana === 'A') {
            return '— No. Inventaris DIPA diisi manual —';
        }

        $urut = $this->getNextUrut($kode, false);
        return $kode . '.' . $kodeDana . '.' . $tahun . '.' . $this->formatUrut($urut);
    }

    // =========================================================================
    // STATISTIK — Untuk dashboard
    // =========================================================================

    public function getStatistikKondisi(): array
    {
        $rows = $this->db->table('inventaris')
            ->select('kondisi, COUNT(*) as total')
            ->groupBy('kondisi')
            ->get()->getResultArray();

        $result = ['Baik' => 0, 'Rusak Ringan' => 0, 'Rusak Berat' => 0];
        foreach ($rows as $row) {
            $result[$row['kondisi']] = (int) $row['total'];
        }

        return $result;
    }

    public function getTotalAset(): int
    {
        return $this->countAll();
    }
}
