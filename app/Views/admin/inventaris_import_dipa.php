<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-file-import"></i>
        </span> Import Inventaris Dana DIPA
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris') ?>">Data Inventaris</a></li>
          <li class="breadcrumb-item active">Import DIPA</li>
        </ul>
      </nav>
    </div>

    <?php if (session()->getFlashdata('error')): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <i class="mdi mdi-alert-circle me-2"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    <?php endif; ?>

    <div class="row">

      <!-- Panel Utama: Upload -->
      <div class="col-lg-7 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-1">Upload File Excel Data DIPA</h4>
            <p class="text-muted mb-4">
              Upload file Excel hasil export dari aplikasi SAKTI/SIMAK BMN pemerintah yang sudah disesuaikan
              dengan format template sistem ini.
            </p>

            <form action="<?= base_url('admin/inventaris/proses_import_dipa') ?>"
                  method="POST" enctype="multipart/form-data" id="formImport">
              <?= csrf_field() ?>

              <div class="form-group">
                <label class="font-weight-bold">
                  File Excel (.xlsx / .xls) <span class="text-danger">*</span>
                </label>
                <div class="input-group">
                  <input type="file" class="form-control form-control-lg"
                         name="file_excel" id="fileExcel"
                         accept=".xlsx,.xls" required>
                </div>
                <small class="text-muted">Maksimal ukuran file: sesuai konfigurasi server (default 2MB).</small>
              </div>

              <div class="alert alert-info py-2 mb-4">
                <i class="mdi mdi-information-outline me-1"></i>
                <strong>Catatan:</strong> Sistem akan <strong>melewati (skip)</strong> baris yang nomor inventarisnya
                sudah terdaftar, sehingga aman dijalankan berulang kali.
              </div>

              <button type="submit" class="btn btn-gradient-primary btn-lg me-2" id="btnImport">
                <i class="mdi mdi-upload me-1"></i> Mulai Import
              </button>
              <a href="<?= base_url('admin/inventaris') ?>" class="btn btn-light btn-lg">Batal</a>
            </form>
          </div>
        </div>
      </div>

      <!-- Panel Kanan: Panduan + Download Template -->
      <div class="col-lg-5 grid-margin stretch-card">
        <div class="card shadow-sm border-primary" style="border-left: 4px solid #1E3A5F !important;">
          <div class="card-body">

            <h5 class="card-title text-primary mb-3">
              <i class="mdi mdi-clipboard-list-outline me-1"></i> Panduan Format File
            </h5>

            <p class="mb-2">File Excel harus memiliki <strong>baris header di baris pertama</strong> dengan nama kolom persis:</p>

            <div class="table-responsive mb-3">
              <table class="table table-sm table-bordered mb-0" style="font-size:0.85rem;">
                <thead class="table-primary">
                  <tr>
                    <th>Nama Kolom</th>
                    <th>Keterangan</th>
                    <th>Wajib?</th>
                  </tr>
                </thead>
                <tbody>
                  <tr><td><code>no_inv</code></td>  <td>No. inventaris resmi dari pemerintah</td><td class="text-center text-danger">✔</td></tr>
                  <tr><td><code>kode</code></td>    <td>Kode SAKTI (lihat sheet Referensi)</td>  <td class="text-center text-danger">✔</td></tr>
                  <tr><td><code>merk</code></td>    <td>Merk / identitas unit fisik</td>         <td class="text-center text-danger">✔</td></tr>
                  <tr><td><code>tahun</code></td>   <td>Tahun pengadaan (mis: 2023, pra2016)</td><td class="text-center text-danger">✔</td></tr>
                  <tr><td><code>kondisi</code></td> <td>Baik / Rusak Ringan / Rusak Berat</td>   <td class="text-center text-muted">Opsional</td></tr>
                  <tr><td><code>lokasi</code></td>  <td>Statis / Mobile</td>                     <td class="text-center text-muted">Opsional</td></tr>
                </tbody>
              </table>
            </div>

            <p class="text-muted small mb-3">
              Kolom <code>kondisi</code> dan <code>lokasi</code> akan diisi otomatis
              (<em>Baik</em> dan <em>Statis</em>) jika dikosongkan.
            </p>

            <a href="<?= base_url('admin/inventaris/template_dipa') ?>"
               class="btn btn-outline-primary btn-sm w-100">
              <i class="mdi mdi-file-download-outline me-1"></i>
              Download Template Excel (.xlsx)
            </a>

          </div>
        </div>
      </div>

    </div>
  </div>

  <footer class="footer">
    <div class="container-fluid">
      <span class="text-muted">Copyright &copy; <?= date('Y') ?> Portal Sarpras MTsN 4 Jombang.</span>
    </div>
  </footer>
</div>
</div>
</div>

<script src="<?= base_url('assets/purpleadmin/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>
<script>
// Disable tombol setelah submit supaya tidak double-post
document.getElementById('formImport').addEventListener('submit', function () {
  var btn = document.getElementById('btnImport');
  btn.disabled = true;
  btn.innerHTML = '<i class="mdi mdi-loading mdi-spin me-1"></i> Memproses...';
});
</script>
</body>
</html>
