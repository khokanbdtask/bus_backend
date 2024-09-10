<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('css') ?>
<link rel="stylesheet" href="<?php echo base_url('public/css/customestyle.css'); ?>" type="text/css">
<?php echo $this->endSection() ?>

<?php echo $this->section('content') ?>

<?php echo $this->include('common/message') ?>

<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <?php foreach ($tags as $tag) { ?>
                <div class="col-4 mt-2">
                    <div class="bg-success text-white rounded p-3 shadow-sm text-center">
                        <div class="header-pretitle fs-15 font-weight-bold text-uppercase">User Info</div>
                        <span>-----------------------</span>

                        <!-- Replace the following with actual user information -->
                        <div class="fs-17"><?php echo $ticket->passengerName ?></div>
                        <div>Seat no : <?php echo $ticket->seatnumber ?></div>
                        <div>Mobile no : <?php echo $ticket->login_mobile ?></div>
                        <div>Email address : <?php echo $ticket->login_email ?></div>
                        <!-- End of user information -->

                        <hr class="my-3"> <!-- Horizontal line to separate user and booking info -->

                        <div class="header-pretitle fs-15 font-weight-bold text-uppercase">Booking Info</div>
                        <span>-----------------------</span>
                        <!-- Replace the following with actual booking information -->
                        <div class="fs-17">Booking No. <?php echo $ticket->booking_id ?></div>
                        <div>Tag no.: <?php echo $tag->tag ?></div>
                        <div>Date: <?php echo $ticket->journeydata ?></div>
                        <!-- End of booking information -->
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>







<?php echo $this->endSection() ?>


<?php echo $this->section('js') ?>

<?php echo $this->endSection() ?>