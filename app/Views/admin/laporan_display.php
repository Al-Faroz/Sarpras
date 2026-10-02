<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-danger text-white me-2">
          <i class="mdi mdi-alert-circle-outline"></i>
        </span> Data Laporan Kerusakan
      </h3>
      <div class="btn-group">
        <a href="<?= base_url('export/laporan_pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
          <i class="mdi mdi-file-pdf"></i> PDF
        </a>
        <a href="<?= base_url('export/laporan_excel') ?>" class="btn btn-success btn-sm">
          <i class="mdi mdi-file-excel"></i> Excel
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Daftar Aduan Fasilitas & Sarpras</h4>
              <form method="GET" action="<?= base_url('admin/laporan') ?>" class="d-flex" style="min-width:300px;">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="search"
                    placeholder="Cari pelapor, barang, status..."
                    value="<?= esc($keyword) ?>">
                  <button class="btn btn-gradient-danger btn-sm px-3" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/laporan') ?>" class="btn btn-outline-secondary btn-sm px-2" title="Reset">
                      <i class="mdi mdi-close"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <?php if (session()->getFlashdata('pesan')): ?>
              <div class="alert alert-success alert-dismissible fade show">
                <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($keyword): ?>
              <p class="text-muted small mb-2">
                Hasil: <strong>"<?= esc($keyword) ?>"</strong> — <?= count($laporan) ?> data ditemukan.
              </p>
            <?php endif; ?>

            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                  <tr>
                    <th width="4%">No</th>
                    <th>Tanggal</th>
                    <th>Pelapor</th>
                    <th>Aset Dilaporkan</th>
                    <th>Kerusakan</th>
                    <th>Bukti Awal</th>
                    <th>Status</th>
                    <th>Bukti Akhir</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($laporan)): ?>
                    <tr>
                      <td colspan="10" class="text-center py-4 text-muted">
                        <i class="mdi mdi-inbox-arrow-down mdi-36px d-block mb-2"></i>
                        <?= $keyword ? 'Tidak ada data yang cocok.' : 'Belum ada laporan masuk.' ?>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($laporan as $row): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <small><?= $row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '—' ?></small><br>
                          <small class="text-muted"><?= $row['created_at'] ? date('H:i', strtotime($row['created_at'])) : '' ?></small>
                        </td>
                        <td>
                          <span class="font-weight-bold"><?= esc($row['nama_pelapor']) ?></span><br>
                          <small class="text-muted"><?= esc($row['unit_kerja']) ?></small>
                        </td>
                        <td>
                          <span class="font-weight-bold"><?= esc($row['nama_barang']) ?></span><br>
                          <small class="text-muted">Kategori: <?= esc($row['nilai_kategori']) ?></small>
                        </td>
                        <td>
                          <small><?= esc(mb_substr($row['keterangan_kerusakan'], 0, 50)) ?>...</small>
                        </td>
                        <td>
                          <?php if (!empty($row['foto_bukti_awal'])): ?>
                            <a href="<?= base_url('uploads/laporan/' . $row['foto_bukti_awal']) ?>"
                              target="_blank" class="badge badge-info">
                              <i class="mdi mdi-eye"></i> Lihat
                            </a>
                          <?php else: ?>
                            <span class="text-muted small">Tanpa Foto</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ($row['status'] == 'Selesai'): ?>
                            <span class="badge badge-success">Selesai</span>
                          <?php elseif ($row['status'] == 'Proses'): ?>
                            <span class="badge badge-warning text-dark">Proses</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Masuk</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if (!empty($row['foto_bukti_akhir'])): ?>
                            <a href="<?= base_url('uploads/laporan/' . $row['foto_bukti_akhir']) ?>"
                              target="_blank" class="badge badge-success">
                              <i class="mdi mdi-image"></i> Foto
                            </a>
                          <?php else: ?>
                            <span class="text-muted small fst-italic">Belum Ada</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?= $row['nama_petugas']
                            ? esc($row['nama_petugas'])
                            : '<span class="text-muted">—</span>' ?>
                        </td>
                        <td>
                          <?php if (session()->get('role') == 'admin'): ?>
                            <a href="<?= base_url('admin/laporan/edit/' . $row['id_laporan']) ?>"
                              class="btn btn-inverse-primary btn-sm btn-icon" title="Tindak Lanjut">
                              <i class="mdi mdi-wrench"></i>
                            </a>
                          <?php else: ?>
                            <span class="text-muted small">No Access</span>
                          <?php endif; ?>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
            <?= $this->include('layout/pagination') ?>

          </div>
        </div>
      </div>
    </div>

  </div>
  <footer class="footer">
    <div class="container-fluid">
      <span class="text-muted">Copyright © 2026 Portal Sarpras MTsN 4 Jombang.</span>
    </div>
  </footer>
</div>
</div>
</div>

<script src="<?= base_url('assets/purpleadmin/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/hoverable-collapse.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>
</body>
</html>
