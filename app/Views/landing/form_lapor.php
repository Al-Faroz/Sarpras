<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Laporan Kerusakan - Portal Sarpras MTsN 4 Jombang</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap');

    body {
      background: linear-gradient(135deg, #fef9c3 0%, #fde8d8 100%);
      min-height: 100vh;
      font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
    }

    .form-container {
      max-width: 700px;
      margin: 30px auto;
      background: #fff;
      padding: 35px;
      border-radius: 20px;
      box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .header-form {
      text-align: center;
      border-bottom: 3px solid #ef4444;
      padding-bottom: 18px;
      margin-bottom: 28px;
    }

    .form-control, .form-select {
      border-radius: 10px;
      font-size: 0.95rem;
    }

    .btn-touch { min-height: 52px; font-weight: 700; font-size: 1rem; border-radius: 12px; }

    .foto-preview {
      display: none;
      border-radius: 10px;
      max-width: 100%;
      max-height: 200px;
      margin-top: 10px;
      border: 2px solid #fbbf24;
    }
  </style>
</head>
<body>

<div class="container py-3">
  <div class="form-container">

    <!-- Header -->
    <div class="header-form">
      <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size:3rem;"></i>
      <h3 class="fw-bold mt-2 mb-1">Laporan Kerusakan Aset</h3>
      <p class="text-muted small mb-0">
        Temukan fasilitas rusak? Laporkan segera agar petugas bisa menindaklanjutinya.
      </p>
    </div>

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

    <form action="<?= base_url('landing/kirim_lapor') ?>" method="POST" enctype="multipart/form-data">
      <?= csrf_field() ?>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Nama Pelapor <span class="text-danger">*</span></label>
          <input type="text" class="form-control form-control-lg" name="nama_pelapor"
            placeholder="Nama lengkap Anda"
            value="<?= esc($old['nama_pelapor'] ?? '') ?>" required>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Unit Kerja / Ruangan <span class="text-danger">*</span></label>
          <input type="text" class="form-control form-control-lg" name="unit_kerja"
            placeholder="Contoh: Kelas 9A / Lab Komputer"
            value="<?= esc($old['unit_kerja'] ?? '') ?>" required>
        </div>
      </div>

      <div class="row">
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Kategori Barang <span class="text-danger">*</span></label>
          <select class="form-select form-select-lg" name="id_kategori_jenis" required>
            <option value="">-- Pilih Kategori --</option>
            <?php foreach ($kategori_jenis as $kj): ?>
              <option value="<?= $kj['id_kategori_jenis'] ?>"
                <?= (($old['id_kategori_jenis'] ?? '') == $kj['id_kategori_jenis']) ? 'selected' : '' ?>>
                <?= esc($kj['nilai_kategori']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-6 mb-3">
          <label class="form-label fw-bold">Nama Fasilitas / Barang <span class="text-danger">*</span></label>
          <input type="text" class="form-control form-control-lg" name="nama_barang"
            placeholder="Contoh: Kursi Siswa, AC Split, Proyektor"
            value="<?= esc($old['nama_barang'] ?? '') ?>" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label fw-bold">
          Kronologi / Detail Kerusakan <span class="text-danger">*</span>
        </label>
        <textarea class="form-control" name="keterangan_kerusakan" rows="4"
          placeholder="Jelaskan kondisi kerusakan yang Anda temukan secara detail. Kapan terjadi, seberapa parah, apakah membahayakan..." required><?= esc($old['keterangan_kerusakan'] ?? '') ?></textarea>
      </div>

      <div class="mb-4">
        <label class="form-label fw-bold text-danger">
          <i class="bi bi-camera-fill"></i> Foto Bukti Kerusakan <span class="text-danger">*</span>
        </label>
        <input class="form-control form-control-lg" type="file" name="foto_bukti_awal"
          accept="image/*" id="inputFoto" required
          onchange="previewFoto(this)">
        <small class="text-muted d-block mt-1">
          <i class="bi bi-info-circle me-1"></i>
          Format JPG/PNG, maks 5MB. Foto yang jelas sangat membantu petugas menilai tingkat kerusakan.
        </small>
        <!-- Preview foto sebelum kirim -->
        <img id="fotoPreview" class="foto-preview" alt="Preview foto bukti">
      </div>

      <div class="d-grid gap-2">
        <button type="submit" class="btn btn-danger btn-touch">
          <i class="bi bi-send-fill me-2"></i> Kirim Laporan Kerusakan
        </button>
        <a href="<?= base_url('/') ?>" class="btn btn-light btn-touch">
          <i class="bi bi-arrow-left me-1"></i> Batal & Kembali
        </a>
      </div>

    </form>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  // Preview foto sebelum dikirim
  function previewFoto(input) {
    const preview = document.getElementById('fotoPreview');
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.style.display = 'block';
      };
      reader.readAsDataURL(input.files[0]);
    }
  }
</script>
</body>
</html>
