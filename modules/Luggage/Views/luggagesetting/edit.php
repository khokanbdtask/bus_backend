<?php echo $this->extend('template/admin/main') ?>
<?php echo $this->section('content') ?>
<?php echo $this->include('common/message') ?>

<div class="card mb-4">
    <div class="card-body">

        <form action="<?php echo base_url(route_to('update-luggagesetting', $luggagesetting->id)) ?>" id="websettingedit" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
            <?php echo $this->include('common/securityupdate') ?>

            <div class="col-lg-12">
                <label for="free_luggage_kg" class=""><?php echo lang("Localize.free") ?> <?php echo lang("Localize.luggage") ?> (in kg)</label>
                <input type="number" step="0.01" id="free_luggage_kg" name="free_luggage_kg" value="<?php echo esc(old('free_luggage_kg')) ?? $luggagesetting->free_luggage_kg?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.free") ?> <?php echo lang("Localize.luggage") ?> (in kg)">
            </div>

            <div class="col-lg-6">
                <label for="paid_max_luggage_pcs" class=""><?php echo lang("Localize.paid") ?> <?php echo lang("Localize.luggage") ?> (max pcs)</label>
                <input type="number" id="paid_max_luggage_pcs" name="paid_max_luggage_pcs" value="<?php echo esc(old('paid_max_luggage_pcs')) ?? $luggagesetting->paid_max_luggage_pcs ?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.paid") ?> <?php echo lang("Localize.luggage") ?> (max pcs)">
            </div>

            <div class="col-lg-6">
                <label for="price_pcs" class=""> <?php echo lang("Localize.paid") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.price") ?> (per pcs)</label>
                <input type="number" step="0.01" id="price_pcs" name="price_pcs" value="<?php echo esc(old('price_pcs')) ?? $luggagesetting->price_pcs ?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.paid") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.price") ?> (per pcs)">
            </div>

            <div class="col-lg-6">
                <label for="special_max_luggage_pcs" class=""><?php echo lang("Localize.special") ?> <?php echo lang("Localize.luggage") ?> (max pcs)</label>
                <input type="number" id="special_max_luggage_pcs" name="special_max_luggage_pcs" value="<?php echo esc(old('special_max_luggage_pcs')) ?? $luggagesetting->special_max_luggage_pcs ?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.special") ?> <?php echo lang("Localize.luggage") ?> (max pcs)">
            </div>

            <div class="col-lg-6">
                <label for="special_price_pcs" class=""> <?php echo lang("Localize.special") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.price") ?> (per pcs)</label>
                <input type="number" step="0.01" id="special_price_pcs" name="special_price_pcs" value="<?php echo esc(old('special_price_pcs')) ?? $luggagesetting->special_price_pcs ?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.special") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.price") ?> (per pcs)">
            </div>

            <div class="col-lg-6">
                <label for="max_length" class=""> <?php echo lang("Localize.non") ?> <?php echo lang("Localize.standard") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.max") ?> <?php echo lang("Localize.length") ?> (in meter)</label>
                <input type="number" step="0.01" id="max_length" name="max_length" value="<?php echo esc(old('max_length')) ?? $luggagesetting->max_length?? '' ?>" class="form-control" placeholder=" <?php echo lang("Localize.non") ?>  <?php echo lang("Localize.standard") ?> <?php echo lang("Localize.luggage") ?>  (Max length)">
            </div>

            <div class="col-lg-6">
                <label for="max_weight" class=""> <?php echo lang("Localize.non") ?> <?php echo lang("Localize.standard") ?> <?php echo lang("Localize.luggage") ?> <?php echo lang("Localize.max") ?> <?php echo lang("Localize.weight") ?> (in kg)</label>
                <input type="number" step="0.01" id="max_weight" name="max_weight" value="<?php echo esc(old('max_weight')) ?? $luggagesetting->max_weight ?? '' ?>" class="form-control" placeholder="<?php echo lang("Localize.non") ?>  <?php echo lang("Localize.standard") ?> <?php echo lang("Localize.luggage") ?> (Max weight)">
            </div>

            <input type="hidden" id="baseurl" name="baseurl" value="<?php echo base_url(); ?>">

            <div class="text-danger">
                <?php if (isset($validation)) : ?>
                    <?= $validation->listErrors(); ?>
                <?php endif ?>
            </div>

            <br>
            <div class="col-12 text-center">
                <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
            </div>
        </form>
    </div>
</div>

<?php echo $this->endSection() ?>