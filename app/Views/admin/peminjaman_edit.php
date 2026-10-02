<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-info text-white me-2">
          <i class="mdi mdi-checkbox-marked-circle-outline"></i>
        </span> Approval Peminjaman
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/peminjaman') ?>">Data Peminjaman</a></li>
          <li class="breadcrumb-item active">Tindak Lanjut</li>
        </ul>
      </nav>
    </div>

    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-4">Tindak Lanjut Permohonan Pinjam</h4>

            <form action="<?= base_url('admin/peminjaman/update/' . $pinjam['id_peminjaman']) ?>" method="POST">
              <?= csrf_field() ?>

              <!-- Detail Aset & Peminjam (Read-only) -->
              <div class="row bg-light p-3 mb-4 rounded border">
                <h6 class="text-muted fw-bold mb-3 col-12">
                  <i class="mdi mdi-information-outline text-info"></i> Detail Permohonan
                </h6>
                <div class="col-md-6 form-group mb-2">
                  <label class="text-muted small font-weight-bold">No. Inventaris</label>
                  <input type="text" class="form-control form-control-sm bg-white font-weight-bold"
                    value="<?= esc($pinjam['no_inv']) ?>" readonly>
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Jenis Barang</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= esc($pinjam['jenis_barang']) ?>" readonly>
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Merk / Identitas Unit</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= esc($pinjam['merk']) ?>" readonly>
                </div>
                <div class="col-md-3 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Kondisi Saat Ini</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= esc($pinjam['kondisi']) ?>" readonly>
                </div>
                <div class="col-md-3 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Tgl Permohonan</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= date('d/m/Y', strtotime($pinjam['tanggal'])) ?>" readonly>
                </div>
                <div class="col-md-6 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Nama Peminjam</label>
                  <input type="text" class="form-control form-control-sm bg-white font-weight-bold"
                    value="<?= esc($pinjam['nama_peminjam']) ?>" readonly>
                </div>
                <div class="col-md-12 form-group mb-0">
                  <label class="text-muted small font-weight-bold">Keterangan / Keperluan</label>
                  <textarea class="form-control form-control-sm bg-white" rows="2" readonly><?= esc($pinjam['keterangan']) ?></textarea>
                </div>
              </div>

              <!-- Field yang bisa diedit admin -->
              <div class="row">
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-primary">
                    <i class="mdi mdi-shield-check-outline"></i> Ubah Status Approval
                  </label>
                  <select class="form-control form-control-lg border-primary" name="status_approval" required>
                    <option value="Pending"   <?= $pinjam['status_approval'] == 'Pending'   ? 'selected' : '' ?>>⏳ Pending (Menunggu)</option>
                    <option value="Disetujui" <?= $pinjam['status_approval'] == 'Disetujui' ? 'selected' : '' ?>>✅ Disetujui (ACC)</option>
                    <option value="Ditolak"   <?= $pinjam['status_approval'] == 'Ditolak'   ? 'selected' : '' ?>>❌ Ditolak</option>
                  </select>
                </div>
                <div class="col-md-6 form-group">
                  <label class="font-weight-bold text-success">
                    <i class="mdi mdi-calendar-check-outline"></i> Tanggal Dikembalikan
                  </label>
                  <input type="date" class="form-control form-control-lg border-success"
                    name="tanggal_kembalikan"
                    value="<?= esc($pinjam['tanggal_kembalikan'] ?? '') ?>">
                  <small class="text-muted">* Isi jika barang sudah dikembalikan.</small>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-gradient-primary me-2 btn-lg">
                  <i class="mdi mdi-content-save"></i> Proses Approval
                </button>
                <a href="<?= base_url('admin/peminjaman') ?>" class="btn btn-light btn-lg">Kembali</a>
              </div>

            </form>
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
<script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>
</body>
</html>
