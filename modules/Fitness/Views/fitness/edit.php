<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>

<?php echo $this->include('common/message') ?>

<div class="card mb-4">
    <div class="card-body">
        <form action="<?php echo base_url(route_to('update-fitness', $fitness->id)) ?>" id="fitnessedit" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
            <?php echo $this->include('common/securityupdate') ?>

            <div class="row justify-content-center">


                <div class="col-lg-11 col-xxl-9">
                    <div class="row g-3">
                        <div class="col-md-6 col-lg-4">
                            <label for="fitness_name" class=""><?php echo lang("Localize.fitness") ?> <?php echo lang("Localize.name") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" id="fitness_name" name="fitness_name" value="<?php echo esc(old('fitness_name') ?? $fitness->fitness_name) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.fitness") ?> <?php echo lang("Localize.name") ?>" required>
                        </div>

                        <div class="col-md-6 col-lg-4" id="payment_method">
                            <label for="vehicle_id"><?php echo lang("Localize.vehicle") . " " . lang("Localize.reg") . " " . lang("Localize.no") ?> <abbr title="Required field">*</abbr></label>
                            <select class="form-select" name="vehicle_id" id="vehicle_id" required>

                                <?php foreach ($vehicle as $vehvalue) : ?>

                                    <?php if ($vehvalue->id == $fitness->vehicle_id) : ?>
                                        <option value="<?php echo $vehvalue->id ?>" selected><?php echo $vehvalue->reg_no ?></option>
                                    <?php else : ?>

                                        <option value="<?php echo $vehvalue->id ?>"><?php echo $vehvalue->reg_no ?></option>
                                    <?php endif ?>

                                <?php endforeach ?>

                            </select>
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="start_date" class="form-label"><?php echo lang("Localize.fitness") ?> <?php echo lang("Localize.start") ?> <?php echo lang("Localize.date") ?> <abbr title="Required field">*</abbr></label>
                            <div class="input-append date datepicker" id="start_date" data-date-format="yyyy-mm-dd">
                                <input size="16" type="text" name="start_date" class="form-control" value="<?php echo $fitness->start_date ?>" required readonly>
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>

                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="end_date" class="form-label"> <?php echo lang("Localize.fitness") ?> <?php echo lang("Localize.end") ?> <?php echo lang("Localize.date") ?></label>
                            <div class="input-append date datepicker" id="end_date" data-date-format="yyyy-mm-dd">
                                <input size="16" type="text" name="end_date" class="form-control" value="<?php echo $fitness->end_date ?>" required readonly>
                                <span class="add-on"><i class="icon-th"></i></span>
                            </div>
                        </div>


                        <div class="col-md-6 col-lg-4">
                            <label for="start_milage" class=""><?php echo lang("Localize.start") ?> <?php echo lang("Localize.milage") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" id="start_milage" name="start_milage" value="<?php echo esc(old('start_milage') ?? $fitness->start_milage) ?>" class="form-control text-capitalize" placeholder="Start Milage" required />
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="end_milage" class=""><?php echo lang("Localize.end") ?> <?php echo lang("Localize.milage") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" id="end_milage" name="end_milage" value="<?php echo esc(old('end_milage') ?? $fitness->end_milage) ?>" class="form-control text-capitalize" placeholder="End Milage" required />
                        </div>

                        <div class="col-md-6 col-lg-4">
                            <label for="total_milage" class=""><?php echo lang("Localize.total") ?> <?php echo lang("Localize.milage") ?> <abbr title="Required field">*</abbr></label>
                            <input type="text" id="total_milage" name="total_milage" value="<?php echo esc(old('total_milage') ?? $fitness->total_milage) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.total") ?> <?php echo lang("Localize.milage") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="tire_condition" class=""><?php echo lang("Localize.tire") ?> <?php echo lang("Localize.condition") ?> </label>
                            <input type="text" id="tire_condition" name="tire_condition" value="<?php echo esc(old('tire_condition') ?? $fitness->tire_condition) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.tire") ?> <?php echo lang("Localize.condition") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="windshield_washer_condition" class=""><?php echo lang("Localize.windshield") ?> <?php echo lang("Localize.washer") ?> <?php echo lang("Localize.condition") ?> </label>
                            <input type="text" id="windshield_washer_condition" name="windshield_washer_condition" value="<?php echo esc(old('windshield_washer_condition') ?? $fitness->windshield_washer_condition) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.windshield") ?> <?php echo lang("Localize.washer") ?> <?php echo lang("Localize.condition") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="windshield_condition" class=""><?php echo lang("Localize.windshield") ?> <?php echo lang("Localize.condition") ?> </label>
                            <input type="text" id="windshield_condition" name="windshield_condition" value="<?php echo esc(old('windshield_condition') ?? $fitness->windshield_condition) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.windshield") ?> <?php echo lang("Localize.condition") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="wiper_condition" class=""><?php echo lang("Localize.wiper") ?> <?php echo lang("Localize.condition") ?> </label>
                            <input type="text" id="wiper_condition" name="wiper_condition" value="<?php echo esc(old('wiper_condition') ?? $fitness->wiper_condition) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.wiper") ?> <?php echo lang("Localize.condition") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="overall_car_condition" class=""><?php echo lang("Localize.overall") ?> <?php echo lang("Localize.car") ?> <?php echo lang("Localize.condition") ?> </label>
                            <input type="text" id="overall_car_condition" name="overall_car_condition" value="<?php echo esc(old('overall_car_condition') ?? $fitness->overall_car_condition) ?>" class="form-control text-capitalize" placeholder="<?php echo lang("Localize.overall") ?> <?php echo lang("Localize.car") ?> <?php echo lang("Localize.condition") ?>" required>
                        </div>
                        <div class="col-md-6 col-lg-4" id="payment_method">
                            <label for="subtrip_id" class="form-label"><?php echo lang("Localize.sub") ?> <?php echo lang("Localize.trip") ?> <abbr title="Required field">*</abbr></label>
                            <select class="form-select" name="subtrip_id" id="subtrip_id" required>

                                <?php foreach ($subtrip as $stripval) : ?>

                                    <?php if ($stripval->id == $fitness->subtrip_id) : ?>
                                        <option value="<?php echo $stripval->id ?>" selected><?php echo $stripval->picklocation . "--" . $stripval->droplocation  ?></option>
                                    <?php else : ?>
                                        <option value="<?php echo $stripval->id ?>"><?php echo $stripval->picklocation . "--" . $stripval->droplocation  ?></option>
                                    <?php endif ?>

                                <?php endforeach ?>

                            </select>

                        </div>
                        <div class="col-md-6 col-lg-4">
                            <label for="driver" class="form-label"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.list") ?></label>

                            <select class="form-select" name="driver_id" id="driver_id">
                                <option value=""><?php echo lang("Localize.select") ?> <?php echo lang("Localize.driver") ?></option>
                                <?php foreach ($driver as $drivervalue) : ?>
                                    <option value="<?php echo $drivervalue->id ?>" <?php if ($drivervalue->id == $fitness->driver_id) {
                                                                                        echo 'selected';
                                                                                    } ?>><?php echo $drivervalue->first_name ?> <?php echo $drivervalue->last_name ?></option>
                                <?php endforeach ?>

                            </select>



                        </div>
                        <div class="col-12 mt-3">
                            <label for="remarks" class=""><?php echo lang("Localize.remarks") ?></label>
                            <textarea class="form-control" name="remarks" id="remarks" rows="3" required><?php echo old('remarks') ?? $fitness->remarks ?></textarea>
                        </div>

                        <div class="text-danger">
                            <?php if (isset($validation)) : ?>
                                <?= $validation->listErrors(); ?>
                            <?php endif ?>
                        </div>

                    </div>

                    <br>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
                    </div>

                </div>



            </div>
        </form>

    </div>
</div>
<?php echo $this->endSection() ?>