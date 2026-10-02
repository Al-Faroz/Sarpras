<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <div class="page-header">
            <h3 class="page-title">
              <span class="page-title-icon bg-gradient-danger text-white me-2">
                <i class="mdi mdi-wrench"></i>
              </span> Tindak Lanjut Laporan
            </h3>
          </div>
          
          <div class="row">
            <div class="col-12 grid-margin stretch-card">
              <div class="card shadow-sm">
                <div class="card-body">
                  <h4 class="card-title mb-4">Formulir Penanganan Kerusakan</h4>
                  
                  <form class="forms-sample" action="<?= base_url('admin/laporan/update/' . $laporan['id_laporan']) ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>

                    <h5 class="text-secondary border-bottom pb-2 mb-3">Informasi Aduan Masuk</h5>
                    <div class="row bg-light p-3 mb-4 rounded border mx-0">
                        <div class="col-md-4 form-group mb-2">
                          <label class="text-muted small font-weight-bold">Nama Pelapor</label>
                          <input type="text" class="form-control form-control-sm bg-white" value="<?= esc($laporan['nama_pelapor']) ?>" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                          <label class="text-muted small font-weight-bold">Unit Kerja / Ruangan</label>
                          <input type="text" class="form-control form-control-sm bg-white" value="<?= esc($laporan['unit_kerja']) ?>" readonly>
                        </div>
                        <div class="col-md-4 form-group mb-2">
                          <label class="text-muted small font-weight-bold">Kategori / Nama Barang</label>
                          <input type="text" class="form-control form-control-sm bg-white" value="<?= esc($laporan['nilai_kategori']) ?> - <?= esc($laporan['nama_barang']) ?>" readonly>
                        </div>
                        <div class="col-md-12 form-group mb-2">
                          <label class="text-muted small font-weight-bold">Detail Kerusakan Fisik</label>
                          <textarea class="form-control form-control-sm bg-white" rows="2" readonly><?= esc($laporan['keterangan_kerusakan']) ?></textarea>
                        </div>
                        <div class="col-md-12 form-group mb-0">
                          <label class="text-muted small font-weight-bold d-block">Foto Bukti Kerusakan Awal</label>
                          <?php if(!empty($laporan['foto_bukti_awal'])): ?>
                              <a href="<?= base_url('uploads/laporan/' . $laporan['foto_bukti_awal']) ?>" target="_blank" class="btn btn-sm btn-info">
                                  <i class="mdi mdi-eye"></i> Lihat Foto Awal
                              </a>
                          <?php else: ?>
                              <span class="text-danger small"><i>*Pelapor tidak melampirkan foto bukti.</i></span>
                          <?php endif; ?>
                        </div>
                    </div>

                    <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">Aksi Penanganan Petugas</h5>
                    
                    <div class="form-group">
                      <label class="font-weight-bold">Keterangan Tindak Lanjut Perbaikan</label>
                      <textarea class="form-control form-control-lg border-primary" name="tindakan" rows="4" placeholder="Contoh: Sudah diperbaiki dengan mengganti komponen X..." required><?= esc($laporan['tindakan']) ?></textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Status Penanganan</label>
                          <select class="form-control form-control-lg border-primary" name="status" required>
                            <option value="Masuk" <?= $laporan['status'] == 'Masuk' ? 'selected' : '' ?>>Masuk (Belum Diproses)</option>
                            <option value="Proses" <?= $laporan['status'] == 'Proses' ? 'selected' : '' ?>>Proses (Sedang Dikerjakan)</option>
                            <option value="Selesai" <?= $laporan['status'] == 'Selesai' ? 'selected' : '' ?>>Selesai (Sudah Diperbaiki)</option>
                          </select>
                        </div>
                        <div class="col-md-6 form-group">
                          <label class="font-weight-bold">Unggah Foto Bukti Hasil Akhir (Opsional)</label>
                          <input type="file" class="form-control form-control-lg" name="foto_bukti_akhir" accept="image/*">
                          <?php if(!empty($laporan['foto_bukti_akhir'])): ?>
                              <small class="text-success mt-1 d-block"><i class="mdi mdi-check-circle"></i> Sudah ada foto akhir yang diunggah.</small>
                          <?php endif; ?>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-gradient-danger me-2 btn-lg"><i class="mdi mdi-content-save"></i> Simpan Tindak Lanjut</button>
                        <a href="<?= base_url('admin/laporan') ?>" class="btn btn-light btn-lg">Kembali</a>
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