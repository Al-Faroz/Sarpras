<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Login - Portal Sarpras MTsN 4 Jombang</title>
  <link rel="stylesheet" href="<?= base_url('assets/purpleadmin/vendors/mdi/css/materialdesignicons.min.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/purpleadmin/vendors/css/vendor.bundle.base.css') ?>">
  <link rel="stylesheet" href="<?= base_url('assets/purpleadmin/css/style.css') ?>">
  <link rel="shortcut icon" href="<?= base_url('assets/purpleadmin/images/favicon.ico') ?>" />
  <style>
    .content-wrapper { background: #f2edf3; }
    .auth-form-light { border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    .btn-login { min-height: 48px; font-size: 16px; font-weight: bold; } /* Touch-first standard minimum 48px */
  </style>
</head>
<body>
  <div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
      <div class="content-wrapper d-flex align-items-center auth px-0">
        <div class="row w-100 mx-0">
          <div class="col-lg-4 mx-auto">
            <div class="auth-form-light text-left py-5 px-4 px-sm-5">
              <div class="brand-logo text-center mb-4">
                <h4 class="text-primary font-weight-bold">PORTAL SARPRAS</h4>
                <p class="text-muted small">MTsN 4 Jombang</p>
              </div>
              <h4 class="font-weight-light text-center mb-4">Log In Petugas</h4>

              <?php if (session()->getFlashdata('error')) : ?>
                <div class="alert alert-danger alert-dismissible fade show small" role="alert">
                  <?= session()->getFlashdata('error') ?>
                </div>
              <?php endif; ?>

              <form class="pt-3" action="<?= base_url('auth/proses_login') ?>" method="POST">
                <?= csrf_field() ?>

                <div class="form-group">
                  <label for="username" class="small font-weight-bold">Username</label>
                  <input type="text" class="form-control form-control-lg" id="username" name="username" placeholder="Masukkan username" required autocomplete="off">
                </div>
                <div class="form-group">
                  <label for="password" class="small font-weight-bold">Password</label>
                  <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Masukkan password" required>
                </div>
                <div class="mt-4">
                  <button type="submit" class="btn btn-block btn-gradient-primary btn-lg font-weight-medium auth-form-btn btn-login">
                    MASUK DASHBOARD
                  </button>
                </div>
                <div class="text-center mt-4 font-weight-light small">
                  Bukan petugas? <a href="<?= base_url('/') ?>" class="text-primary font-weight-bold">Kembali ke Beranda</a>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script src="<?= base_url('assets/purpleadmin/vendors/js/vendor.bundle.base.js') ?>"></script>
  <script src="<?= base_url('assets/purpleadmin/js/off-canvas.js') ?>"></script>
  <script src="<?= base_url('assets/purpleadmin/js/hoverable-collapse.js') ?>"></script>
  <script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>
</body>
</html>