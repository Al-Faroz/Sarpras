<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Catatan Pemeliharaan Aset</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; }
        .header { text-align: center; margin-bottom: 25px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; text-transform: uppercase; }
        .header p { margin: 5px 0 0 0; font-size: 12px; color: #666; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #666; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; text-transform: uppercase; font-size: 10px; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <h2>Catatan Perawatan & Perbaikan Sarpras</h2>
        <p>MTsN 4 Jombang - Rekapitulasi Biaya Pemeliharaan Fasilitas</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%" class="text-center">No</th>
                <th width="20%">Nama Pemeliharaan</th>
                <th width="12%" class="text-center">No Aset</th>
                <th width="24%">Keterangan Perbaikan</th>
                <th width="10%" class="text-center">Tanggal</th>
                <th width="15%" class="text-right">Biaya (Rp)</th>
                <th width="15%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($pemeliharaan)) : ?>
                <tr>
                    <td colspan="7" class="text-center">Belum ada data pemeliharaan tercatat.</td>
                </tr>
            <?php else : ?>
                <?php $no = 1; $total_biaya = 0; foreach ($pemeliharaan as $row) : $total_biaya += $row['biaya']; ?>
                <tr>
                    <td class="text-center"><?= $no++ ?></td>
                    <td class="font-bold"><?= esc($row['nama_pemeliharaan']) ?></td>
                    <td class="text-center"><?= esc($row['no_aset'] ?: '-') ?></td>
                    <td><i><?= esc($row['keterangan']) ?></i></td>
                    <td class="text-center"><?= date('d/m/Y', strtotime($row['tanggal'])) ?></td>
                    <td class="text-right font-bold"><?= number_format($row['biaya'], 0, ',', '.') ?></td>
                    <td><?= esc($row['nama_petugas']) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr style="background-color: #f9f9f9;">
                    <td colspan="5" class="text-right font-bold" style="padding: 10px;">TOTAL PENGELUARAN :</td>
                    <td class="text-right font-bold" style="color: #d9534f; padding: 10px;">Rp <?= number_format($total_biaya, 0, ',', '.') ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</body>
</html>