<div class="row g-3">
    <div class="col-sm-6 col-md-12 col-lg-6 col-xl-3 d-flex">
        <div class="info-box d-flex position-relative rounded flex-fill w-100 overflow-hidden gradient-five">
            <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l3 opacity-3" style="width: 8rem; height: 8rem;"></div>
            <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l2 opacity-5" style="width: 6.5rem; height: 6.5rem;"></div>
            <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l1 opacity-5" style="width: 5rem; height: 5rem;"></div>
            <span class="info-box-icon d-flex align-self-center text-center">

                <img src="<?php echo base_url() . '/public/image/icone/img/icon/png/supplier.png' ?>" alt="" height="64" width="64">
            </span>
            <div class="info-box-content d-flex flex-column justify-content-center">
                <span class="info-box-text fw-bold fs-17px"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.trip") ?></span>
                <span class="info-box-number d-block fw-black counter"><?php echo $todaytrip ?></span>
                <div class="progress">
                    <div class="progress-bar" style="width: 70%"></div>
                </div>
                <div class="progress-description fs-14"><i class="fa fa-caret-down"></i> <?php echo lang("Localize.today") ?></div>
            </div>
            <!-- /.info-box-content -->
        </div>
        <!-- /.info-box -->
    </div>
    <!-- /.col -->
    <div class="col-sm-6 col-md-12 col-lg-6 col-xl-3 d-flex">
        <div class="info-box d-flex position-relative rounded flex-fill w-100 overflow-hidden gradient-six"">
                          <div class=" position-br mb-n5 mr-n5 radius-round bgc-purple-l3 opacity-3" style="width: 8rem; height: 8rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l2 opacity-5" style="width: 6.5rem; height: 6.5rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l1 opacity-5" style="width: 5rem; height: 5rem;"></div>
        <span class="info-box-icon d-flex align-self-center text-center">
            <img src="<?php echo base_url() . '/public/image/icone/img/icon/png/smartphone.png' ?>" alt="" height="64" width="64">

        </span>
        <div class="info-box-content d-flex flex-column justify-content-center">
            <span class="info-box-text fw-bold fs-17px"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.ticket_booking") ?></span>
            <span class="info-box-number d-block fw-black counter"><?php echo $todaybooking; ?> </span>
            <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
            </div>
            <div class="progress-description fs-14"><i class="fa fa-caret-down"></i> <?php echo lang("Localize.today") ?></div>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>

<div class="col-sm-6 col-md-12 col-lg-6 col-xl-3 d-flex">
    <div class="info-box d-flex position-relative rounded flex-fill w-100 overflow-hidden gradient-seven">
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l3 opacity-3" style="width: 8rem; height: 8rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l2 opacity-5" style="width: 6.5rem; height: 6.5rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l1 opacity-5" style="width: 5rem; height: 5rem;"></div>
        <span class="info-box-icon d-flex align-self-center text-center">
            <img src="<?php echo base_url() . '/public/image/icone/img/icon/png/choices.png' ?>" width="64">

        </span>
        <div class="info-box-content d-flex flex-column justify-content-center">
            <span class="info-box-text fw-bold fs-17px"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.booking") ?> <?php echo lang("Localize.amount") ?></span>
            <span class="info-box-number d-block fw-black counter"><?php echo $totalmoney; ?></span>
            <div class="progress">
                <div class="progress-bar" style="width: 70%"></div>
            </div>
            <div class="progress-description fs-14"><i class="fa fa-caret-down"></i> <?php echo lang("Localize.today") ?></div>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>
<!-- /.col -->
<div class="col-sm-6 col-md-12 col-lg-6 col-xl-3 d-flex">
    <div class="info-box d-flex position-relative rounded flex-fill w-100 overflow-hidden gradient-one">
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l3 opacity-3" style="width: 8rem; height: 8rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l2 opacity-5" style="width: 6.5rem; height: 6.5rem;"></div>
        <div class="position-br	mb-n5 mr-n5 radius-round bgc-purple-l1 opacity-5" style="width: 5rem; height: 5rem;"></div>
        <span class="info-box-icon d-flex align-self-center text-center">
            <img src=" <?php echo base_url() . '/public/image/icone/img/icon/png/choices.png' ?>" width="64">

        </span>
        <div class="info-box-content d-flex flex-column justify-content-center">
            <span class="info-box-text fw-bold fs-17px"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.passanger") ?></span>
            <span class="info-box-number d-block fw-black counter"><?php echo $totalpassanger; ?></span>
            <div class="progress">
                <div class="progress-bar w-75"></div>
            </div>
            <div class="progress-description fs-14"><i class="fa fa-caret-down"></i> <?php echo lang("Localize.today") ?></div>
        </div>
        <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
