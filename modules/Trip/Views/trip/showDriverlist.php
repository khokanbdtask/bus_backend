<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('css') ?>
<link rel="stylesheet" href="<?php echo base_url('public/css/customestyle.css'); ?>" type="text/css">
<?php echo $this->endSection() ?>

<?php echo $this->section('content') ?>

<?php echo $this->include('common/message') ?>
<?php 
    $sessiondata = \Config\Services::session();
    $employee_id = $sessiondata->get('employee_id');
?>

<div class="card mb-4">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table display table-bordered table-striped table-hover basic" id="triplist2">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.name") ?></th>
                        <?php
                        if ($employee_type != 1) :
                        ?>
                            <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.mobile") ?></th>
                            <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.email") ?></th>
                        <?php endif; ?>
                        <th scope="col"><?php echo lang("Localize.from") ?></th>
                        <th scope="col"><?php echo lang("Localize.to") ?></th>
                        <th scope="col"><?php echo lang("Localize.approved") ?> <?php echo lang("Localize.by") ?></th>
                        <th scope="col"><?php echo lang("Localize.status") ?></th>
                        <th scope="col"><?php echo lang("Localize.action") ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($driverList as $key => $value) :
                    ?>
                        <tr>
                            <th scope="row"><?php echo $key + 1; ?></th>
                            <td><?php echo $value->emp_first_name . " " . $value->emp_last_name; ?></td>
                            <?php
                            if ($employee_type != 1) :
                            ?>
                                <td><?php echo $value->phone; ?></td>
                                <td><?php echo $value->email; ?></td>
                            <?php endif; ?>
                            <td><?php echo $value->start_date; ?></td>
                            <td><?php echo $value->end_date; ?></td>
                            <td><?php echo $value->approve_by_firstname . " " . $value->approve_by_lastname; ?></td>
                            <td><?php if ($value->is_approved == 0) {
                                    echo "Not Approved";
                                } else {
                                    echo "Approved";
                                }  ?>
                            </td>


                            <td>
                                <?php if ($value->is_approved == 0 && $employee_type != 1) { ?>
                                    <a onclick="approveDriver('<?php echo $value->s_id ?>')" class="btn btn-primary btn-sm"><i class="fas fa-check-square"></i> </a>
                                <?php } ?>
                                <?php if (($employee_type == 1 && $employee_id == $value->employee_id) || $employee_type != 1) { ?>
                                <a onclick="deleteDriver('<?php echo $value->s_id ?>')" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo base_url() ?>">
<input type="hidden" id="csrf" name="csrf" value="<?php echo csrf_hash(); ?>">
<?php echo $this->include('common/datatable_default_lang_change') ?>
<?php echo $this->endSection() ?>

<?php $this->section('js') ?>
<script src="<?php echo base_url('public/js/trips.js'); ?>"></script>
<?php $this->endSection() ?>