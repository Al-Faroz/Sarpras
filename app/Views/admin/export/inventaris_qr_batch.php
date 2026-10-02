<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cetak Label QR — <?= $total ?> Aset — Sarpras MTsN 4 Jombang</title>
  <style>
    /* ============================================================
       RESET & BASE
    ============================================================ */
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: 'Arial Narrow', Arial, sans-serif;
      background: #f5f5f5;
      color: #000;
    }

    /* ============================================================
       PREVIEW TOOLBAR (hanya tampil di layar, hilang saat print)
    ============================================================ */
    .toolbar {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 9999;
      background: #1E3A5F;
      color: #fff;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 12px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.3);
    }
    .toolbar .info { font-size: 0.9rem; }
    .toolbar .info strong { font-size: 1rem; }
    .btn-print {
      background: #22c55e;
      color: #fff;
      border: none;
      padding: 8px 24px;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 700;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .btn-print:hover { background: #16a34a; }
    .btn-back {
      background: rgba(255,255,255,0.15);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.3);
      padding: 8px 16px;
      border-radius: 8px;
      font-size: 0.85rem;
      cursor: pointer;
      text-decoration: none;
    }
    .btn-back:hover { background: rgba(255,255,255,0.25); color:#fff; }

    /* Body offset saat di layar agar tidak tertutup toolbar */
    body { padding-top: 56px; }

    /* ============================================================
       HALAMAN A4 PORTRAIT
       A4: 210mm × 297mm
       Margin: 8mm tiap sisi
       Area cetak: 194mm × 281mm
       3 kolom × 7 baris = 21 label per halaman
       Setiap label: 60mm × 40mm (sesuai ukuran stiker)
       Gap antar label: (194 - 3×60) / 2 = 7mm horizontal
                        (281 - 7×40) / 6 = 11.8mm vertikal → 11mm
    ============================================================ */
    @page {
      size: A4 portrait;
      margin: 8mm;
    }

    .page-a4 {
      width: 210mm;
      min-height: 297mm;
      padding: 8mm;
      background: #fff;
      margin: 0 auto 20px;
      box-shadow: 0 4px 20px rgba(0,0,0,0.15);
    }

    .label-grid {
      display: grid;
      grid-template-columns: repeat(3, 60mm);
      grid-template-rows: repeat(7, 40mm);
      column-gap: 7mm;
      row-gap: 0mm;
      /* Total grid width: 3×60 + 2×7 = 194mm ✓ */
    }

    /* ============================================================
       SATU LABEL QR — 60mm × 40mm
       Sama persis dengan inventaris_qr.php (layout satuan)
    ============================================================ */
    .label-item {
      width: 60mm;
      height: 40mm;
      padding: 2mm;
      overflow: hidden;
      /* Garis bantu potong (tipis, tidak dominan) */
      border: 0.3pt dashed #bbb;
    }

    .label-wrapper {
      display: flex;
      flex-direction: row;
      width: 56mm;
      height: 36mm;
      gap: 1.5mm;
    }

    /* Kolom kiri: QR + No. Inventaris */
    .col-left {
      width: 30mm;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      align-items: center;
    }

    .qr-img {
      width: 28mm;
      height: 28mm;
      display: block;
    }

    .no-inv {
      font-size: 4.8px;
      font-family: 'Courier New', Courier, monospace;
      font-weight: bold;
      text-align: center;
      word-break: break-all;
      line-height: 1.3;
      margin-top: 1mm;
      width: 30mm;
    }

    /* Kolom kanan: Info teks */
    .col-right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      padding-left: 1mm;
      border-left: 0.5pt solid #555;
    }

    .kop {
      font-size: 6px;
      font-weight: bold;
      text-transform: uppercase;
      text-align: center;
      letter-spacing: 0.3px;
      border-bottom: 0.5pt solid #000;
      padding-bottom: 1mm;
      margin-bottom: 1.5mm;
      line-height: 1.2;
    }

    .info-row {
      font-size: 5.5px;
      line-height: 1.5;
      margin-bottom: 0.5mm;
    }

    .info-row .lbl {
      color: #666;
      font-size: 4.8px;
    }

    .info-row .val {
      font-weight: bold;
      display: block;
      word-break: break-word;
    }

    /* Label placeholder (jika total tidak habis 21) */
    .label-empty {
      width: 60mm;
      height: 40mm;
      border: 0.3pt dashed #ddd;
      background: #fafafa;
    }

    /* ============================================================
       PRINT RULES
    ============================================================ */
    @media print {
      body {
        padding-top: 0;
        background: #fff;
      }

      .toolbar { display: none !important; }

      .page-a4 {
        width: 210mm;
        min-height: 297mm;
        padding: 8mm;
        margin: 0;
        box-shadow: none;
        page-break-after: always;
      }

      .page-a4:last-child {
        page-break-after: avoid;
      }

      .label-item {
        page-break-inside: avoid;
      }

      @page { margin: 8mm; }
    }
  </style>
</head>
<body>

  <!-- ============================================================
       TOOLBAR PREVIEW (hanya di layar)
  ============================================================ -->
  <div class="toolbar">
    <div class="info">
      <strong>Preview Cetak Label QR</strong> —
      <?= $total ?> label /
      <?= ceil($total / 21) ?> halaman A4
      <span style="opacity:0.6; font-size:0.8rem; margin-left:8px;">
        (3 label/baris × 7 baris = 21 label/halaman)
      </span>
    </div>
    <div class="d-flex gap-2">
      <a href="javascript:history.back()" class="btn-back">← Kembali</a>
      <button class="btn-print" onclick="window.print()">
        🖨️ Cetak Sekarang
      </button>
    </div>
  </div>

  <!-- ============================================================
       HALAMAN-HALAMAN A4
       Setiap halaman = 21 label, dipotong dari array $aset_list
  ============================================================ -->
  <?php
    $perHalaman = 21;
    $halaman    = array_chunk($aset_list, $perHalaman);
  ?>

  <?php foreach ($halaman as $halamanIdx => $chunk): ?>
    <div class="page-a4">
      <div class="label-grid">

        <?php foreach ($chunk as $item):
          $aset      = $item['aset'];
          $qrBase64  = $item['qr_image_base64'];
          $labelDana = $item['label_dana'];
        ?>
          <!-- LABEL: <?= esc($aset['no_inv']) ?> -->
          <div class="label-item">
            <div class="label-wrapper">

              <!-- Kolom Kiri: QR + No. Inv -->
              <div class="col-left">
                <img class="qr-img" src="<?= $qrBase64 ?>" alt="QR">
                <div class="no-inv"><?= esc($aset['no_inv']) ?></div>
              </div>

              <!-- Kolom Kanan: Info -->
              <div class="col-right">

                <div class="kop">
                  MTsN 4 JOMBANG<br>
                  <span style="font-size:4px;font-weight:normal;">Sistem Sarpras</span>
                </div>

                <div class="info-row">
                  <span class="lbl">Jenis Barang</span>
                  <span class="val"><?= esc($aset['jenis_barang']) ?></span>
                </div>

                <div class="info-row">
                  <span class="lbl">Merk / Unit</span>
                  <span class="val"><?= esc($aset['merk']) ?></span>
                </div>

                <div class="info-row">
                  <span class="lbl">Sumber Dana</span>
                  <span class="val"><?= esc($labelDana) ?></span>
                </div>

                <div class="info-row">
                  <span class="lbl">Tahun</span>
                  <span class="val">
                    <?= $aset['tahun'] === 'pra2016' ? 'Sblm 2016' : esc($aset['tahun']) ?>
                  </span>
                </div>

              </div><!-- /col-right -->
            </div><!-- /label-wrapper -->
          </div><!-- /label-item -->

        <?php endforeach; ?>

        <?php
          // Isi sisa slot kosong agar grid tetap rapi
          $sisa = $perHalaman - count($chunk);
          for ($i = 0; $i < $sisa; $i++):
        ?>
          <div class="label-empty"></div>
        <?php endfor; ?>

      </div><!-- /label-grid -->
    </div><!-- /page-a4 -->
  <?php endforeach; ?>

  <script>
    // Auto print jika dibuka via target="_blank" dari form batch
    // Komentari baris ini jika ingin preview dulu sebelum cetak
    // window.onload = function () { window.print(); };
  </script>

</body>
</html>
