<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Label QR - <?= esc($aset['no_inv']) ?></title>
  <style>
    /* ============================================================
       KERTAS LABEL TERMAL: 60mm x 40mm
       Margin aman: 2mm tiap sisi
       Area cetak efektif: 56mm x 36mm
    ============================================================ */
    @page {
      size: 60mm 40mm;
      margin: 0;
    }

    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Arial Narrow', Arial, sans-serif;
      width: 60mm;
      height: 40mm;
      padding: 2mm;
      background: #fff;
      color: #000;
      overflow: hidden;
    }

    /* Layout utama: 2 kolom */
    .wrapper {
      display: flex;
      flex-direction: row;
      width: 56mm;   /* 60mm - 4mm padding */
      height: 36mm;  /* 40mm - 4mm padding */
      gap: 1.5mm;
    }

    /* ============================================================
       KOLOM KIRI: QR Code + No. Inventaris di bawahnya
    ============================================================ */
    .col-left {
      width: 30mm;
      display: flex;
      flex-direction: column;
      align-items: center;
      flex-shrink: 0;
    }

    .qr-img {
      width: 28mm;
      height: 28mm;
      display: block;
    }

    .no-inv {
      font-size: 5px;
      font-family: 'Courier New', Courier, monospace;
      font-weight: bold;
      text-align: center;
      word-break: break-all;
      line-height: 1.3;
      margin-top: 1mm;
      width: 30mm;
    }

    /* ============================================================
       KOLOM KANAN: Info teks hierarki
    ============================================================ */
    .col-right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: flex-start;
      padding-left: 1mm;
      border-left: 0.5pt solid #555;
    }

    .kop {
      font-size: 6.5px;
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
      font-size: 5.8px;
      line-height: 1.5;
      margin-bottom: 0.5mm;
    }

    .info-row .lbl {
      color: #555;
      font-size: 5px;
    }

    .info-row .val {
      font-weight: bold;
      display: block;
      word-break: break-word;
    }

    /* Print: hapus header/footer browser */
    @media print {
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      @page { margin: 0; }
    }
  </style>
</head>

<body onload="window.print()">
  <div class="wrapper">

    <!-- KOLOM KIRI: QR + No Inventaris -->
    <div class="col-left">
      <img class="qr-img" src="<?= $qr_image_base64 ?>" alt="QR">
      <div class="no-inv"><?= esc($aset['no_inv']) ?></div>
    </div>

    <!-- KOLOM KANAN: Info teks -->
    <div class="col-right">

      <div class="kop">
        MTsN 4 JOMBANG<br>
        <span style="font-size:4.5px;font-weight:normal;">Sistem Sarpras</span>
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
        <span class="val"><?= esc($label_dana) ?></span>
      </div>

      <div class="info-row">
        <span class="lbl">Tahun</span>
        <span class="val">
          <?= $aset['tahun'] === 'pra2016' ? 'Sebelum 2016' : esc($aset['tahun']) ?>
        </span>
      </div>

      <div class="info-row">
        <span class="lbl">Kondisi</span>
        <span class="val"><?= esc($aset['kondisi']) ?></span>
      </div>

    </div>
  </div>
</body>
</html>
