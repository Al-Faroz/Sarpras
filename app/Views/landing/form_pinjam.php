<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Formulir Peminjaman - Portal Sarpras MTsN 4 Jombang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');

    body {
      background: linear-gradient(135deg, #f0fdf4 0%, #e0f2fe 100%);
      min-height: 100vh;
      font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
    }

    .form-container {
      max-width: 680px;
      margin: 30px auto;
      background: #fff;
      padding: 35px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .header-form {
      text-align: center;
      border-bottom: 3px solid #198754;
      padding-bottom: 18px;
      margin-bottom: 28px;
    }

    /* ============================================================
       BLOK INFO ASET (auto-fill dari QR)
    ============================================================ */
    .aset-card {
      background: linear-gradient(135deg, #f0fdf4, #dcfce7);
      border: 2px solid #86efac;
      border-radius: 14px;
      padding: 18px 20px;
      margin-bottom: 24px;
    }

    .aset-card .aset-title {
      font-size: 0.72rem;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      color: #15803d;
      margin-bottom: 10px;
    }

    .aset-info-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 8px 16px;
    }

    .aset-info-item .lbl {
      font-size: 0.7rem;
      color: #6b7280;
      display: block;
    }

    .aset-info-item .val {
      font-size: 0.88rem;
      font-weight: 600;
      color: #111827;
    }

    .no-inv-badge {
      font-family: 'Courier New', monospace;
      font-size: 0.78rem;
      font-weight: 700;
      background: #166534;
      color: #fff;
      padding: 4px 10px;
      border-radius: 6px;
      word-break: break-all;
      display: inline-block;
      margin-top: 4px;
    }

    /* Kondisi badge */
    .badge-baik    { background:#d1fae5; color:#065f46; padding:3px 8px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .badge-rusak-r { background:#fef3c7; color:#92400e; padding:3px 8px; border-radius:20px; font-size:0.75rem; font-weight:600; }
    .badge-rusak-b { background:#fee2e2; color:#991b1b; padding:3px 8px; border-radius:20px; font-size:0.75rem; font-weight:600; }

    /* State tidak ada aset (form manual tidak tersedia) */
    .no-aset-notice {
      background: #fef9c3;
      border: 2px dashed #fbbf24;
      border-radius: 14px;
      padding: 20px;
      text-align: center;
      margin-bottom: 24px;
    }

    .btn-touch { min-height: 52px; font-weight: 700; font-size: 1rem; border-radius: 12px; }

    .form-control, .form-select {
      border-radius: 10px;
      font-size: 0.95rem;
    }
  </style>
</head>
<body>

<div class="container py-3">
  <div class="form-container">

    <!-- Header -->
    <div class="header-form">
      <i class="bi bi-bookmark-check text-success" style="font-size:3rem;"></i>
      <h3 class="fw-bold mt-2 mb-1">Formulir Peminjaman Aset</h3>
      <p class="text-muted small mb-0">
        Scan QR Code pada barang, lalu isi data di bawah ini untuk meminjam fasilitas.
      </p>
    </div>

    <!-- Flash error dari controller -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger d-flex align-items-start gap-2 rounded-3">
        <i class="bi bi-exclamation-triangle-fill mt-1 flex-shrink-0"></i>
        <div><?= $error ?></div>
      </div>
    <?php endif; ?>

    <!-- Validation errors -->
    <?php if (!empty($errors)): ?>
      <div class="alert alert-warning rounded-3">
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $e): ?>
            <li class="small"><?= esc($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if (!empty($aset)): ?>
      <!-- ============================================================
           STATE A: Aset terdeteksi dari QR Scan — tampilkan info & form
      ============================================================ -->

      <!-- Kartu info aset (read-only, auto-fill) -->
      <div class="aset-card">
        <div class="aset-title">
          <i class="bi bi-qr-code-scan me-1"></i> Aset Terdeteksi dari QR Code
        </div>

        <div class="aset-info-grid mb-2">
          <div class="aset-info-item">
            <span class="lbl">Jenis Barang</span>
            <span class="val"><?= esc($aset['jenis_barang']) ?></span>
          </div>
          <div class="aset-info-item">
            <span class="lbl">Merk / Unit</span>
            <span class="val"><?= esc($aset['merk']) ?></span>
          </div>
          <div class="aset-info-item">
            <span class="lbl">Kondisi</span>
            <span class="val">
              <?php if ($aset['kondisi'] == 'Baik'): ?>
                <span class="badge-baik">✓ Baik</span>
              <?php elseif ($aset['kondisi'] == 'Rusak Ringan'): ?>
                <span class="badge-rusak-r">⚠ Rusak Ringan</span>
              <?php else: ?>
                <span class="badge-rusak-b">✗ Rusak Berat</span>
              <?php endif; ?>
            </span>
          </div>
          <div class="aset-info-item">
            <span class="lbl">Lokasi</span>
            <span class="val"><?= esc($aset['lokasi']) ?></span>
          </div>
        </div>

        <div class="aset-info-item mt-1">
          <span class="lbl">No. Inventaris</span><br>
          <span class="no-inv-badge"><?= esc($aset['no_inv']) ?></span>
        </div>
      </div>

      <!-- Form peminjaman -->
      <form action="<?= base_url('landing/kirim_pinjam') ?>" method="POST">
        <?= csrf_field() ?>

        <!-- Hidden: id_inventaris dari QR scan -->
        <input type="hidden" name="id_inventaris" value="<?= esc($aset['id_inventaris']) ?>">

        <div class="mb-3">
          <label class="form-label fw-bold">
            Nama Lengkap Peminjam <span class="text-danger">*</span>
          </label>
          <input type="text" class="form-control form-control-lg" name="nama_peminjam"
            placeholder="Contoh: Bpk. Haryono, S.Pd (Guru Olahraga)"
            value="<?= esc($old['nama_peminjam'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
          <label class="form-label fw-bold">
            Tanggal Peminjaman <span class="text-danger">*</span>
          </label>
          <input type="date" class="form-control form-control-lg" name="tanggal"
            value="<?= esc($old['tanggal'] ?? date('Y-m-d')) ?>" required>
        </div>

        <div class="mb-4">
          <label class="form-label fw-bold">
            Keterangan / Keperluan <span class="text-danger">*</span>
          </label>
          <textarea class="form-control" name="keterangan" rows="4"
            placeholder="Contoh: Untuk presentasi materi IPA kelas 8B pada hari Senin pagi." required><?= esc($old['keterangan'] ?? '') ?></textarea>
          <small class="text-muted">Tuliskan keperluan secara spesifik agar mudah disetujui petugas.</small>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success btn-touch">
            <i class="bi bi-send-fill me-2"></i> Kirim Permohonan Pinjam
          </button>
          <a href="<?= base_url('/') ?>" class="btn btn-light btn-touch">
            <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
          </a>
        </div>
      </form>

    <?php else: ?>
      <!-- ============================================================
           STATE B: Belum scan QR — tampilkan instruksi & tombol scan
      ============================================================ -->

      <div class="no-aset-notice">
        <i class="bi bi-qr-code" style="font-size:2.5rem; color:#f59e0b;"></i>
        <h5 class="fw-bold mt-3 mb-2">Scan QR Code Terlebih Dahulu</h5>
        <p class="text-muted small mb-3">
          Formulir ini hanya dapat diisi setelah Anda men-scan QR Code yang menempel
          pada barang/fasilitas yang ingin dipinjam.
        </p>
        <p class="text-muted small mb-0">
          <i class="bi bi-info-circle me-1 text-info"></i>
          Scan QR Code → sistem otomatis membuka form ini dengan data aset terisi.
        </p>
      </div>

      <div class="d-grid gap-2">
        <a href="<?= base_url('scanner') ?>" class="btn btn-success btn-touch">
          <i class="bi bi-qr-code-scan me-2"></i> Buka Kamera & Scan QR Code
        </a>
        <a href="<?= base_url('/') ?>" class="btn btn-light btn-touch">
          <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
        </a>
      </div>

    <?php endif; ?>

  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
