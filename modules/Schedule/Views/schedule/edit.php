<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>

    <?php echo $this->include('common/message') ?>

    <div class="card mb-4">
        <div class="card-body">

            <form action="<?php echo base_url(route_to('update-schedule', $schedule->id)) ?>" id="employee" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
                <?php echo $this->include('common/securityupdate') ?>

                <div class="col-lg-4 ">
                    <label for="start_time" class="form-label"> <?php echo lang("Localize.start") ?> <?php echo lang("Localize.time") ?> <abbr title="Required field">*</abbr></label>
                    <input type="text" id="start_time" name="start_time" class="form-control" value="<?php echo old('start_time') ?? $schedule->start_time ?>" placeholder="<?php echo lang("Localize.start") ?> <?php echo lang("Localize.time") ?>" required>
                </div>
                <div class="col-lg-4">
                    <label for="end_time" class="form-label"> <?php echo lang("Localize.end") ?> <?php echo lang("Localize.time") ?> <abbr title="Required field">*</abbr></label>
                    <input type="text" id="end_time" name="end_time" class="form-control" value="<?php echo old('end_time') ?? $schedule->end_time  ?>" placeholder="<?php echo lang("Localize.end") ?> <?php echo lang("Localize.time") ?>" required>
                </div>

                <br>
                <div class="col-lg-4 mt-5">
                    <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
                </div>

                <div class="text-danger">
                    <?php if (isset($validation)) : ?>
                        <?= $validation->listErrors(); ?>
                    <?php endif ?>
                </div>
            </form>
        </div>
    </div>
<?php echo $this->endSection() ?>