<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <!-- Page Header -->
    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-dark text-white me-2">
          <i class="mdi mdi-qrcode-plus"></i>
        </span> Cetak Label QR Massal
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris') ?>">Data Inventaris</a></li>
          <li class="breadcrumb-item active">Cetak QR Massal</li>
        </ul>
      </nav>
    </div>

    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">

            <!-- Info + Search Bar -->
            <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
              <div>
                <h4 class="card-title mb-0">Pilih Aset yang Akan Dicetak</h4>
                <small class="text-muted">
                  <i class="mdi mdi-information-outline text-info"></i>
                  Centang aset yang diinginkan, lalu klik tombol <strong>Cetak QR Terpilih</strong>.
                  Maks <strong>63 label</strong> (3 halaman A4) per satu kali cetak.
                </small>
              </div>
              <!-- Search -->
              <form method="GET" action="<?= base_url('admin/inventaris/print_qr_batch') ?>"
                    class="d-flex" style="min-width:300px;">
                <div class="input-group">
                  <input type="text" class="form-control form-control-sm" name="search"
                    placeholder="Cari no. inventaris, jenis, merk..."
                    value="<?= esc($keyword) ?>">
                  <button class="btn btn-gradient-dark btn-sm px-3" type="submit">
                    <i class="mdi mdi-magnify"></i>
                  </button>
                  <?php if ($keyword): ?>
                    <a href="<?= base_url('admin/inventaris/print_qr_batch') ?>"
                       class="btn btn-outline-secondary btn-sm px-2" title="Reset">
                      <i class="mdi mdi-close"></i>
                    </a>
                  <?php endif; ?>
                </div>
              </form>
            </div>

            <!-- Flash messages -->
            <?php if (session()->getFlashdata('error')): ?>
              <div class="alert alert-danger alert-dismissible fade show">
                <?= session()->getFlashdata('error') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
              </div>
            <?php endif; ?>

            <?php if ($keyword): ?>
              <p class="text-muted small mb-2">
                Hasil: <strong>"<?= esc($keyword) ?>"</strong> — <?= count($inventaris) ?> data.
              </p>
            <?php endif; ?>

            <!-- Form POST ke proses_print_qr -->
            <form action="<?= base_url('admin/inventaris/proses_print_qr') ?>"
                  method="POST" id="formBatch" target="_blank">
              <?= csrf_field() ?>

              <!-- Toolbar atas tabel: Pilih semua + Counter + Tombol cetak -->
              <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                  <div class="form-check mb-0">
                    <input class="form-check-input" type="checkbox" id="checkAll">
                    <label class="form-check-label fw-bold" for="checkAll">
                      Pilih Semua
                    </label>
                  </div>
                  <span class="badge bg-primary px-3 py-2" id="counterBadge">
                    0 dipilih
                  </span>
                </div>
                <button type="submit" class="btn btn-gradient-dark btn-sm px-4"
                        id="btnCetak" disabled>
                  <i class="mdi mdi-printer me-1"></i> Cetak QR Terpilih
                </button>
              </div>

              <!-- Tabel aset -->
              <div class="table-responsive">
                <table class="table table-hover table-striped align-middle">
                  <thead class="table-dark">
                    <tr>
                      <th width="4%" class="text-center">✓</th>
                      <th width="5%">No</th>
                      <th>No. Inventaris</th>
                      <th>Jenis Barang</th>
                      <th>Merk / Identitas</th>
                      <th>Kondisi</th>
                      <th>Tahun</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php if (empty($inventaris)): ?>
                      <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                          <i class="mdi mdi-inbox-arrow-down mdi-36px d-block mb-2"></i>
                          <?= $keyword ? 'Tidak ada data yang cocok.' : 'Belum ada data inventaris.' ?>
                        </td>
                      </tr>
                    <?php else: ?>
                      <?php $no = 1; foreach ($inventaris as $row): ?>
                        <tr class="row-item" onclick="toggleRow(this)" style="cursor:pointer;">
                          <td class="text-center" onclick="event.stopPropagation();">
                            <input class="form-check-input item-check"
                              type="checkbox" name="id_inventaris[]"
                              value="<?= $row['id_inventaris'] ?>"
                              onclick="updateCounter()">
                          </td>
                          <td><?= $no++ ?></td>
                          <td>
                            <code class="small"><?= esc($row['no_inv']) ?></code>
                          </td>
                          <td class="fw-bold"><?= esc($row['jenis_barang']) ?></td>
                          <td><?= esc($row['merk']) ?></td>
                          <td>
                            <?php if ($row['kondisi'] == 'Baik'): ?>
                              <span class="badge badge-success">Baik</span>
                            <?php elseif ($row['kondisi'] == 'Rusak Ringan'): ?>
                              <span class="badge badge-warning text-dark">Rusak Ringan</span>
                            <?php else: ?>
                              <span class="badge badge-danger">Rusak Berat</span>
                            <?php endif; ?>
                          </td>
                          <td><?= esc($row['tahun']) ?></td>
                        </tr>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </tbody>
                </table>
              </div>

              <!-- Toolbar bawah -->
              <?php if (!empty($inventaris)): ?>
              <div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
                <span class="text-muted small">
                  Total tersedia: <strong><?= count($inventaris) ?></strong> aset
                </span>
                <button type="submit" class="btn btn-gradient-dark px-5"
                        id="btnCetakBawah" disabled>
                  <i class="mdi mdi-printer me-2"></i> Cetak QR Terpilih
                  <span id="counterBtn" class="badge bg-light text-dark ms-1">0</span>
                </button>
              </div>
              <?php endif; ?>

            </form>
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

