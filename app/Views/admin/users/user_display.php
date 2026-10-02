<?= $this->include('layout/admin_header') ?>
<?= $this->include('layout/admin_sidebar') ?>

<div class="main-panel">
    <div class="content-wrapper">
        <div class="page-header">
            <h3 class="page-title">
                <span class="page-title-icon bg-gradient-primary text-white me-2">
                    <i class="mdi mdi-account-multiple"></i>
                </span> Manajemen User
            </h3>
            <nav aria-label="breadcrumb">
                <a href="<?= base_url('admin/users/tambah') ?>" class="btn btn-gradient-primary btn-icon-text font-weight-bold shadow-sm">
                    <i class="mdi mdi-plus btn-icon-prepend"></i> Tambah User 
                </a>
            </nav>
        </div>

        <?php if(session()->getFlashdata('pesan')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="mdi mdi-check-circle me-2"></i> <?= session()->getFlashdata('pesan') ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-12 grid-margin">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h4 class="card-title">Daftar Pengguna Sistem</h4>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 5%;">No</th>
                                        <th>Nama Lengkap</th>
                                        <th>Username</th>
                                        <th>Hak Akses / Role</th>
                                        <th style="width: 15%; text-align: center;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $no = 1; foreach($users as $row): ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td class="font-weight-bold text-dark"><?= esc($row['nama_lengkap']) ?></td>
                                        <td><code><?= esc($row['username']) ?></code></td>
                                        <td>
                                            <?php if($row['role'] == 'admin'): ?>
                                                <label class="badge badge-gradient-danger px-3 rounded-pill">Administrator</label>
                                            <?php else: ?>
                                                <label class="badge badge-gradient-info px-3 rounded-pill">Petugas Sarpras</label>
                                            <?php endif; ?>
                                        </td>
                                        <td style="text-align: center;">
                                            <a href="<?= base_url('admin/users/edit/' . $row['id_user']) ?>" class="btn btn-inverse-warning btn-sm btn-icon" title="Edit Pengguna">
                                                <i class="mdi mdi-pencil"></i>
                                            </a>
                                            <?php if(session()->get('id_user') != $row['id_user']): ?>
                                                <a href="<?= base_url('admin/users/hapus/' . $row['id_user']) ?>" class="btn btn-inverse-danger btn-sm btn-icon ms-1" title="Hapus Pengguna" onclick="return confirm('Apakah Anda yakin ingin menghapus user ini?')">
                                                    <i class="mdi mdi-delete"></i>
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
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