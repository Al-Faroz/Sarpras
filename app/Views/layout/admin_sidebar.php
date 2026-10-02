<?php $role = session()->get('role'); ?>
<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <ul class="nav">
    <li class="nav-item nav-profile">
      <a href="#" class="nav-link">
        <div class="nav-profile-text d-flex flex-column">
          <span class="font-weight-bold mb-1"><?= esc(session()->get('nama_lengkap')) ?></span>
          <span class="text-secondary text-small text-uppercase">
            <?= $role === 'admin' ? '⚙ Admin' : '👤 Operator' ?>
          </span>
        </div>
      </a>
    </li>

    <!-- Notifikasi Bell -->
    <li class="nav-item px-3 py-2">
      <div class="d-flex align-items-center gap-2 position-relative">
        <button class="btn btn-sm btn-outline-warning w-100 text-start d-flex align-items-center gap-2"
                id="btnNotifToggle" onclick="toggleNotif()">
          <i class="mdi mdi-bell-outline"></i>
          <span>Notifikasi</span>
          <span class="badge bg-danger ms-auto" id="notifCount" style="display:none;">0</span>
        </button>
      </div>
      <div id="notifDropdown" class="card shadow mt-1 p-0" style="display:none; max-height:280px; overflow-y:auto; font-size:0.82rem;">
        <div id="notifBody" class="p-2 text-muted text-center">Memuat...</div>
      </div>
    </li>

    <li class="nav-item"><hr class="mt-1 mb-1 border-secondary"></li>

    <!-- Dashboard -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/dashboard') ? 'active' : '' ?>"
         href="<?= base_url($role . '/dashboard') ?>">
        <span class="menu-title">Dashboard</span>
        <i class="mdi mdi-home menu-icon"></i>
      </a>
    </li>

    <!-- Data Inventaris -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/inventaris') && !str_contains(current_url(), 'print_qr') && !str_contains(current_url(), 'import_dipa') ? 'active' : '' ?>"
         href="<?= base_url('admin/inventaris') ?>">
        <span class="menu-title">Data Inventaris</span>
        <i class="mdi mdi-cube-outline menu-icon"></i>
      </a>
    </li>

    <!-- Cetak QR Massal -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), 'print_qr_batch') ? 'active' : '' ?>"
         href="<?= base_url('admin/inventaris/print_qr_batch') ?>">
        <span class="menu-title">Cetak QR Massal</span>
        <i class="mdi mdi-qrcode-plus menu-icon"></i>
      </a>
    </li>

    <!-- Import DIPA -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), 'import_dipa') ? 'active' : '' ?>"
         href="<?= base_url('admin/inventaris/import_dipa') ?>">
        <span class="menu-title">Import DIPA</span>
        <i class="mdi mdi-file-import menu-icon"></i>
      </a>
    </li>

    <!-- Peminjaman -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/peminjaman') ? 'active' : '' ?>"
         href="<?= base_url($role . '/peminjaman') ?>">
        <span class="menu-title">Peminjaman</span>
        <i class="mdi mdi-bookmark-outline menu-icon"></i>
      </a>
    </li>

    <!-- Pemeliharaan — admin only -->
    <?php if ($role === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/pemeliharaan') ? 'active' : '' ?>"
         href="<?= base_url('admin/pemeliharaan') ?>">
        <span class="menu-title">Pemeliharaan</span>
        <i class="mdi mdi-wrench menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

    <!-- Laporan Kerusakan -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/laporan') ? 'active' : '' ?>"
         href="<?= base_url($role . '/laporan') ?>">
        <span class="menu-title">Laporan Kerusakan</span>
        <i class="mdi mdi-alert-circle-outline menu-icon"></i>
      </a>
    </li>

    <!-- Scan Aset -->
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/scanner') ? 'active' : '' ?>"
         href="<?= base_url('admin/scanner') ?>">
        <span class="menu-title">Scan Aset</span>
        <i class="mdi mdi-qrcode-scan menu-icon"></i>
      </a>
    </li>

    <!-- Manajemen User — admin only -->
    <?php if ($role === 'admin'): ?>
    <li class="nav-item">
      <a class="nav-link <?= str_contains(current_url(), '/users') ? 'active' : '' ?>"
         href="<?= base_url('admin/users') ?>">
        <span class="menu-title">Manajemen User</span>
        <i class="mdi mdi-account-multiple menu-icon"></i>
      </a>
    </li>
    <?php endif; ?>

  </ul>
</nav>

<script>
let notifOpen = false;

function toggleNotif() {
  notifOpen = !notifOpen;
  document.getElementById('notifDropdown').style.display = notifOpen ? 'block' : 'none';
  if (notifOpen) muatNotif();
}

function muatNotif() {
  const role = '<?= $role ?>';
  // Link edit notif: operator diarahkan ke path yang bisa diakses
  const editPinjamBase   = '<?= base_url("admin/peminjaman/edit/") ?>';
  const editLaporanBase  = '<?= base_url("admin/laporan/edit/") ?>';

  fetch('<?= base_url('ajax/notifikasi') ?>')
    .then(r => r.json())
    .then(data => {
      const badge = document.getElementById('notifCount');
      badge.textContent = data.total;
      badge.style.display = data.total > 0 ? 'inline-block' : 'none';

      let html = '';
      if (data.total === 0) {
        html = '<p class="text-muted text-center py-2 mb-0"><i class="mdi mdi-check-circle text-success"></i> Tidak ada notifikasi baru</p>';
      } else {
        if (data.items_pinjam.length > 0) {
          html += '<div class="px-2 pt-2 pb-1 text-uppercase text-muted" style="font-size:0.7rem;letter-spacing:1px;font-weight:bold;">⏳ Peminjaman Pending</div>';
          data.items_pinjam.forEach(p => {
            html += `<a href="${editPinjamBase}${p.id_peminjaman}"
                        class="d-block px-2 py-1 text-dark text-decoration-none border-bottom hover-bg">
                       <i class="mdi mdi-bookmark-outline text-warning me-1"></i>
                       <strong>${p.nama_peminjam}</strong> — ${p.jenis_barang}<br>
                       <small class="text-muted">${p.merk}</small>
                     </a>`;
          });
        }
        if (data.items_laporan.length > 0) {
          html += '<div class="px-2 pt-2 pb-1 text-uppercase text-muted" style="font-size:0.7rem;letter-spacing:1px;font-weight:bold;">🔴 Laporan Masuk</div>';
          data.items_laporan.forEach(l => {
            html += `<a href="${editLaporanBase}${l.id_laporan}"
                        class="d-block px-2 py-1 text-dark text-decoration-none border-bottom hover-bg">
                       <i class="mdi mdi-alert-circle-outline text-danger me-1"></i>
                       <strong>${l.nama_pelapor}</strong> — ${l.nama_barang}
                     </a>`;
          });
        }
      }
      document.getElementById('notifBody').innerHTML = html;
    })
    .catch(() => {
      document.getElementById('notifBody').innerHTML = '<p class="text-danger text-center py-2">Gagal memuat notifikasi.</p>';
    });
}

document.addEventListener('DOMContentLoaded', () => {
  muatNotif();
  setInterval(muatNotif, 60000);
});

document.addEventListener('click', function(e) {
  const btn = document.getElementById('btnNotifToggle');
  const dd  = document.getElementById('notifDropdown');
  if (btn && dd && !btn.contains(e.target) && !dd.contains(e.target)) {
    notifOpen = false;
    dd.style.display = 'none';
  }
});
</script>
<style>.hover-bg:hover { background: #f8f9fa; }</style>
