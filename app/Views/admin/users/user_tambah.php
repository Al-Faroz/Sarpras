<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-plus"></i>
                </span> Tambah User Baru
            </h3>
            <nav aria-label="breadcrumb">
                <a href="<?= base_url('admin/users') ?>" class="btn btn-light btn-icon-text font-weight-bold">
                    <i class="mdi mdi-arrow-left btn-icon-prepend"></i> Kembali
                </a>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8 grid-margin stretch-card">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Formulir Data Pengguna Baru</h4>
                        
                        <form class="forms-sample" action="<?= base_url('admin/users/simpan') ?>" method="POST">
                            <?= csrf_field() ?>
                            
                            <div class="form-group mb-3">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" class="form-control form-control-lg text-dark" id="nama_lengkap" name="nama_lengkap" placeholder="Masukkan nama lengkap petugas..." required autocomplete="off">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="username">Username</label>
                                <input type="text" class="form-control form-control-lg text-dark" id="username" name="username" placeholder="Buat nama pengguna (untuk login)..." required autocomplete="off">
                            </div>
                            
                            <div class="form-group mb-3">
                                <label for="password">Password</label>
                                <input type="password" class="form-control form-control-lg text-dark" id="password" name="password" placeholder="Masukkan password kata sandi baru..." required>
                            </div>
                            
                            <div class="form-group mb-4">
                                <label for="role">Hak Akses Sistem (Role)</label>
                                <select class="form-select form-select-lg text-dark" id="role" name="role" required>
                                    <option value="" disabled selected>-- Pilih Hak Akses --</option>
                                    <option value="admin">Administrator (Akses Penuh)</option>
                                    <option value="operator">Petugas Sarpras (Operator)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-gradient-primary btn-lg me-2 font-weight-bold">Simpan User</button>
                            <a href="<?= base_url('admin/users') ?>" class="btn btn-light btn-lg font-weight-bold">Batal</a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
</div>

<script src="<?= base_url('assets/purpleadmin/vendors/js/vendor.bundle.base.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/off-canvas.js') ?>"></script>
<script src="<?= base_url('assets/purpleadmin/js/misc.js') ?>"></script>
</body>
</html>