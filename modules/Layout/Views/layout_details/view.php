<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>
<?php echo $this->include('common/message') ?>

<style>
    .bus_seat_plan_table_wraper {
        position: relative;
    }

    /* 
    .bus_seat_plan_table_wraper::before {
        position: absolute;
        content: url("img/arrow-right.svg");
        width: 30px;

        top: 5px;
        left: -30px;
    } */

    .bus_seat_plan_table td,
    .bus_seat_plan_table th {
        padding: 8px 10px;
        text-align: center;
        /* border: 1px solid rgb(205, 205, 205); */
    }

    .seat-container {
        position: relative;
        width: 45px;
        /* Adjust as needed */
        height: 45px;
        /* Adjust as needed */
    }
    @media (max-width: 768px) {
        .seat-container {
        width: 30px;
        /* Adjust as needed */
        height: 30px;
        /* Adjust as needed */
    } 
    .seat-number {
        font-size: 10px!important;
    }
    .bus_seat_plan_table td,
    .bus_seat_plan_table th {
        padding: 8px 6px;
        /* border: 1px solid rgb(205, 205, 205); */
    }
    }

    .seat-image {
        width: 100%;
        height: 100%;
    }

    .seat-number {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        font-size: 13px;
        color: #000;
        /* Set the desired color */
        font-weight: bold;
    }
</style>

<div class="card mb-4">
    <div class="card-body">

        <div class="table-responsive">
            <table class="table display table-bordered table-striped table-hover basic">
                <thead>
                    <tr>
                        <th scope="col"><?php echo lang("Localize.layout") ?> <?php echo lang("Localize.number") ?></th>
                        <th scope="col"><?php echo lang("Localize.car") ?> <?php echo lang("Localize.type") ?></th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.seat") ?></th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.row") ?> </th>
                        <th scope="col"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.column") ?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $layout->layout_number ?></td>
                        <td><?php echo $layout->car_type ?></td>
                        <td><?php echo $layout->total_seat ?></td>
                        <td><?php echo $layout->total_row ?></td>
                        <td><?php echo $layout->total_column ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="table-responsive">
            <table class="table display table-bordered table-striped table-hover basic">
                <thead>
                    <tr>
                        <!-- <th scope="col">#</th> -->
                        <th scope="col"><?php echo lang("Localize.row") ?> <?php echo lang("Localize.no") ?></th>

                        <?php for ($col = 1; $col <= $layout->total_column; $col++) : ?>
                            <th scope="col"><?php echo lang("Localize.column") ?> <?php echo $col ?></th>
                        <?php endfor; ?>
                    </tr>
                </thead>
                <tbody>

                    <?php $i = 1; ?>
                    <?php foreach ($layout_details as $rowDetails) : ?>
                        <tr>
                            <!-- <th scope="row"><?php echo $i ?></th> -->
                            <td><?php echo $rowDetails->row_no ?></td>

                            <?php for ($col = 1; $col <= $layout->total_column; $col++) : ?>
                                <td>
                                    <?php
                                    $columnElement = 'column' . $col . '_element';
                                    $seatNo = 'seat_no' . $col;
                                    ?>
                                    <?php echo $rowDetails->$columnElement ?>
                                    <?php if (isset($rowDetails->$seatNo) && $rowDetails->$seatNo != '') : ?>
                                        <span class="badge bg-dark text-wrap">
                                            <?php echo ($rowDetails->$seatNo) ? " (Seat no: " . $rowDetails->$seatNo . ')' : ''  ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            <?php endfor; ?>

                        </tr>
                        <?php $i++; ?>
                    <?php endforeach ?>

                </tbody>
            </table>
        </div>

        <h6 class="fw-bold"><?php echo lang("Localize.layout") ?> <?php echo lang("Localize.representation") ?></h6>
        <div class="body-content">
            <!-- Bus seat layout -->
            <?php
            if($layout->total_column == 5){
                $width = '365px';
            }elseif ($layout->total_column == 4) {
                $width = '300px';
            }elseif ($layout->total_column == 3) {
                $width = '250px';
            }elseif ($layout->total_column == 2) {
                $width = '200px';
            }elseif ($layout->total_column == 1) {
                $width = '150px';
            }else{
                $width = '400px';
            }
            ?>
            <div style="
                border-width: 6px;
                border-style: solid;
                border-color: rgb(119, 119, 119) rgb(211 213 215)
                  rgb(195 195 195);

                width: 100%;
                height: 100%;
                margin: 0px auto;
                max-width: <?php echo $width; ?>;
              "
              
              
              class="bus_seat_plan_table_wraper">
                <table style="width: 100%" class="bus_seat_plan_table">
                    <tbody>

                        <?php $i = 1; ?>
                        <?php foreach ($layout_details as $rowDetails) : ?>
                            <tr>
                                <?php for ($col = 1; $col <= $layout->total_column; $col++) : ?>
                                    <?php
                                    $columnElement = 'column' . $col . '_element';
                                    $seatNo = 'seat_no' . $col;
                                    ?>

                                    <?php if ($rowDetails->$columnElement == 'Driver') : ?>
                                        <td class="position-relative">
                                            <img class="d-flex justify-content-end align-content-center" style="width: 40px; height: 40px" src="<?php echo base_url('public/image/steering.svg') ?>" alt="" />
                                        </td>
                                    <?php elseif ($rowDetails->$columnElement == 'Blank') : ?>
                                        <td></td>
                                    <?php elseif ($rowDetails->$columnElement == 'Passenger') : ?>
                                        <td class="position-relative">
                                            <div class="seat-container">
                                                <img class="d-flex justify-content-center align-items-center seat-image" src="<?php echo base_url('public/image/seatavailable.svg') ?>" alt="" />
                                                <span class="position-absolute top-50 left-50 translate-middle z-index-1 fs-13 pb-2"><?php echo $rowDetails->$seatNo ?></span>
                                            </div>
                                        </td>
                                    <?php endif; ?>

                                <?php endfor; ?>

                            </tr>
                            <?php $i++; ?>
                        <?php endforeach ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<?php echo $this->include('common/datatable_default_lang_change') ?>
<?php echo $this->include('common/confirmation-modal') ?>
<?php echo $this->endSection() ?>