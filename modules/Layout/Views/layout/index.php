<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>
<?php echo $this->include('common/message') ?>

<div class="card mb-4">
    <div class="card-body">

        <?php //if ($add_data == true) : 
        ?>
        <div class="text-end">
            <a class="btn btn-success" href="<?php echo base_url(route_to('new-layout')) ?>">
                <i class="fas fa-chair"></i><sup><i class="fas fa-plus small"></i></sup>
                <?php echo lang("Localize.add") ?> <?php echo lang("Localize.layout") ?>
            </a>
        </div>
        <?php //endif 
        ?>

        <div class="table-responsive">
            <table class="table display table-bordered table-striped table-hover basic" id="fleetlist">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo lang("Localize.layout") ?> <?php echo lang("Localize.number") ?></th>
                        <th scope="col"><?php echo lang("Localize.car") ?> <?php echo lang("Localize.type") ?></th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.seat") ?></th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.row") ?> </th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.column") ?></th>
                        <th scope="col"><?php echo lang("Localize.status") ?> </th>
                        <th scope="col"><?php echo lang("Localize.action") ?> </th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 1; ?>
                    <?php foreach ($layout as $row) : ?>
                        <tr>
                            <th scope="row"><?php echo $i ?></th>
                            <td><?php echo $row->layout_number ?></td>
                            <td><?php echo $row->car_type ?></td>
                            <td><?php echo $row->total_seat ?></td>
                            <td><?php echo $row->total_row ?></td>
                            <td><?php echo $row->total_column ?></td>
                            <td><?php echo (
                                    $row->status == 1 ?
                                    '<span class="badge bg-success">' . lang("Localize.active") . '</span>' :
                                    '<span class="badge bg-danger">' . lang("Localize.disable") . '</span>'

                                ) ?></td>
                            <td>
                                <div class="btn-group" role="group" aria-label="Basic example">
                                    <?php //if ($edit_data == true) : 
                                    if ($row->layout_details_count == 0) :
                                    ?>

                                        <a href="<?= base_url(route_to('edit-layout', $row->id)) ?>" class="btn btn-sm btn-warning" title="<?php echo lang("Localize.edit") ?>" style="margin-right: 6px">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a class="btn btn-primary btn-sm" href="<?php echo base_url(route_to('new-layout-details', $row->id)) ?>">
                                            <i class="fas fa-plus mr-2"></i>
                                        </a>
                                    <?php //endif 
                                    endif
                                    ?>
                                    <?php //if ($edit_data == true) :
                                    if ($row->layout_details_count > 0) :
                                    ?>
                                        <a class="btn btn-primary btn-sm" href="<?php echo base_url(route_to('view-layout-details', $row->id)) ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php //endif
                                    endif
                                    ?>
                                </div>
                            </td>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach ?>


                </tbody>
            </table>
        </div>
    </div>
</div>
<?php echo $this->include('common/datatable_default_lang_change') ?>
<?php echo $this->include('common/confirmation-modal') ?>
<?php echo $this->endSection() ?>