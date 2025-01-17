<?php echo $this->extend('template/admin/main') ?>
	<?php echo $this->section('content') ?>
	<?php echo $this->include('common/message') ?>

	<div class="card mb-4">
      <div class="card-body">

		<form action="<?php echo base_url(route_to('create-section-two'))?>" id="sectiontwo" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
					<?php echo $this->include('common/security') ?>
				
		
				<div class="col-md-4">
				<label for="title"><?php echo lang("Localize.title") ?></label>
					<input type="text"  name ="title" value="<?php echo $secTwo->title ?? esc(old('title')) ?>" class="form-control text-capitalize">
				</div>
				<div class="col-md-4">
				<label for="sub_title"><?php echo lang("Localize.sub") ?> <?php echo lang("Localize.title") ?></label>
					<input type="text"  name ="sub_title" value="<?php  echo $secTwo->sub_title ?? esc(old('sub_title')) ?>" class="form-control text-capitalize">
				</div>

							<div class="col-4">
								<br>
								<button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
							</div>
				
							<div class="text-danger">
									<?php if (isset($validation)): ?>
									<?=$validation->listErrors();?>
									<?php endif?>
								</div>
	</form>
</div>
</div>
<?php echo $this->endSection() ?>