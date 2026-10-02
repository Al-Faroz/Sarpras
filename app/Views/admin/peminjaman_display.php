<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-bookmark-outline"></i>
        </span> Data Peminjaman Aset
      </h3>
      <div class="btn-group">
        <a href="<?= base_url('export/peminjaman_pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
          <i class="mdi mdi-file-pdf"></i> PDF
        </a>
        <a href="<?= base_url('export/peminjaman_excel') ?>" class="btn btn-success btn-sm">
          <i class="mdi mdi-file-excel"></i> Excel
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Daftar Permohonan & Riwayat Peminjaman</h4>
              <form method="GET" action="<?= base_url('admin/peminjaman') ?>" class="d-flex" style="min-width:320px;">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="search"
                    placeholder="Cari no. inventaris, jenis, merk, peminjam..."
                    value="<?= esc($keyword) ?>">
                  <button class="btn btn-gradient-primary btn-sm px-3" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/peminjaman') ?>" class="btn btn-outline-secondary btn-sm px-2" title="Reset">
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
                Hasil pencarian: <strong>"<?= esc($keyword) ?>"</strong> — <?= count($peminjaman) ?> data ditemukan.
              </p>
            <?php endif; ?>

            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                  <tr>
                    <th width="4%">No</th>
                    <th>No. Inventaris</th>
                    <th>Jenis Barang</th>
                    <th>Merk</th>
                    <th>Nama Peminjam</th>
                    <th>Keterangan</th>
                    <th>Tgl Pinjam</th>
                    <th>Status</th>
                    <th>Tgl Kembali</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($peminjaman)): ?>
                    <tr>
                      <td colspan="10" class="text-center py-4 text-muted">
                        <i class="mdi mdi-inbox-arrow-down mdi-36px d-block mb-2"></i>
                        <?= $keyword ? 'Tidak ada data yang cocok.' : 'Belum ada data peminjaman.' ?>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($peminjaman as $row): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td><code class="small"><?= esc($row['no_inv']) ?></code></td>
                        <td class="font-weight-bold"><?= esc($row['jenis_barang']) ?></td>
                        <td><?= esc($row['merk']) ?></td>
                        <td class="font-weight-bold"><?= esc($row['nama_peminjam']) ?></td>
                        <td><small class="text-muted fst-italic"><?= esc($row['keterangan']) ?></small></td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td>
                          <?php if ($row['status_approval'] == 'Disetujui'): ?>
                            <span class="badge badge-success">Disetujui</span>
                          <?php elseif ($row['status_approval'] == 'Ditolak'): ?>
                            <span class="badge badge-danger">Ditolak</span>
                          <?php else: ?>
                            <span class="badge badge-warning text-dark">Pending</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?= $row['tanggal_kembalikan']
                            ? date('d/m/Y', strtotime($row['tanggal_kembalikan']))
                            : '<span class="text-muted small">—</span>' ?>
                        </td>
                        <td>
                          <?php if (session()->get('role') == 'admin'): ?>
                            <a href="<?= base_url('admin/peminjaman/edit/' . $row['id_peminjaman']) ?>"
                              class="btn btn-inverse-primary btn-sm btn-icon" title="Update Status">
                              <i class="mdi mdi-pencil"></i>
                            </a>
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
