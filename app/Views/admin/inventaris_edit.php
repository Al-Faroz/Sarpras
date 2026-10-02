<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-info text-white me-2">
          <i class="mdi mdi-pencil"></i>
        </span> Edit Data Aset
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris') ?>">Data Inventaris</a></li>
          <li class="breadcrumb-item active">Edit Data</li>
        </ul>
      </nav>
    </div>

    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-1">Perbarui Data Aset Sarpras</h4>
            <p class="text-muted small mb-4">
              <i class="mdi mdi-information-outline text-info"></i>
              Kode SAKTI dan nomor inventaris dikunci setelah data dibuat. Yang dapat diubah: merk, sumber dana, tahun, kondisi, dan lokasi.
            </p>

            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/inventaris/update/' . $barang['id_inventaris']) ?>" method="POST">
              <?= csrf_field() ?>

              <!-- ============================================================ -->
              <!-- BLOK INFO TETAP (Read-only)                                   -->
              <!-- ============================================================ -->
              <div class="row bg-light p-3 mb-4 rounded border">
                <div class="col-md-4 form-group mb-2">
                  <label class="text-muted small font-weight-bold">No. Inventaris (Dikunci)</label>
                  <input type="text" class="form-control form-control-sm bg-white font-weight-bold"
                    value="<?= esc($barang['no_inv']) ?>" readonly>
                </div>
                <div class="col-md-4 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Kode SAKTI (Dikunci)</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= esc($barang['kode']) ?>" readonly>
                </div>
                <div class="col-md-4 form-group mb-2">
                  <label class="text-muted small font-weight-bold">Jenis Barang (Dikunci)</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= esc($barang['jenis_barang']) ?>" readonly>
                </div>
                <div class="col-md-3 form-group mb-0">
                  <label class="text-muted small font-weight-bold">No. Urut</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= str_pad($barang['no_urut'], 4, '0', STR_PAD_LEFT) ?>" readonly>
                </div>
                <div class="col-md-3 form-group mb-0">
                  <label class="text-muted small font-weight-bold">Tanggal Dibuat</label>
                  <input type="text" class="form-control form-control-sm bg-white"
                    value="<?= date('d/m/Y H:i', strtotime($barang['created_at'])) ?>" readonly>
                </div>
              </div>

              <!-- ============================================================ -->
              <!-- BLOK EDIT                                                      -->
              <!-- ============================================================ -->
              <div class="form-group">
                <label class="font-weight-bold">Merk / Identitas Unit <span class="text-danger">*</span></label>
                <input type="text" class="form-control form-control-lg" name="merk"
                  value="<?= esc($barang['merk']) ?>" required autocomplete="off"
                  placeholder="Contoh: ASUS ExpertBook B1400">
              </div>

              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Sumber Dana <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="kode_dana" required>
                      <?php foreach ($sumber_dana as $kode => $label): ?>
                        <option value="<?= $kode ?>" <?= $barang['kode_dana'] === $kode ? 'selected' : '' ?>>
                          <?= esc($label) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="tahun" required>
                      <?php foreach ($daftar_tahun as $t): ?>
                        <option value="<?= $t ?>" <?= (string)$barang['tahun'] === (string)$t ? 'selected' : '' ?>>
                          <?= $t === 'pra2016' ? '< 2016 (Aset Lama)' : $t ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Kondisi Fisik <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="kondisi" required>
                      <?php foreach (['Baik', 'Rusak Ringan', 'Rusak Berat'] as $k): ?>
                        <option value="<?= $k ?>" <?= $barang['kondisi'] === $k ? 'selected' : '' ?>>
                          <?= $k ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Lokasi Aset <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="lokasi" required>
                      <option value="Statis" <?= $barang['lokasi'] === 'Statis' ? 'selected' : '' ?>>Statis (Tetap)</option>
                      <option value="Mobile" <?= $barang['lokasi'] === 'Mobile' ? 'selected' : '' ?>>Mobile (Dipindah)</option>
                    </select>
                  </div>
                </div>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-gradient-info me-2 btn-lg">
                  <i class="mdi mdi-content-save"></i> Simpan Perubahan
                </button>
                <a href="<?= base_url('admin/inventaris') ?>" class="btn btn-light btn-lg">Batal</a>
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