<script>
const MAX_CETAK = 63;

// Klik baris → toggle checkbox
function toggleRow(row) {
  const cb = row.querySelector('.item-check');
  if (!cb) return;

  const checked = cb.checked;
  const total   = document.querySelectorAll('.item-check:checked').length;

  // Cegah lebih dari MAX_CETAK
  if (!checked && total >= MAX_CETAK) {
    alert('Maksimal ' + MAX_CETAK + ' label (3 halaman A4) per sekali cetak.');
    return;
  }

  cb.checked = !cb.checked;
  row.classList.toggle('table-active', cb.checked);
  updateCounter();
}

// Update semua counter dan state tombol
function updateCounter() {
  const checked = document.querySelectorAll('.item-check:checked').length;

  document.getElementById('counterBadge').textContent = checked + ' dipilih';
  document.getElementById('counterBtn').textContent   = checked;

  const enable = checked > 0;
  document.getElementById('btnCetak').disabled     = !enable;
  document.getElementById('btnCetakBawah').disabled = !enable;

  // Sync visual baris aktif
  document.querySelectorAll('.item-check').forEach(cb => {
    cb.closest('tr').classList.toggle('table-active', cb.checked);
  });

  // Sync checkbox "Pilih Semua"
  const allChecks = document.querySelectorAll('.item-check');
  document.getElementById('checkAll').checked =
    allChecks.length > 0 && checked === allChecks.length;
  document.getElementById('checkAll').indeterminate =
    checked > 0 && checked < allChecks.length;
}

// Pilih Semua / Batal Semua
document.getElementById('checkAll').addEventListener('change', function () {
  const checks = document.querySelectorAll('.item-check');
  let count = 0;

  checks.forEach(cb => {
    if (this.checked && count < MAX_CETAK) {
      cb.checked = true;
      count++;
    } else if (!this.checked) {
      cb.checked = false;
    }
  });

  if (this.checked && checks.length > MAX_CETAK) {
    alert('Hanya ' + MAX_CETAK + ' aset pertama yang dipilih (batas maks per cetak).');
  }

  updateCounter();
});

// Validasi sebelum submit
document.getElementById('formBatch').addEventListener('submit', function (e) {
  const checked = document.querySelectorAll('.item-check:checked').length;
  if (checked === 0) {
    e.preventDefault();
    alert('Pilih minimal 1 aset untuk dicetak!');
    return;
  }
  // Buka di tab baru sudah dihandle oleh target="_blank" di form
});
</script>

</body>
</html>
