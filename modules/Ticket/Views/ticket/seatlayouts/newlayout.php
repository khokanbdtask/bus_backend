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
        cursor: pointer;
    }
</style>
<?php
if ($seatRows['total_column'] == 5) {
    $width = '365px';
} elseif ($seatRows['total_column'] == 4) {
    $width = '300px';
} elseif ($seatRows['total_column'] == 3) {
    $width = '250px';
} elseif ($seatRows['total_column'] == 2) {
    $width = '200px';
} elseif ($seatRows['total_column'] == 1) {
    $width = '150px';
} else {
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
                max-width:  <?php echo $width ?>;
                background-color: #fff;
              " class="bus_seat_plan_table_wraper">
        <table style="width: 100%" class="bus_seat_plan_table">
            <input type="hidden" id="<?php echo 'seatnumber' . $subTripId; ?>" name="<?php echo 'seatnumber' . $subTripId; ?>[]" value="">
            <tbody>

                <?php foreach ($seatRows['rowData'] as $singleRow) : ?>
                    <tr>
                        <?php foreach ($singleRow['columns']  as $singleElem) :
                        ?>
                            <?php
                            $columnElement = $singleElem['column_element'];
                            $seatNo = $singleElem['seat_no'];
                            $seatLabelThumb = $singleElem['isBooked'] ? 'booked' : 'available';
                            ?>

                            <?php if ($columnElement == 'Driver') : ?>
                                <td class="position-relative">
                                    <img class="d-flex justify-content-end align-content-center" style="width: 40px; height: 40px" src="<?php echo base_url('public/image/steering.svg') ?>" alt="" />
                                </td>
                            <?php elseif ($columnElement == 'Blank') : ?>
                                <td></td>
                            <?php elseif ($columnElement == 'Passenger') : ?>
                                <td class="position-relative">
                                    <div class="seat-container" onclick="<?php echo "seacClick(this, $subTripId, '$seatLabelThumb');"; ?>" data-seatnumber="<?php echo $seatNo; ?>">
                                        <img class="d-flex justify-content-center align-items-center seat-image" src="<?php echo base_url("public/image/seat{$seatLabelThumb}.svg") ?>" alt="" />
                                        <span class="seat-number"><?php echo $seatNo; ?></span>
                                    </div>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach ?>


            </tbody>
        </table>
    </div>