</div>

<?php if (count($driverAssignedListToday) > 0) :
    $sessiondata = \Config\Services::session();
?>

    <div class="card">
        <div class="card-header">
            <h6 class="fs-17 fw-semi-bold mb-0"><?php echo lang("Localize.today") ?><?php echo lang("Localize.trip") ?> <?php echo lang("Localize.list") ?> <span>(<?php echo $sessiondata->get('first_name'); ?>
                    <?php echo $sessiondata->get('last_name'); ?>)
                </span>
            </h6>

        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table display table-bordered table-striped table-hover basic">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col"><?php echo lang("Localize.pick_up") ?> </th>
                            <th scope="col"><?php echo lang("Localize.drop") ?> </th>
                            <th scope="col"><?php echo lang("Localize.journey") ?> <?php echo lang("Localize.hour") ?> </th>
                            <th scope="col"><?php echo lang("Localize.trip") ?> <?php echo lang("Localize.start") ?> <?php echo lang("Localize.date") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                        <?php
                        foreach ($driverAssignedListToday as $kye =>  $value) :
                        ?>
                            <tr class="gradient-six info-box fw-bold fs-17px">
                                <th scope="row"><?php echo $kye + 1; ?></th>
                                <td><?php echo  $value->pickup_location_name; ?></td>
                                <td><?php echo $value->drop_location_name; ?></td>
                                <td><?php echo $value->journey_hour; ?></td>
                                <td><?php echo date('Y-m-d', strtotime($value->startdate)); ?></td>
                            </tr>
                        <?php endforeach ?>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
<div class="card">
    <div class="card-header">
        <h6 class="fs-17 fw-semi-bold mb-0"><?php echo lang("Localize.available") ?> <?php echo lang("Localize.trip") ?> <?php echo lang("Localize.list") ?></h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table display table-bordered table-striped table-hover" id="triplist2">
                <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col"><?php echo lang("Localize.pick_up") ?> </th>
                        <th scope="col"><?php echo lang("Localize.drop") ?> </th>
                        <th scope="col"><?php echo lang("Localize.schedule") ?> </th>
                        <th scope="col"><?php echo lang("Localize.vehicle") ?> </th>
                        <th scope="col"><?php echo lang("Localize.trip") ?> <?php echo lang("Localize.start") ?> <?php echo lang("Localize.date") ?></th>
                        <th scope="col"><?php echo lang("Localize.action") ?></th>
                    </tr>
                </thead>
                <tbody>

                    <?php
                    foreach ($trip as $kye =>  $value) :
                        $this->db = \Config\Database::connect();
                        $builder = $this->db->table('locations');
                        $query = $builder->where('id', $value->pick_location_id)->get();
                        $locationName =  $query->getRow();

                        // drop location name
                        $dropquery   = $builder->where('id', $value->drop_location_id)->get();
                        $droplocationName =  $dropquery->getRow();
                    ?>
                        <tr>
                            <th scope="row"><?php echo $kye + 1; ?></th>
                            <td><?php echo  $locationName->name; ?></td>
                            <td><?php echo $droplocationName->name; ?></td>
                            <td><?php echo $value->start_time; ?> <?php echo $value->end_time; ?></td>
                            <td><?php echo $value->reg_no; ?></td>
                            <td><?php echo date('Y-m-d', strtotime($value->startdate)); ?></td>
                            <td>
                                <a onclick="assignDirver(<?php echo $value->tripid ?>)" class="btn btn-primary btn-sm"><i class="fas fa-user-plus"></i></a>
                                <a href="<?= base_url(route_to('show-driver-trip', $value->tripid)) ?>" class="btn btn-success btn-sm"><i class="fas fa-list"></i></a>
                            </td>
                        </tr>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="driverList" tabindex="-1" aria-labelledby="driverListLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.list") ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body driverDetails">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="assignDriver" tabindex="-1" aria-labelledby="assignDriverLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.list") ?> </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body assignDriverDetails">

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary saveBtn" data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>