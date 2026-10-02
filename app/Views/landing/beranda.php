<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Portal Sarpras - MTsN 4 Jombang</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

  <style>
    @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;600;700;800&display=swap');

    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body, html {
      height: 100%;
      overflow: hidden;
      font-family: 'Plus Jakarta Sans', 'Segoe UI', sans-serif;
      user-select: none;
    }

    .kiosk-bg {
      background-image:
        url('<?= base_url('assets/img/bg-landing.jpg') ?>'),
        url('https://images.unsplash.com/photo-1562774053-701939374585?auto=format&fit=crop&w=1920&q=80');
      background-size: cover;
      background-position: center;
      height: 100vh;
      width: 100vw;
      display: flex;
      flex-direction: column;
      position: relative;
    }
    .kiosk-bg::before {
      content: '';
      position: absolute;
      inset: 0;
      background: linear-gradient(160deg, rgba(0,40,20,0.72) 0%, rgba(0,0,0,0.62) 100%);
      z-index: 0;
    }

    .kiosk-content {
      position: relative;
      z-index: 1;
      display: flex;
      flex-direction: column;
      height: 100%;
    }

    /* --- Header --- */
    .kiosk-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 28px 50px;
      color: white;
    }
    .kiosk-brand img { height: 60px; }
    .kiosk-time { text-align: right; }
    .kiosk-time h2 { margin: 0; font-weight: 800; font-size: 3rem; letter-spacing: 2px; }
    .kiosk-time p  { margin: 0; font-weight: 300; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }

    /* --- Main / Menu --- */
    .kiosk-main {
      flex: 1;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 40px;
    }
    .menu-card {
      background: rgba(255,255,255,0.1);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 24px;
      padding: 50px 30px;
      text-align: center;
      color: white;
      transition: all 0.3s ease;
      text-decoration: none;
      display: block;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    .menu-card:hover {
      transform: translateY(-10px);
      background: rgba(255,255,255,0.2);
      color: white;
      border-color: rgba(255,255,255,0.5);
    }
    .menu-icon { font-size: 5rem; margin-bottom: 20px; }

    /* --- Footer --- */
    .kiosk-footer {
      background: rgba(0,0,0,0.5);
      backdrop-filter: blur(10px);
      color: white;
      padding: 15px 50px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    .running-text { flex: 1; font-size: 1.2rem; font-weight: 300; margin-right: 30px; }
    .login-btn { text-decoration: none; transition: 0.3s; display: flex; align-items: center; }
    .login-btn img { height: 40px; opacity: 0.5; transition: 0.3s; }
    .login-btn:hover img { opacity: 1; transform: scale(1.1); }

    /* --- Mobile --- */
    @media (max-width: 767px) {
      body, html { height: auto; overflow-x: hidden; overflow-y: auto; }
      .kiosk-bg { height: auto; min-height: 100vh; }
      .kiosk-header { flex-direction: column; padding: 20px 15px; text-align: center; }
      .kiosk-brand { display: flex !important; flex-direction: column; margin-bottom: 15px; }
      .kiosk-brand img { height: 50px; margin: 0 0 10px 0 !important; }
      .kiosk-brand h1 { font-size: 1.5rem; }
      .kiosk-brand p  { font-size: 1rem !important; }
      .kiosk-time { text-align: center; }
      .kiosk-time h2 { font-size: 1.8rem; }
      .kiosk-time p  { font-size: 0.9rem; }
      .kiosk-main { padding: 20px 15px; }
      .menu-card { padding: 25px 15px; }
      .menu-icon { font-size: 3rem; margin-bottom: 10px; }
      .menu-card h3 { font-size: 1.2rem; }
      .menu-card p  { font-size: 0.85rem !important; margin-top: 5px !important; }
      .kiosk-footer { padding: 15px; gap: 15px; }
      .running-text { font-size: 0.9rem; margin-right: 10px; }
      .login-btn img { height: 30px; }
    }
  </style>
</head>
<body>
  <div class="kiosk-bg">
    <div class="kiosk-content">

      <!-- Header -->
      <div class="kiosk-header">
        <div class="kiosk-brand d-flex align-items-center">
          <img src="<?= base_url('assets/img/logo-mtsn.png') ?>" alt="Logo MTsN" class="me-3">
          <div>
            <h1 class="mb-0 fw-bold">PORTAL SARPRAS</h1>
            <p class="mb-0 fs-4 text-white-50">MTsN 4 JOMBANG</p>
          </div>
        </div>
        <div class="kiosk-time">
          <h2 id="landing_clock">00:00:00</h2>
          <p id="landing_date">Memuat Tanggal...</p>
        </div>
      </div>

      <!-- Menu Utama -->
      <div class="kiosk-main">
        <div class="container">
          <div class="row justify-content-center g-4">

            <!-- Scan QR untuk Pinjam (utama) -->
            <div class="col-md-4">
              <a href="<?= base_url('scanner') ?>" class="menu-card h-100" style="border-color: rgba(13,202,240,0.5);">
                <i class="bi bi-qr-code-scan text-info menu-icon"></i>
                <h3 class="fw-bold">Scan & Pinjam</h3>
                <p class="mb-0 text-white-50 mt-2">
                  Scan QR Code pada barang untuk langsung membuat permohonan peminjaman.
                </p>
              </a>
            </div>

            <!-- Form Pinjam Manual -->
            <div class="col-md-4">
              <a href="<?= base_url('landing/form_pinjam') ?>" class="menu-card h-100">
                <i class="bi bi-bookmark-check text-success menu-icon"></i>
                <h3 class="fw-bold">Form Peminjaman</h3>
                <p class="mb-0 text-white-50 mt-2">
                  Tidak ada QR? Scan dulu aset lewat kamera untuk membuka form otomatis.
                </p>
              </a>
            </div>

            <!-- Laporan Kerusakan -->
            <div class="col-md-4">
              <a href="<?= base_url('landing/form_lapor') ?>" class="menu-card h-100">
                <i class="bi bi-exclamation-triangle text-warning menu-icon"></i>
                <h3 class="fw-bold">Laporan Kerusakan</h3>
                <p class="mb-0 text-white-50 mt-2">
                  Temukan fasilitas rusak? Laporkan segera ke petugas Sarpras di sini.
                </p>
              </a>
            </div>

          </div>
        </div>
      </div>

      <!-- Footer Running Text -->
      <div class="kiosk-footer">
        <div class="running-text">
          <marquee behavior="scroll" direction="left" scrollamount="6">
            Selamat Datang di Portal Layanan Sarpras MTsN 4 Jombang. &nbsp;
            Waktu Pelayanan: Senin s/d Jumat, Pukul 08.00 – 15.00 WIB. &nbsp;
            Cara Pinjam: Scan QR Code pada barang → Isi form → Tunggu konfirmasi petugas. &nbsp;
          </marquee>
        </div>
        <a href="<?= base_url('auth') ?>" class="login-btn" title="Login Admin">
          <img src="<?= base_url('assets/img/logo-mtsn-white.png') ?>"
            alt="Login" onerror="this.style.display='none'">
        </a>
      </div>

    </div>
  </div>

  <!-- SweetAlert: notif sukses kirim pinjam / lapor -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <?php if (session()->getFlashdata('pesan')): ?>
  <script>
    Swal.fire({
      icon: 'success',
      title: 'Berhasil!',
      html: '<?= addslashes(session()->getFlashdata('pesan')) ?>',
      confirmButtonColor: '#198754',
      confirmButtonText: 'OK'
    });
  </script>
  <?php endif; ?>
  <?php if (session()->getFlashdata('error')): ?>
  <script>
    Swal.fire({
      icon: 'warning',
      title: 'Perhatian',
      html: '<?= addslashes(session()->getFlashdata('error')) ?>',
      confirmButtonColor: '#dc3545'
    });
  </script>
  <?php endif; ?>

  <script>
    function updateLandingTime() {
      const now = new Date();
      const jam = now.toLocaleTimeString('id-ID', {
        hour: '2-digit', minute: '2-digit', second: '2-digit'
      }).replace(/\./g, ':');
      const tgl = now.toLocaleDateString('id-ID', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
      });
      document.getElementById('landing_clock').innerText = jam;
      document.getElementById('landing_date').innerText  = tgl;
    }
    setInterval(updateLandingTime, 1000);
    updateLandingTime();
  </script>
</body>
</html>
