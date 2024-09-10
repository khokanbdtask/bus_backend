<form action="<?php echo base_url(route_to('add-driver-trip')) ?>" id="addDriverToTrip" method="post" class="addDriverToTripForm">
    <?php echo $this->include('common/security') ?>
    <input type="hidden" name="tripId" value="<?php echo $tripId ?>">
    <div class="col-12">
        <label for="driver" class="form-label"><?php echo lang("Localize.driver") ?> <?php echo lang("Localize.list") ?> <abbr title="Required field">*</abbr></label>
        <select name="driver" class="testselect3" required>
            <option value="" disabled selected><?php echo lang("Localize.none") ?></option>

            <?php foreach ($driver as $drivervalue) : ?>
                <option value="<?php echo $drivervalue->id ?>"><?php echo $drivervalue->first_name ?> <?php echo $drivervalue->last_name ?></option>
            <?php endforeach ?>
        </select>
    </div>

    <div class="col-12 mt-2">
        <label for="startdate" class="form-label"><?php echo lang("Localize.start") ?> <?php echo lang("Localize.date") ?> <abbr title="Required field">*</abbr></label>
        <div class="input-group">
            <input type="date" class="form-control" name="start_date" id="start_date" required />
        </div>
    </div>

    <div class="col-12 mt-2">
        <label for="startdate" class="form-label"><?php echo lang("Localize.end") ?> <?php echo lang("Localize.date") ?> <abbr title="Required field">*</abbr></label>
        <div class="input-group">
            <input type="date" class="form-control" name="end_date" id="end_date" required />
        </div>
    </div>
</form>
<script>
    $('.testselect3').SumoSelect();
    $(function() {
        var dtToday = new Date();

        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();

        var maxDate = year + '-' + month + '-' + day;
        $('#start_date').attr('min', maxDate);
        $('#end_date').attr('min', maxDate);
    });
</script>