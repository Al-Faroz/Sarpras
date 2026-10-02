<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Laporan Data Inventaris</title>
  <style>
    body { font-family: Arial, sans-serif; font-size: 10px; color: #222; }

    .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1E3A5F; padding-bottom: 8px; }
    .header h2 { margin: 0 0 4px; font-size: 16px; text-transform: uppercase; color: #1E3A5F; }
    .header p  { margin: 2px 0; font-size: 10px; color: #555; }
    .header .tgl { font-size: 9px; color: #888; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; }

    thead tr th {
      background-color: #1E3A5F;
      color: #fff;
      padding: 6px 5px;
      text-align: left;
      font-size: 9px;
      text-transform: uppercase;
      letter-spacing: 0.3px;
    }

    tbody tr td { border: 1px solid #ccc; padding: 5px; vertical-align: top; }
    tbody tr:nth-child(even) td { background-color: #f7f9fc; }

    .text-center { text-align: center; }
    .text-right  { text-align: right; }
    .code        { font-family: 'Courier New', monospace; font-size: 8px; }
    .bold        { font-weight: bold; }

    .badge-baik    { color: #155724; background: #d4edda; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight:bold; }
    .badge-ringan  { color: #856404; background: #fff3cd; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight:bold; }
    .badge-berat   { color: #721c24; background: #f8d7da; padding: 2px 5px; border-radius: 3px; font-size: 8px; font-weight:bold; }

    .footer-pdf { margin-top: 15px; font-size: 8px; color: #888; text-align: right; }
  </style>
</head>
<body>

  <div class="header">
    <h2>Data Master Inventaris Sarpras</h2>
    <p>MTsN 4 Jombang — Laporan Aset Aktif</p>
    <p class="tgl">Dicetak: <?= date('d F Y, H:i') ?> WIB</p>
  </div>

  <table>
    <thead>
      <tr>
        <th width="3%"  class="text-center">No</th>
        <th width="24%">No. Inventaris</th>
        <th width="18%">Jenis Barang</th>
        <th width="16%">Merk / Unit</th>
        <th width="10%">Sumber Dana</th>
        <th width="6%"  class="text-center">Tahun</th>
        <th width="9%"  class="text-center">Kondisi</th>
        <th width="7%"  class="text-center">Lokasi</th>
        <th width="7%">Petugas</th>
      </tr>
    </thead>
    <tbody>
      <?php if (empty($inventaris)): ?>
        <tr>
          <td colspan="9" class="text-center" style="padding:15px; color:#888;">
            Belum ada data inventaris.
          </td>
        </tr>
      <?php else: ?>
        <?php $no = 1; foreach ($inventaris as $row): ?>
          <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="code"><?= esc($row['no_inv']) ?></td>
            <td class="bold"><?= esc($row['jenis_barang']) ?></td>
            <td><?= esc($row['merk']) ?></td>
            <td><?= esc(\App\Models\InventarisModel::getLabelDana($row['kode_dana'])) ?></td>
            <td class="text-center">
              <?= $row['tahun'] === 'pra2016' ? '< 2016' : esc($row['tahun']) ?>
            </td>
            <td class="text-center">
              <?php if ($row['kondisi'] == 'Baik'): ?>
                <span class="badge-baik">Baik</span>
              <?php elseif ($row['kondisi'] == 'Rusak Ringan'): ?>
                <span class="badge-ringan">Rusak Ringan</span>
              <?php else: ?>
                <span class="badge-berat">Rusak Berat</span>
              <?php endif; ?>
            </td>
            <td class="text-center"><?= esc($row['lokasi']) ?></td>
            <td><?= esc($row['nama_petugas']) ?></td>
          </tr>
        <?php endforeach; ?>
      <?php endif; ?>
    </tbody>
  </table>

  <div class="footer-pdf">
    Total: <?= count($inventaris) ?> unit aset terdaftar — Portal Sarpras MTsN 4 Jombang
  </div>

</body>
</html>
