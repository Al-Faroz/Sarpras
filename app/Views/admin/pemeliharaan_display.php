<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-wrench"></i>
        </span> Data Pemeliharaan Aset
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item active">
            <a href="<?= base_url('admin/pemeliharaan/tambah') ?>" class="btn btn-gradient-success btn-icon-text btn-sm">
              <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Riwayat Baru
            </a>
          </li>
        </ul>
      </nav>
      <div class="btn-group">
        <a href="<?= base_url('export/pemeliharaan_pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
          <i class="mdi mdi-file-pdf"></i> PDF
        </a>
        <a href="<?= base_url('export/pemeliharaan_excel') ?>" class="btn btn-success btn-sm">
          <i class="mdi mdi-file-excel"></i> Excel
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">

            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Catatan Perawatan & Perbaikan Sarpras</h4>
              <form method="GET" action="<?= base_url('admin/pemeliharaan') ?>" class="d-flex" style="min-width:300px;">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="search"
                    placeholder="Cari nama, no. aset, tanggal..."
                    value="<?= esc($keyword) ?>">
                  <button class="btn btn-gradient-primary btn-sm px-3" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/pemeliharaan') ?>" class="btn btn-outline-secondary btn-sm px-2" title="Reset">
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
            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($keyword): ?>
              <p class="text-muted small mb-2">
                Hasil: <strong>"<?= esc($keyword) ?>"</strong> — <?= count($pemeliharaan) ?> data ditemukan.
              </p>
            <?php endif; ?>

            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                  <tr>
                    <th width="4%">No</th>
                    <th>Nama Pemeliharaan</th>
                    <th>No. Aset</th>
                    <th>Tanggal</th>
                    <th>Biaya (Rp)</th>
                    <th>Petugas</th>
                    <th>Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($pemeliharaan)): ?>
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">
                        <i class="mdi mdi-inbox-arrow-down mdi-36px d-block mb-2"></i>
                        <?= $keyword ? 'Tidak ada data yang cocok.' : 'Belum ada catatan pemeliharaan.' ?>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($pemeliharaan as $row): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <span class="font-weight-bold"><?= esc($row['nama_pemeliharaan']) ?></span><br>
                          <small class="text-muted"><?= esc(mb_substr($row['keterangan'], 0, 60)) ?>...</small>
                        </td>
                        <td>
                          <?php if ($row['no_aset']): ?>
                            <code class="small"><?= esc($row['no_aset']) ?></code>
                          <?php else: ?>
                            <span class="text-muted">—</span>
                          <?php endif; ?>
                        </td>
                        <td><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                        <td class="text-danger font-weight-bold">
                          Rp <?= number_format($row['biaya'], 0, ',', '.') ?>
                        </td>
                        <td><?= esc($row['nama_petugas']) ?></td>
                        <td>
                          <a href="<?= base_url('admin/pemeliharaan/edit/' . $row['id_pemeliharaan']) ?>"
                            class="btn btn-inverse-info btn-sm btn-icon" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                          </a>
                          <a href="<?= base_url('admin/pemeliharaan/hapus/' . $row['id_pemeliharaan']) ?>"
                            class="btn btn-inverse-danger btn-sm btn-icon" title="Hapus"
                            onclick="return confirm('Hapus catatan pemeliharaan ini?')">
                            <i class="mdi mdi-delete"></i>
                          </a>
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
