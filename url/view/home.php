<?php $this->view('head.php', null, CoreView::CORE); ?>

<div class="container">
  <div class="row m-5">
    <div class="col">
      <h3 class="border-bottom pb-3"><i class="bi bi-link mx-2 text-primary"></i> Home</h3>
      <?php if ($links): ?>
        <div class="row">
          <h5 class="mt-3">List of shortenen Links</h5>
          <ul class="list-group">
            <?php foreach ($links as $link): ?>
                <li class="list-group-item">
                  <a href=<?php echo $link->redirect_url ?> target="_blank" ><?php echo $link->display_url ?></a>
                </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php else: ?>
        <div class="alert alert-info">No Links found</div>
      <?php endif; ?>
    </div>
    <form id="kit-save-as-dialog" class="card d-none">
  <h6 class="card-header"><i class="bi bi-file-earmark-plus dialog-icon"></i>
    <span class="dialog-title">Save Kit
      As...</span></h6>
  <div class="card-body">
    <div class="row mb-1 align-items-center">
      <label for="input-title" class="col-sm-3 col-form-label">Title</label>
      <div class="col-sm-9">
        <input type="text" name="title" class="form-control form-control-sm input-title" id="input-title">
      </div>
    </div>
    <div class="row mb-1 align-items-center">
      <label for="input-fid" class="col-sm-3 col-form-label">Kit ID</label>
      <div class="col-sm-9">
        <div class="input-group">
          <input type="text" class="form-control input-fid form-control-sm" name="fid" id="input-fid" style="text-transform: lowercase;">
          <button class="bt-generate-fid btn btn-warning btn-sm"><i class="bi bi-magic"></i></button>
        </div>
      </div>
    </div>
  </div>
  <div class="card-footer">
    <div class="row">
      <div class="col text-end">
        <button class="bt-cancel btn btn-sm btn-secondary" style="min-width: 6rem;"><?php echo Lang::l('cancel'); ?></button>
        <button class="bt-save btn btn-sm btn-primary ms-1" style="min-width: 6rem;"><?php echo Lang::l('save'); ?></button>
      </div>
    </div>
  </div>
</form>
  </div>
</div>

<?php $this->view('foot.php', null, CoreView::CORE); ?>
