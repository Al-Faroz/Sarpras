<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>
<?php $role = session()->get('role'); ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2"><i class="mdi mdi-home"></i></span>
        Dashboard
        <small class="text-muted fs-6 fw-normal ms-2">
          <?= $role === 'admin' ? 'Administrator' : 'Operator' ?>
        </small>
      </h3>
      <span class="text-muted small">Per <?= date('d F Y, H:i') ?> WIB</span>
    </div>

    <!-- BARIS 1: Statistik Kondisi Aset (4 kartu) -->
    <div class="row">

      <!-- Total Aset -->
      <div class="col-xl-3 col-md-6 grid-margin stretch-card">
        <div class="card shadow" style="background:linear-gradient(135deg,#1E3A5F,#2d6a9f); border:none;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                   style="width:52px;height:52px;background:rgba(255,255,255,0.18);">
                <i class="mdi mdi-package-variant-closed text-white" style="font-size:1.7rem;"></i>
              </div>
              <div class="text-white overflow-hidden">
                <p class="mb-0" style="font-size:0.75rem;opacity:0.8;white-space:nowrap;">Total Aset Terdaftar</p>
                <h3 class="mb-0 font-weight-bold" style="font-size:2rem;line-height:1;"><?= number_format($total_aset) ?></h3>
                <p class="mb-0" style="font-size:0.7rem;opacity:0.65;">unit fisik inventaris</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Kondisi Baik -->
      <div class="col-xl-3 col-md-6 grid-margin stretch-card">
        <div class="card shadow" style="background:linear-gradient(135deg,#1a7a4a,#2ecc71); border:none;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                   style="width:52px;height:52px;background:rgba(255,255,255,0.18);">
                <i class="mdi mdi-check-circle-outline text-white" style="font-size:1.7rem;"></i>
              </div>
              <div class="text-white overflow-hidden">
                <p class="mb-0" style="font-size:0.75rem;opacity:0.8;">Kondisi Baik</p>
                <h3 class="mb-0 font-weight-bold" style="font-size:2rem;line-height:1;"><?= number_format($kondisi_aset['Baik']) ?></h3>
                <p class="mb-0" style="font-size:0.7rem;opacity:0.65;">unit siap pakai</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Rusak Ringan -->
      <div class="col-xl-3 col-md-6 grid-margin stretch-card">
        <div class="card shadow" style="background:linear-gradient(135deg,#b8620a,#f39c12); border:none;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                   style="width:52px;height:52px;background:rgba(255,255,255,0.18);">
                <i class="mdi mdi-alert-outline text-white" style="font-size:1.7rem;"></i>
              </div>
              <div class="text-white overflow-hidden">
                <p class="mb-0" style="font-size:0.75rem;opacity:0.8;">Rusak Ringan</p>
                <h3 class="mb-0 font-weight-bold" style="font-size:2rem;line-height:1;"><?= number_format($kondisi_aset['Rusak Ringan']) ?></h3>
                <p class="mb-0" style="font-size:0.7rem;opacity:0.65;">perlu perhatian</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Rusak Berat -->
      <div class="col-xl-3 col-md-6 grid-margin stretch-card">
        <div class="card shadow" style="background:linear-gradient(135deg,#9b1c1c,#e74c3c); border:none;">
          <div class="card-body p-3">
            <div class="d-flex align-items-center gap-3">
              <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle"
                   style="width:52px;height:52px;background:rgba(255,255,255,0.18);">
                <i class="mdi mdi-close-circle-outline text-white" style="font-size:1.7rem;"></i>
              </div>
              <div class="text-white overflow-hidden">
                <p class="mb-0" style="font-size:0.75rem;opacity:0.8;">Rusak Berat</p>
                <h3 class="mb-0 font-weight-bold" style="font-size:2rem;line-height:1;"><?= number_format($kondisi_aset['Rusak Berat']) ?></h3>
                <p class="mb-0" style="font-size:0.7rem;opacity:0.65;">penanganan segera</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

    <!-- BARIS 2: Modul cards -->
    <div class="row">

      <!-- Peminjaman -->
      <div class="col-md-<?= $role === 'admin' ? '4' : '6' ?> grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title d-flex align-items-center gap-2 mb-3">
              <span class="p-1 rounded" style="background:#eaf0fb;">
                <i class="mdi mdi-bookmark-outline text-primary" style="font-size:1.2rem;"></i>
              </span>
              Peminjaman
            </h5>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span><span class="badge bg-warning text-dark">Pending</span></span>
                <strong class="fs-5"><?= $status_pinjam['Pending'] ?></strong>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span><span class="badge bg-success">Disetujui</span></span>
                <strong class="fs-5"><?= $status_pinjam['Disetujui'] ?></strong>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2">
                <span><span class="badge bg-danger">Ditolak</span></span>
                <strong class="fs-5"><?= $status_pinjam['Ditolak'] ?></strong>
              </div>
            </div>
            <p class="text-muted small mb-3">
              <i class="mdi mdi-clock-alert text-warning me-1"></i>
              <strong><?= $total_pinjam_aktif ?></strong> aset sedang aktif dipinjam
            </p>
            <a href="<?= base_url($role . '/peminjaman') ?>" class="btn btn-outline-primary btn-sm w-100">
              <i class="mdi mdi-arrow-right me-1"></i> Kelola Peminjaman
            </a>
          </div>
        </div>
      </div>

      <!-- Laporan Kerusakan -->
      <div class="col-md-<?= $role === 'admin' ? '4' : '6' ?> grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title d-flex align-items-center gap-2 mb-3">
              <span class="p-1 rounded" style="background:#fdecea;">
                <i class="mdi mdi-alert-circle-outline text-danger" style="font-size:1.2rem;"></i>
              </span>
              Laporan Kerusakan
            </h5>
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span><span class="badge bg-danger">Masuk</span></span>
                <strong class="fs-5"><?= $status_laporan['Masuk'] ?></strong>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <span><span class="badge bg-warning text-dark">Proses</span></span>
                <strong class="fs-5"><?= $status_laporan['Proses'] ?></strong>
              </div>
              <div class="d-flex justify-content-between align-items-center py-2">
                <span><span class="badge bg-success">Selesai</span></span>
                <strong class="fs-5"><?= $status_laporan['Selesai'] ?></strong>
              </div>
            </div>
            <p class="text-muted small mb-3">
              <i class="mdi mdi-alert text-danger me-1"></i>
              <strong><?= $status_laporan['Masuk'] + $status_laporan['Proses'] ?></strong> laporan belum selesai
            </p>
            <a href="<?= base_url($role . '/laporan') ?>" class="btn btn-outline-danger btn-sm w-100">
              <i class="mdi mdi-arrow-right me-1"></i> Kelola Laporan
            </a>
          </div>
        </div>
      </div>

      <!-- Pemeliharaan — admin only -->
      <?php if ($role === 'admin'): ?>
      <div class="col-md-4 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title d-flex align-items-center gap-2 mb-3">
              <span class="p-1 rounded" style="background:#eaf7ef;">
                <i class="mdi mdi-wrench text-success" style="font-size:1.2rem;"></i>
              </span>
              Pemeliharaan
            </h5>
            <div class="text-center my-3">
              <p class="text-muted small mb-1">Total Catatan Pemeliharaan</p>
              <h2 class="font-weight-bold text-success mb-0"><?= number_format($total_pemeliharaan) ?></h2>
            </div>
            <div class="bg-light rounded p-2 text-center mb-3">
              <small class="text-muted d-block">Biaya bulan ini</small>
              <strong class="text-success">Rp <?= number_format($biaya_bulan_ini, 0, ',', '.') ?></strong>
            </div>
            <a href="<?= base_url('admin/pemeliharaan') ?>" class="btn btn-outline-success btn-sm w-100">
              <i class="mdi mdi-arrow-right me-1"></i> Kelola Pemeliharaan
            </a>
          </div>
        </div>
      </div>
      <?php endif; ?>

    </div>

    <!-- BARIS 3: Akses Cepat -->
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h5 class="card-title mb-3">⚡ Akses Cepat</h5>
            <div class="d-flex flex-wrap gap-2">

              <a href="<?= base_url('admin/inventaris/tambah') ?>" class="btn btn-gradient-success btn-icon-text">
                <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Aset
              </a>

              <?php if ($role === 'admin'): ?>
              <a href="<?= base_url('admin/pemeliharaan/tambah') ?>" class="btn btn-gradient-primary btn-icon-text">
                <i class="mdi mdi-wrench btn-icon-prepend"></i> Catat Pemeliharaan
              </a>
              <?php endif; ?>

              <a href="<?= base_url('admin/scanner') ?>" class="btn btn-gradient-info btn-icon-text">
                <i class="mdi mdi-qrcode-scan btn-icon-prepend"></i> Scan QR Aset
              </a>

              <a href="<?= base_url('admin/inventaris/import_dipa') ?>" class="btn btn-gradient-dark btn-icon-text">
                <i class="mdi mdi-file-import btn-icon-prepend"></i> Import DIPA
              </a>

              <a href="<?= base_url('export/inventaris_excel') ?>" class="btn btn-outline-success btn-icon-text">
                <i class="mdi mdi-file-excel btn-icon-prepend"></i> Export Inventaris
              </a>

              <?php if ($role === 'admin'): ?>
              <a href="<?= base_url('admin/users') ?>" class="btn btn-outline-secondary btn-icon-text">
                <i class="mdi mdi-account-multiple btn-icon-prepend"></i> Kelola User
              </a>
              <?php endif; ?>

            </div>
          </div>
        </div>
      </div>
    </div>

  </div>
  <footer class="footer">
    <div class="container-fluid d-flex justify-content-between align-items-center">
      <span class="text-muted small">Copyright &copy; <?= date('Y') ?> Portal Sarpras MTsN 4 Jombang.</span>
      <span class="text-muted small">Login sebagai: <strong><?= esc(session()->get('nama_lengkap')) ?></strong></span>
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
