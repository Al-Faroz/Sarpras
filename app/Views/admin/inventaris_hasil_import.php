<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-info text-white me-2">
          <i class="mdi mdi-clipboard-check-outline"></i>
        </span> Hasil Import DIPA
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris') ?>">Data Inventaris</a></li>
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris/import_dipa') ?>">Import DIPA</a></li>
          <li class="breadcrumb-item active">Hasil Import</li>
        </ul>
      </nav>
    </div>

    <!-- Ringkasan -->
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card bg-gradient-success text-white shadow-sm">
          <div class="card-body text-center py-3">
            <h2 class="mb-0 font-weight-bold"><?= $summary['ok'] ?></h2>
            <p class="mb-0 small">Berhasil Diimpor</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-gradient-warning text-white shadow-sm">
          <div class="card-body text-center py-3">
            <h2 class="mb-0 font-weight-bold"><?= $summary['skip'] ?></h2>
            <p class="mb-0 small">Dilewati (Duplikat)</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-gradient-danger text-white shadow-sm">
          <div class="card-body text-center py-3">
            <h2 class="mb-0 font-weight-bold"><?= $summary['error'] ?></h2>
            <p class="mb-0 small">Gagal / Error</p>
          </div>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card bg-gradient-dark text-white shadow-sm">
          <div class="card-body text-center py-3">
            <h2 class="mb-0 font-weight-bold"><?= $summary['total'] ?></h2>
            <p class="mb-0 small">Total Baris Diproses</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tabel detail -->
    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-3">Detail Per Baris</h4>
            <div class="table-responsive">
              <table class="table table-hover table-sm align-middle">
                <thead class="table-dark">
                  <tr>
                    <th width="70">Baris Excel</th>
                    <th>No. Inventaris</th>
                    <th width="100">Status</th>
                    <th>Keterangan</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($hasil as $row): ?>
                    <tr>
                      <td class="text-center text-muted"><?= $row['baris'] ?></td>
                      <td><code><?= esc($row['no_inv']) ?></code></td>
                      <td class="text-center">
                        <?php if ($row['status'] === 'ok'): ?>
                          <span class="badge bg-success">Berhasil</span>
                        <?php elseif ($row['status'] === 'skip'): ?>
                          <span class="badge bg-warning text-dark">Dilewati</span>
                        <?php else: ?>
                          <span class="badge bg-danger">Error</span>
                        <?php endif; ?>
                      </td>
                      <td class="<?= $row['status'] === 'error' ? 'text-danger' : 'text-muted' ?> small">
                        <?= esc($row['pesan']) ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>

            <div class="mt-4 d-flex gap-2">
              <a href="<?= base_url('admin/inventaris') ?>" class="btn btn-gradient-primary">
                <i class="mdi mdi-format-list-bulleted me-1"></i> Lihat Data Inventaris
              </a>
              <a href="<?= base_url('admin/inventaris/import_dipa') ?>" class="btn btn-outline-primary">
                <i class="mdi mdi-file-import me-1"></i> Import Lagi
              </a>
            </div>

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
</body>
</html>
