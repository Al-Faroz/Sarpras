<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <!-- Page Header -->
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-primary text-white me-2">
          <i class="mdi mdi-cube-outline"></i>
        </span> Data Master Inventaris
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item active">
            <a href="<?= base_url('admin/inventaris/tambah') ?>" class="btn btn-gradient-success btn-icon-text btn-sm">
              <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah Aset Baru
            </a>
          </li>
        </ul>
      </nav>
      <div class="btn-group">
        <a href="<?= base_url('export/inventaris_pdf') ?>" class="btn btn-danger btn-sm" target="_blank">
          <i class="mdi mdi-file-pdf"></i> PDF
        </a>
        <a href="<?= base_url('export/inventaris_excel') ?>" class="btn btn-success btn-sm">
          <i class="mdi mdi-file-excel"></i> Excel
        </a>
      </div>
    </div>

    <div class="row">
      <div class="col-lg-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h4 class="card-title mb-0">Daftar Aset & Fasilitas (Sarpras)</h4>
              <!-- Search Bar -->
              <form method="GET" action="<?= base_url('admin/inventaris') ?>" class="d-flex" style="min-width:320px;">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="search"
                    placeholder="Cari no. inventaris, jenis, merk, kondisi..."
                    value="<?= esc($keyword) ?>">
                  <button class="btn btn-gradient-primary btn-sm px-3" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/inventaris') ?>" class="btn btn-outline-secondary btn-sm px-2" title="Reset">
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
                <i class="mdi mdi-filter-outline"></i>
                Menampilkan hasil pencarian untuk: <strong>"<?= esc($keyword) ?>"</strong>
                — <?= count($inventaris) ?> data ditemukan.
              </p>
            <?php endif; ?>

            <div class="table-responsive">
              <table class="table table-hover table-striped align-middle">
                <thead class="table-dark">
                  <tr>
                    <th width="4%">No</th>
                    <th>No. Inventaris</th>
                    <th>Jenis Barang</th>
                    <th>Merk / Identitas</th>
                    <th>Sumber Dana</th>
                    <th>Tahun</th>
                    <th>Kondisi</th>
                    <th>Lokasi</th>
                    <th width="11%">Aksi</th>
                  </tr>
                </thead>
                <tbody>
                  <?php if (empty($inventaris)): ?>
                    <tr>
                      <td colspan="9" class="text-center py-4 text-muted">
                        <i class="mdi mdi-inbox-arrow-down mdi-36px d-block mb-2"></i>
                        <?= $keyword ? 'Tidak ada data yang cocok dengan pencarian.' : 'Belum ada data inventaris.' ?>
                      </td>
                    </tr>
                  <?php else: ?>
                    <?php $no = 1; foreach ($inventaris as $row): ?>
                      <tr>
                        <td><?= $no++ ?></td>
                        <td>
                          <code class="small"><?= esc($row['no_inv']) ?></code>
                        </td>
                        <td class="font-weight-bold"><?= esc($row['jenis_barang']) ?></td>
                        <td><?= esc($row['merk']) ?></td>
                        <td>
                          <span class="badge bg-info text-white small">
                            <?= esc(\App\Models\InventarisModel::getLabelDana($row['kode_dana'])) ?>
                          </span>
                        </td>
                        <td><?= esc($row['tahun']) ?></td>
                        <td>
                          <?php if ($row['kondisi'] == 'Baik'): ?>
                            <span class="badge badge-success">Baik</span>
                          <?php elseif ($row['kondisi'] == 'Rusak Ringan'): ?>
                            <span class="badge badge-warning text-dark">Rusak Ringan</span>
                          <?php else: ?>
                            <span class="badge badge-danger">Rusak Berat</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <small class="text-muted">
                            <i class="mdi <?= $row['lokasi'] == 'Mobile' ? 'mdi-truck-outline' : 'mdi-map-marker' ?>"></i>
                            <?= esc($row['lokasi']) ?>
                          </small>
                        </td>
                        <td>
                          <a href="<?= base_url('admin/inventaris/cetak_qr/' . $row['id_inventaris']) ?>"
                            target="_blank" class="btn btn-inverse-dark btn-sm btn-icon" title="Cetak Label QR">
                            <i class="mdi mdi-qrcode"></i>
                          </a>
                          <a href="<?= base_url('admin/inventaris/edit/' . $row['id_inventaris']) ?>"
                            class="btn btn-inverse-info btn-sm btn-icon" title="Edit">
                            <i class="mdi mdi-pencil"></i>
                          </a>
                          <a href="<?= base_url('admin/inventaris/hapus/' . $row['id_inventaris']) ?>"
                            class="btn btn-inverse-danger btn-sm btn-icon" title="Hapus"
                            onclick="return confirm('Hapus aset [<?= esc($row['merk']) ?> - <?= esc($row['no_inv']) ?>] secara permanen?')">
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
    <div class="container-fluid d-flex justify-content-between">
      <span class="text-muted d-block text-center text-sm-start d-sm-inline-block">Copyright © 2026 Portal Sarpras MTsN 4 Jombang.</span>
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
