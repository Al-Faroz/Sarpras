<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              <span class="page-title-icon bg-gradient-info text-white me-2">
                <i class="mdi mdi-pencil"></i>
              </span> Edit Catatan Pemeliharaan
            </h3>
          </div>
          
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card shadow-sm">
                <div class="card-body">
                  <form class="forms-sample" action="<?= base_url('admin/pemeliharaan/update/' . $pemeliharaan['id_pemeliharaan']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Nama Pemeliharaan / Pekerjaan</label>
                          <input type="text" class="form-control form-control-lg" name="nama_pemeliharaan" value="<?= esc($pemeliharaan['nama_pemeliharaan']) ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Nomor Aset (Opsional)</label>
                          <input type="text" class="form-control form-control-lg" name="no_aset" value="<?= esc($pemeliharaan['no_aset']) ?>">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Tanggal Pelaksanaan</label>
                          <input type="date" class="form-control form-control-lg" name="tanggal" value="<?= $pemeliharaan['tanggal'] ?>" required>
                        </div>
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Biaya Perbaikan (Rp)</label>
                          <input type="number" class="form-control form-control-lg" name="biaya" value="<?= esc($pemeliharaan['biaya']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group">
                      <label class="font-weight-bold">Keterangan Detail Pekerjaan</label>
                      <textarea class="form-control form-control-lg" name="keterangan" rows="4" required><?= esc($pemeliharaan['keterangan']) ?></textarea>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-gradient-info me-2 btn-lg"><i class="mdi mdi-content-save"></i> Simpan Perubahan</button>
                        <a href="<?= base_url('admin/pemeliharaan') ?>" class="btn btn-light btn-lg">Batal</a>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
        
        <footer class="footer">
          <div class="container-fluid d-flex justify-content-between">
            <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Copyright © 2026 Portal Sarpras MTsN 4 Jombang.</span>
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