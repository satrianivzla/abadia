<?php if (isset($breadcrumbs) && is_array($breadcrumbs) && !empty($breadcrumbs)): ?>
<nav aria-label="breadcrumb" style="--bs-breadcrumb-divider: '>';">
  <ol class="breadcrumb">
    <?php foreach ($breadcrumbs as $i => $breadcrumb): ?>
      <?php if ($i == count($breadcrumbs) - 1): ?>
        <li class="breadcrumb-item active" aria-current="page">
          <?php echo html_escape($breadcrumb['label']); ?>
        </li>
      <?php else: ?>
        <li class="breadcrumb-item">
          <a href="<?php echo site_url($breadcrumb['url']); ?>">
            <?php echo html_escape($breadcrumb['label']); ?>
          </a>
        </li>
      <?php endif; ?>
    <?php endforeach; ?>
  </ol>
</nav>
<?php endif; ?>
