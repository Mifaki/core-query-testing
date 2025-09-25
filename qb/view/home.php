<?php $this->view('head.php', null, CoreView::CORE); ?>

<div class="container">
  <div class="row m-5">
    <div class="col">
      <h3 class="border-bottom pb-3"><i class="bi bi-speedometer mx-2 text-primary"></i> Home</h3>
      <?php if ($posts): ?>
        <div class="row">
            <?php foreach ($posts as $post): ?>
              <div class="col-md-4 mb-3">
                <div class="card">
                  <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars(is_object($post) ? ($post->name ?? 'No Title') : ($post['name'] ?? 'No Title')) ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars(is_object($post) ? ($post->description ?? 'No Description') : ($post['description'] ?? 'No Description')) ?></p>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No posts found</div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php $this->view('foot.php', null, CoreView::CORE); ?>
