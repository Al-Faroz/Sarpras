<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Data Peminjaman Aset</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 9px; color: #222; }

    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1E3A5F; padding-bottom: 8px; }
    .header h2 { margin: 0 0 4px; font-size: 15px; text-transform: uppercase; color: #1E3A5F; }
    .header p  { margin: 2px 0; font-size: 10px; color: #555; }
    .header .tgl { font-size: 8px; color: #888; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }

    thead tr th {
      background-color: #1E3A5F;
      color: #fff;
      padding: 6px 4px;
      text-align: left;
      font-size: 8px;
      text-transform: uppercase;
    }

    tbody tr td { border: 1px solid #ccc; padding: 5px 4px; vertical-align: middle; }
    tbody tr:nth-child(even) td { background-color: #f7f9fc; }

    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .code { font-family: 'Courier New', monospace; font-size: 7.5px; }
    .italic { font-style: italic; color: #555; }

    .badge-setuju { color: #155724; background:#d4edda; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }
    .badge-tolak  { color: #721c24; background:#f8d7da; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }
    .badge-pending{ color: #533f03; background:#fff3cd; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }

    .img-bukti { max-width: 80px; max-height: 60px; border-radius: 3px; border: 1px solid #ddd; }
    .no-foto { color: #aaa; font-style: italic; font-size: 7px; }

    .footer-pdf { margin-top: 15px; font-size: 8px; color: #888; text-align: right; }
  </style>
</head>
<body>

  <div class="header">
    <h2>Riwayat Peminjaman Aset Sarpras</h2>
    <p>MTsN 4 Jombang — Rekapitulasi Data Peminjaman</p>
    <p class="tgl">Dicetak: <?= date('d F Y, H:i') ?> WIB</p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="3%"  class="text-center">No</th>
        <th width="16%">No. Inventaris</th>
        <th width="13%">Jenis Barang</th>
        <th width="11%">Merk</th>
        <th width="12%">Nama Peminjam</th>
        <th width="13%">Keterangan</th>
        <th width="7%"  class="text-center">Tgl Pinjam</th>
        <th width="7%"  class="text-center">Status</th>
        <th width="7%"  class="text-center">Tgl Kembali</th>
        <th width="11%" class="text-center">Bukti Pengembalian</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($peminjaman)): ?>
        <tr>
          <td colspan="10" class="text-center" style="padding:15px;color:#888;">
            Belum ada data peminjaman.
          </td>
        </tr>
      <?php else: ?>
        <?php $no = 1; foreach ($peminjaman as $row): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="code"><?= esc($row['no_inv']) ?></td>
            <td class="bold"><?= esc($row['jenis_barang']) ?></td>
            <td><?= esc($row['merk']) ?></td>
            <td class="bold"><?= esc($row['nama_peminjam']) ?></td>
            <td class="italic"><?= esc(mb_substr($row['keterangan'], 0, 50)) ?></td>
            <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
            <td class="text-center">
              <?php if ($row['status_approval'] == 'Disetujui'): ?>
                <span class="badge-setuju">Disetujui</span>
              <?php elseif ($row['status_approval'] == 'Ditolak'): ?>
                <span class="badge-tolak">Ditolak</span>
              <?php else: ?>
                <span class="badge-pending">Pending</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?= $row['tanggal_kembalikan']
                ? date('d/m/Y', strtotime($row['tanggal_kembalikan']))
                : '—' ?>
            </td>
            <td class="text-center">
              <?php
              $fotoPath = FCPATH . 'uploads/peminjaman/' . ($row['foto_bukti_pengembalian'] ?? '');
              if (!empty($row['foto_bukti_pengembalian']) && file_exists($fotoPath)):
                $imgData = base64_encode(file_get_contents($fotoPath));
                $mimeType = mime_content_type($fotoPath) ?: 'image/jpeg';
              ?>
                <img src="data:<?= $mimeType ?>;base64,<?= $imgData ?>"
                     class="img-bukti" alt="Bukti Pengembalian">
              <?php else: ?>
                <span class="no-foto">Belum ada foto</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer-pdf">
    Total: <?= count($peminjaman) ?> record — Portal Sarpras MTsN 4 Jombang
  </div>

</body>
</html>
