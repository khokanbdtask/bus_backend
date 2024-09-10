<?php echo $this->extend('template/admin/main') ?>

<?php
$local_session = \Config\Services::session(); // Needed for Point 5
?>

<?php echo $this->section('content') ?>
<?php echo $this->include('common/message') ?>



<?php echo $this->include('common/chart/chart_driver') ?>
<input type="hidden" id="baseUrl" name="baseUrl" value="<?php echo base_url() ?>">
<?php echo $this->include('common/datatable_default_lang_change') ?>
<?php echo $this->endSection() ?>


<?php echo $this->section('js') ?>

<script script src="<?php echo base_url('public/js/trips.js'); ?>"></script>


<?php echo $this->endSection() ?>