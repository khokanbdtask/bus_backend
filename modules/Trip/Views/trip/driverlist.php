<div class="table-responsive">
    <table class="table display table-bordered table-striped table-hover basic" id="triplist">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.name") ?></th>
                <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.mobile") ?></th>
                <th scope="col"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.email") ?></th>
                <th scope="col"><?php echo lang("Localize.from") ?></th>
                <th scope="col"><?php echo lang("Localize.to") ?></th>
                <th scope="col"><?php echo lang("Localize.approved") ?> <?php echo lang("Localize.by") ?></th>
                <th scope="col"><?php echo lang("Localize.status") ?></th>
                <?php
                if ($employee_type != 1) :

                ?>
                    <th scope="col"><?php echo lang("Localize.action") ?></th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>

            <?php
            foreach ($driverList as $key => $value) :
            ?>
                <tr>
                    <th scope="row"><?php echo $key + 1; ?></th>
                    <td><?php echo $value->emp_first_name . " " . $value->emp_last_name; ?></td>
                    <td><?php echo $value->phone; ?></td>
                    <td><?php echo $value->email; ?></td>
                    <td><?php echo $value->start_date; ?></td>
                    <td><?php echo $value->end_date; ?></td>
                    <td><?php echo $value->approve_by_firstname . " " . $value->approve_by_lastname; ?></td>
                    <td><?php if ($value->is_approved == 0) {
                            echo "Not Approved";
                        } else {
                            echo "Approved";
                        }  ?>
                    </td>

                    <?php
                    if ($employee_type != 1) :
                    ?>
                        <td>
                            <?php if ($value->is_approved == 0) { ?>
                                <a onclick="approveDriver('<?php echo $value->s_id ?>')" class="btn btn-primary btn-sm"><i class="fas fa-check-square"></i> </a>
                            <?php } ?>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>
</div>