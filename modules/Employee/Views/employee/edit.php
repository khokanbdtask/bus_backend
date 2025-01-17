<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>
    <?php echo $this->include('common/message') ?>

    <div class="card mb-4">
        <div class="card-body">
            <form action="<?php echo base_url(route_to('update-employee', $employee->id)) ?>" id="employee" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
                <?php echo $this->include('common/securityupdate') ?>

                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="row">
                            <div class="col-lg-3 mt-3">
                                <label for="first_name"><?php echo lang("Localize.first_name") ?> <abbr title="Required field">*</abbr></label>
                                <input type="text" name="first_name" class="form-control" value="<?php echo old('first_name') ?? $employee->first_name ?>" placeholder="<?php echo lang("Localize.city_name") ?>" required>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="last_name"><?php echo lang("Localize.last_name") ?> <abbr title="Required field">*</abbr></label>
                                <input type="text" name="last_name" class="form-control" value="<?php echo old('last_name') ?? $employee->last_name ?>" placeholder="<?php echo lang("Localize.last_name") ?>" required>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="employeetype_id"><?php echo lang("Localize.employee") ?> <?php echo lang("Localize.type") ?> <abbr title="Required field">*</abbr></label>
                                <select class="form-select" name="employeetype_id" id="employeetype_id" required>

                                    <?php foreach ($employeetype as $employeetypevalue) : ?>
                                        <?php if ($employeetypevalue->id == $employee->employeetype_id) : ?>
                                            <option value="<?php echo $employeetypevalue->id ?>" selected><?php echo $employeetypevalue->type ?></option>
                                        <?php else : ?>
                                            <option value="<?php echo $employeetypevalue->id ?>"><?php echo $employeetypevalue->type ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>

                                </select>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="phone"><?php echo lang("Localize.mobile") ?> <abbr title="Required field">*</abbr></label>
                                <input type="number" name="phone" class="form-control" value="<?php echo old('phone') ?? $employee->phone ?>" placeholder="<?php echo lang("Localize.mobile") ?>" aria-label="phone" required>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="email"><?php echo lang("Localize.email") ?> <abbr title="Required field">*</abbr></label>
                                <input type="email" name="email" class="form-control" value="<?php echo old('email') ?? $employee->email ?>" placeholder="<?php echo lang("Localize.email") ?>" aria-label="Email" required>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="blood"><?php echo lang("Localize.blood") ?></label>
                                <input type="text" name="blood" class="form-control" value="<?php echo old('blood') ?? $employee->blood ?>" placeholder="<?php echo lang("Localize.blood") ?>" aria-label="blood">
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="nid"><?php echo lang("Localize.id_type") ?></label>
                                <input type="text" name="id_type" class="form-control" value="<?php echo old('id_type') ?? $employee->id_type ?>" placeholder="<?php echo lang("Localize.id_type") ?>">
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="nid"><?php echo lang("Localize.nid_passport_number") ?></label>
                                <input type="text" name="nid" class="form-control" value="<?php echo old('nid') ?? $employee->nid ?>" placeholder="<?php echo lang("Localize.nid_passport_number") ?>" aria-label="Nid/Passport Number">
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="country_id"><?php echo lang("Localize.country_name") ?> <abbr title="Required field">*</abbr></label>
                                
                                <select class="form-select" name="country_id" id="country_id" required>
                                    <?php foreach ($country as $countryvalue) : ?>
                                        <?php if ($countryvalue->id == $employee->country_id) : ?>
                                            <option value="<?php echo $countryvalue->id ?>" selected><?php echo $countryvalue->name ?></option>
                                        <?php else : ?>
                                            <option value="<?php echo $countryvalue->id ?>"><?php echo $countryvalue->name ?></option>
                                        <?php endif ?>
                                    <?php endforeach ?>

                                </select>
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="city"><?php echo lang("Localize.city_name") ?></label>
                                <input type="text" name="city" class="form-control" value="<?php echo old('city') ?? $employee->city ?>" placeholder="<?php echo lang("Localize.city_name") ?>" aria-label="City Name">
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="zip"><?php echo lang("Localize.zip_code") ?></label>
                                <input type="number" name="zip" step="1" class="form-control" value="<?php echo old('zip') ?? $employee->zip ?>" placeholder="<?php echo lang("Localize.zip_code") ?>" aria-label="Zip Code">
                            </div>

                            <div class="col-12 mt-3">
                                <label for="address"><?php echo lang("Localize.address") ?> <abbr title="Required field">*</abbr></label>
                                <textarea class="form-control" name="address" id="address" rows="3" required><?php echo old('address') ?? $employee->address ?></textarea>
                            </div>


                            <div class="col-lg-3 mt-3">
                                <label for="documentedit" class="form-label"><?php echo lang("Localize.nid_passport_image") ?></label>
                                <div id="documentedit">

                                </div>
                                <input type="hidden" id="documentoldpic" name="documentoldpic" value="<?php echo $employee->nid_picture ?>">
                            </div>

                            <div class="col-lg-3 mt-3">
                                <label for="document" class="form-label"><?php echo lang("Localize.profile_image") ?></label>
                                <div id="profileedit">

                                </div>
                                <input type="hidden" id="profileoldpic" name="profileoldpic" value="<?php echo $employee->profile_picture ?>">
                            </div>
                        </div>

                        <input type="hidden" id="baseurl" name="baseurl" value="<?php echo base_url(); ?>">
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php echo $this->endSection() ?>