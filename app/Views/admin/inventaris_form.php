<?= $this->include('layout/admin_header') ?>
<style>
/* Fix Select2 tidak tumpuk dengan keterangan di bawahnya */
.select2-container { 
  width: 100% !important; 
  display: block;
}
.select2-container .select2-selection--single {
  height: 48px !important;
  line-height: 46px;
  border-radius: 4px;
  border: 1px solid #ced4da;
}
.select2-container .select2-selection--single .select2-selection__rendered {
  line-height: 46px;
  padding-left: 12px;
  font-size: 1rem;
}
.select2-container .select2-selection--single .select2-selection__arrow {
  height: 46px;
}
/* Pastikan teks keterangan di bawah select tidak tertutup */
.form-group {
  position: relative;
  z-index: auto;
}
.form-group:has(#selectKode) {
  z-index: 1050;
}
/* Dropdown Select2 muncul di atas element lain */
.select2-dropdown {
  z-index: 9999 !important;
}
</style>

<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
  <div class="content-wrapper">

    <div class="page-header">
      <h3 class="page-title">
        <span class="page-title-icon bg-gradient-success text-white me-2">
          <i class="mdi mdi-plus"></i>
        </span> Tambah Aset Baru
      </h3>
      <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
          <li class="breadcrumb-item"><a href="<?= base_url('admin/inventaris') ?>">Data Inventaris</a></li>
          <li class="breadcrumb-item active">Tambah Data</li>
        </ul>
      </nav>
    </div>

    <div class="row">
      <div class="col-12 grid-margin stretch-card">
        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-4">Formulir Pendaftaran Aset Sarpras</h4>

            <?php if (!empty($errors)): ?>
              <div class="alert alert-danger">
                <ul class="mb-0">
                  <?php foreach ($errors as $e): ?><li><?= esc($e) ?></li><?php endforeach; ?>
                </ul>
              </div>
            <?php endif; ?>

            <form action="<?= base_url('admin/inventaris/simpan') ?>" method="POST" id="formTambah">
              <?= csrf_field() ?>

              <!-- BLOK 1: KODE BARANG -->
              <div class="form-group">
                <label class="font-weight-bold">
                  Kode &amp; Jenis Barang (SAKTI) <span class="text-danger">*</span>
                </label>
                <select class="form-control form-control-lg" name="kode" id="selectKode" required style="width:100%">
                  <?php if (!empty($old['kode'])): ?>
                    <?php foreach ($kode_sakti as $ks): ?>
                      <?php if ($ks['kode'] === $old['kode']): ?>
                        <option value="<?= esc($ks['kode']) ?>" selected>
                          <?= esc($ks['kode']) ?> — <?= esc($ks['jenis_barang']) ?>
                        </option>
                      <?php endif; ?>
                    <?php endforeach; ?>
                  <?php endif; ?>
                </select>
                <small class="text-muted">Ketik minimal 2 huruf untuk mencari kode atau nama jenis barang.</small>
              </div>

              <!-- BLOK 2: MERK -->
              <div class="form-group">
                <label class="font-weight-bold">
                  Merk / Identitas Unit <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control form-control-lg" name="merk"
                  placeholder="Contoh: ASUS ExpertBook B1400, Daikin FTKC25NVM4, Chitose Futura..."
                  value="<?= esc($old['merk'] ?? '') ?>" required autocomplete="off">
                <small class="text-muted">Isi dengan merk dan tipe spesifik unit ini sebagai pembeda antar unit sejenis.</small>
              </div>

              <!-- BLOK 3: SUMBER DANA + TAHUN + LOKASI + KONDISI -->
              <div class="row">
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Sumber Dana <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="kode_dana" id="selectDana" required>
                      <option value="">-- Pilih Dana --</option>
                      <?php foreach ($sumber_dana as $kode => $label): ?>
                        <option value="<?= $kode ?>" <?= (($old['kode_dana'] ?? '') === $kode) ? 'selected' : '' ?>>
                          <?= esc($label) ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
                <div class="col-md-3">
                  <div class="form-group">
                    <label class="font-weight-bold">Tahun Pengadaan <span class="text-danger">*</span></label>
                    <select class="form-control form-control-lg" name="tahun" id="selectTahun" required>
                      <option value="">-- Pilih Tahun --</option>
                      <?php foreach ($daftar_tahun as $t): ?>
                        <option value="<?= $t ?>" <?= (($old['tahun'] ?? '') === (string)$t) ? 'selected' : '' ?>>
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
                        <option value="<?= $k ?>" <?= (($old['kondisi'] ?? 'Baik') === $k) ? 'selected' : '' ?>>
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
                      <option value="Statis" <?= (($old['lokasi'] ?? '') === 'Statis') ? 'selected' : '' ?>>Statis (Tetap)</option>
                      <option value="Mobile" <?= (($old['lokasi'] ?? '') === 'Mobile') ? 'selected' : '' ?>>Mobile (Dipindah)</option>
                    </select>
                  </div>
                </div>
              </div>

              <!-- BLOK 4: NO. INVENTARIS — conditional DIPA vs Non-DIPA -->

              <!-- Panel DIPA: input manual -->
              <div id="panelDipa" class="form-group mt-2" style="display:none;">
                <label class="font-weight-bold text-primary">
                  <i class="mdi mdi-file-document-edit-outline me-1"></i>
                  No. Inventaris DIPA (dari aplikasi pemerintah) <span class="text-danger">*</span>
                </label>
                <input type="text" class="form-control form-control-lg" name="no_inv_dipa"
                  id="inputNoInvDipa"
                  placeholder="Contoh: 3.10.01.02.002.A.2023.0001"
                  value="<?= esc($old['no_inv_dipa'] ?? '') ?>"
                  autocomplete="off">
                <small class="text-muted">
                  Isi sesuai nomor inventaris resmi yang tercatat di aplikasi SAKTI/SIMAK BMN pemerintah.
                  Sistem tidak akan menimpa nomor ini.
                </small>
              </div>

              <!-- Panel Non-DIPA: preview auto-generate -->
              <div id="panelNonDipa" class="form-group mt-2">
                <label class="font-weight-bold text-muted small">Preview No. Inventaris (Auto-generate)</label>
                <div class="p-3 rounded border bg-light d-flex align-items-center gap-2">
                  <i class="mdi mdi-barcode-scan text-primary mdi-24px"></i>
                  <code id="previewNoInv" class="text-dark" style="font-size:1rem; letter-spacing:0.5px;">
                    — Pilih kode, dana, dan tahun untuk melihat preview —
                  </code>
                  <small id="previewSpinner" class="text-muted ms-2" style="display:none;">
                    <i class="mdi mdi-loading mdi-spin"></i>
                  </small>
                </div>
                <small class="text-muted">Nomor urut akhir (0001, 0002, dst) ditentukan otomatis saat data disimpan.</small>
              </div>

              <div class="mt-4">
                <button type="submit" class="btn btn-gradient-primary me-2 btn-lg">
                  <i class="mdi mdi-content-save"></i> Simpan Data
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
      <span class="text-muted">Copyright &copy; <?= date('Y') ?> Portal Sarpras MTsN 4 Jombang.</span>
    </div>
  </footer>
