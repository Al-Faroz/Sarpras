<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Pengaduan & Kerusakan Fasilitas</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 9px; color: #222; }

    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #c0392b; padding-bottom: 8px; }
    .header h2 { margin: 0 0 4px; font-size: 15px; text-transform: uppercase; color: #c0392b; }
    .header p  { margin: 2px 0; font-size: 10px; color: #555; }
    .header .tgl { font-size: 8px; color: #888; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }

    thead tr th {
      background-color: #c0392b;
      color: #fff;
      padding: 6px 4px;
      text-align: left;
      font-size: 8px;
      text-transform: uppercase;
    }

    tbody tr td { border: 1px solid #ddd; padding: 5px 4px; vertical-align: middle; }
    tbody tr:nth-child(even) td { background-color: #fdf9f9; }

    .text-center { text-align: center; }
    .bold { font-weight: bold; }
    .italic { font-style: italic; color: #555; }

    .badge-selesai { color:#155724; background:#d4edda; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }
    .badge-proses  { color:#533f03; background:#fff3cd; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }
    .badge-masuk   { color:#721c24; background:#f8d7da; padding:2px 5px; border-radius:3px; font-size:7px; font-weight:bold; }

    .img-bukti { max-width: 75px; max-height: 56px; border-radius: 3px; border: 1px solid #ddd; }
    .no-foto { color: #bbb; font-style: italic; font-size: 7px; }

    .footer-pdf { margin-top: 15px; font-size: 8px; color: #888; text-align: right; }
  </style>
</head>
<body>

  <div class="header">
    <h2>Daftar Aduan Fasilitas &amp; Sarpras</h2>
    <p>MTsN 4 Jombang — Rekap Laporan Kerusakan dari Unit Kerja Madrasah</p>
    <p class="tgl">Dicetak: <?= date('d F Y, H:i') ?> WIB</p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="3%"  class="text-center">No</th>
        <th width="7%"  class="text-center">Tgl Laporan</th>
        <th width="12%">Pelapor / Unit</th>
        <th width="12%">Aset Dilaporkan</th>
        <th width="14%">Detail Kerusakan</th>
        <th width="7%"  class="text-center">Status</th>
        <th width="13%">Tindakan</th>
        <th width="8%">Petugas</th>
        <th width="12%" class="text-center">Foto Bukti Awal</th>
        <th width="12%" class="text-center">Foto Bukti Akhir</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($laporan)): ?>
        <tr>
          <td colspan="10" class="text-center" style="padding:15px;color:#888;">
            Belum ada laporan masuk.
          </td>
        </tr>
      <?php else: ?>
        <?php $no = 1; foreach ($laporan as $row): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center">
              <?= $row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '—' ?>
            </td>
            <td>
              <span class="bold"><?= esc($row['nama_pelapor']) ?></span><br>
              <span class="italic" style="font-size:7.5px;"><?= esc($row['unit_kerja']) ?></span>
            </td>
            <td>
              <span class="bold"><?= esc($row['nama_barang']) ?></span><br>
              <span class="italic" style="font-size:7.5px;"><?= esc($row['nilai_kategori']) ?></span>
            </td>
            <td><?= esc(mb_substr($row['keterangan_kerusakan'], 0, 60)) ?></td>
            <td class="text-center">
              <?php if ($row['status'] == 'Selesai'): ?>
                <span class="badge-selesai">Selesai</span>
              <?php elseif ($row['status'] == 'Proses'): ?>
                <span class="badge-proses">Proses</span>
              <?php else: ?>
                <span class="badge-masuk">Masuk</span>
              <?php endif; ?>
            </td>
            <td class="italic"><?= esc($row['tindakan'] ?: 'Belum ada tindakan') ?></td>
            <td><?= $row['nama_petugas'] ? esc($row['nama_petugas']) : '—' ?></td>

            <!-- Foto Bukti Awal -->
            <td class="text-center">
              <?php
              $pathAwal = FCPATH . 'uploads/laporan/' . ($row['foto_bukti_awal'] ?? '');
              if (!empty($row['foto_bukti_awal']) && file_exists($pathAwal)):
                $imgAwal = base64_encode(file_get_contents($pathAwal));
                $mimeAwal = mime_content_type($pathAwal) ?: 'image/jpeg';
              ?>
                <img src="data:<?= $mimeAwal ?>;base64,<?= $imgAwal ?>"
                     class="img-bukti" alt="Bukti Awal">
              <?php else: ?>
                <span class="no-foto">Belum ada</span>
              <?php endif; ?>
            </td>

            <!-- Foto Bukti Akhir -->
            <td class="text-center">
              <?php
              $pathAkhir = FCPATH . 'uploads/laporan/' . ($row['foto_bukti_akhir'] ?? '');
              if (!empty($row['foto_bukti_akhir']) && file_exists($pathAkhir)):
                $imgAkhir = base64_encode(file_get_contents($pathAkhir));
                $mimeAkhir = mime_content_type($pathAkhir) ?: 'image/jpeg';
              ?>
                <img src="data:<?= $mimeAkhir ?>;base64,<?= $imgAkhir ?>"
                     class="img-bukti" alt="Bukti Akhir">
              <?php else: ?>
                <span class="no-foto">Belum ada</span>
              <?php endif; ?>
            </td>

          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer-pdf">
    Total: <?= count($laporan) ?> laporan — Portal Sarpras MTsN 4 Jombang
  </div>

</body>
</html>
