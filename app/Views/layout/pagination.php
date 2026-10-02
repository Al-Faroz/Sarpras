<?php if (isset($totalPage) && $totalPage > 1): ?>
<?php
  $baseUrl     = current_url();
  $queryParams = [];
  if (!empty($keyword)) $queryParams['search'] = $keyword;
  $buildUrl = function(int $p) use ($baseUrl, $queryParams): string {
      return $baseUrl . '?' . http_build_query(array_merge($queryParams, ['page' => $p]));
  };
  $from = (($page - 1) * $perPage) + 1;
  $to   = min($page * $perPage, $total);
?>
<div class="d-flex justify-content-between align-items-center mt-3 flex-wrap gap-2">
  <small class="text-muted">
    Menampilkan <strong><?= $from ?>–<?= $to ?></strong> dari <strong><?= number_format($total) ?></strong> data
  </small>
  <nav>
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buildUrl(1) ?>">&laquo;</a>
      </li>
      <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buildUrl($page - 1) ?>">‹</a>
      </li>
      <?php
        $start = max(1, $page - 2);
        $end   = min($totalPage, $page + 2);
        if ($end - $start < 4) {
            $start = max(1, $end - 4);
            $end   = min($totalPage, $start + 4);
        }
      ?>
      <?php if ($start > 1): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
      <?php endif; ?>
      <?php for ($i = $start; $i <= $end; $i++): ?>
        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
          <a class="page-link" href="<?= $buildUrl($i) ?>"><?= $i ?></a>
        </li>
      <?php endfor; ?>
      <?php if ($end < $totalPage): ?>
        <li class="page-item disabled"><span class="page-link">…</span></li>
      <?php endif; ?>
      <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buildUrl($page + 1) ?>">›</a>
      </li>
      <li class="page-item <?= $page >= $totalPage ? 'disabled' : '' ?>">
        <a class="page-link" href="<?= $buildUrl($totalPage) ?>">&raquo;</a>
      </li>
    </ul>
  </nav>
</div>
<?php endif; ?>