</div>
</div>
</div>

<link rel="stylesheet" href="<?= base_url('assets/purpleadmin/vendors/select2/select2.min.css') ?>">
<script src="<?= base_url('assets/purpleadmin/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/vendors/select2/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>

<script>
$(document).ready(function () {

  // ── Select2 kode SAKTI ──
  $('#selectKode').select2({
    placeholder: 'Ketik untuk mencari kode atau nama jenis barang...',
    minimumInputLength: 2,
    allowClear: true,
    ajax: {
      url: '<?= base_url('admin/inventaris/ajax_kode') ?>',
      dataType: 'json',
      delay: 300,
      data: function (params) { return { q: params.term }; },
      processResults: function (data) { return { results: data }; },
      cache: true
    },
    templateResult: function (item) {
      if (item.loading) return item.text;
      return $('<span><code class="me-2 text-primary small">' + item.id + '</code> ' + item.text.split(' — ')[1] + '</span>');
    },
    templateSelection: function (item) { return item.text || item.id; }
  });

  // ── Toggle panel DIPA vs Non-DIPA ──
  function togglePanelDana() {
    var dana = $('#selectDana').val();
    if (dana === 'A') {
      $('#panelDipa').show();
      $('#inputNoInvDipa').prop('required', true);
      $('#panelNonDipa').hide();
    } else {
      $('#panelDipa').hide();
      $('#inputNoInvDipa').prop('required', false).val('');
      $('#panelNonDipa').show();
    }
  }

  // Jalankan saat halaman load (untuk kasus old input / validation error)
  togglePanelDana();
  $('#selectDana').on('change', function () {
    togglePanelDana();
    updatePreview();
  });

  // ── Preview No. Inventaris (hanya untuk non-DIPA) ──
  var previewTimer = null;

  function updatePreview() {
    var kode  = $('#selectKode').val();
    var dana  = $('#selectDana').val();
    var tahun = $('#selectTahun').val();

    if (dana === 'A') return; // DIPA: tidak ada preview

    if (!kode || !dana || !tahun) {
      $('#previewNoInv').text('— Pilih kode, dana, dan tahun untuk melihat preview —');
      return;
    }

    clearTimeout(previewTimer);
    previewTimer = setTimeout(function () {
      $('#previewSpinner').show();
      $.getJSON('<?= base_url('admin/inventaris/ajax_preview_no_inv') ?>', {
        kode: kode, dana: dana, tahun: tahun
      }, function (res) {
        $('#previewNoInv').text(res.no_inv);
      }).always(function () {
        $('#previewSpinner').hide();
      });
    }, 400);
  }

  $('#selectKode').on('change', updatePreview);
  $('#selectTahun').on('change', updatePreview);
});
</script>

</body>
</html>
