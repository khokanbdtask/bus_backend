<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>

<?php echo $this->include('common/message') ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <form action="<?php echo base_url(route_to('update-layout', $layout->id)) ?>" id="layout" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
                    <?php echo $this->include('common/security') ?>

                    <div class="row">
                        <div class="col-6">
                            <label for="layout_number" class="form-label"><?php echo lang("Localize.layout") . " " . lang("Localize.type") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" placeholder="<?php echo lang("Localize.layout") . " " . lang("Localize.type") ?>" name="layout_number" value="<?php echo $layout->layout_number ?? esc(old('layout_number')) ?>" class="form-control" required />
                        </div>
                        <div class="col-6">
                            <label for="car_type" class="form-label"><?php echo lang("Localize.car") . " " . lang("Localize.type") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" placeholder="<?php echo lang("Localize.car") . " " . lang("Localize.type") ?>" name="car_type" value="<?php echo $layout->car_type ?? esc(old('car_type')) ?>" class="form-control" required />
                        </div>
                        <div class="col-3 mt-3">
                            <label for="total_seat" class="form-label"><?php echo lang("Localize.total") . " " . lang("Localize.seat") . " (" . lang("Localize.without") . " " . lang("Localize.driver") .")"?> <abbr title="Required field">*</abbr></label>
                            <input type="number" placeholder="<?php echo lang("Localize.total") . " " . lang("Localize.seat") ?>" name="total_seat" value="<?php echo $layout->total_seat ?? esc(old('total_seat')) ?>" class="form-control" required />
                        </div>
                        <div class="col-3 mt-3">
                            <label for="total_row" class="form-label"><?php echo lang("Localize.total") . " " . lang("Localize.row") ?> <abbr title="Required field">*</abbr></label>
                            <input type="number" placeholder="<?php echo lang("Localize.total") . " " . lang("Localize.row") ?>" name="total_row" value="<?php echo $layout->total_row ?? esc(old('total_row')) ?>" class="form-control" required />
                        </div>
                        <div class="col-3 mt-3">
                            <label for="total_column" class="form-label"><?php echo lang("Localize.total") . " " . lang("Localize.column") ?> <abbr title="Required field">*</abbr></label>
                            <select name="total_column" class="form-control" required>
                                <option value=""><?php echo lang("Localize.total") . " " . lang("Localize.column") ?></option>
                                <?php for ($i = 1; $i <= 5; $i++) : ?>
                                    <option value="<?php echo $i; ?>" <?php echo $layout->total_column == $i ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>

                        <div class="col-3 mt-3">
                            <label class="form-group" for="">
                                <?php echo lang("Localize.status") ?>
                            </label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status" value="1" <?php echo($layout->status == 1) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="exampleRadios1">
                                    <?php echo lang("Localize.active") ?>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status" value="0" <?php echo($layout->status == 0) ? 'checked' : ''; ?>>
                                <label class="form-check-label" for="exampleRadios2">
                                    <?php echo lang("Localize.in_active") ?>
                                </label>
                            </div>
                        </div>

                    </div>

                    <div class="text-danger">
                        <?php if (isset($validation)) : ?>
                            <?= $validation->listErrors(); ?>
                        <?php endif ?>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
                    </div>


                </form>
            </div>
        </div>


    </div>
</div>
<?php echo $this->endSection() ?>

<?php echo $this->section('js') ?>
<script src="<?php //echo base_url('public/js/fleet.js'); 
                ?>"></script>
<?php echo $this->endSection() ?>