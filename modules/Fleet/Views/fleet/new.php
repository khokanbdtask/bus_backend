<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>

    <?php echo $this->include('common/message') ?>

    <div class="card mb-4">
        <div class="card-body">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <form action="<?php echo base_url(route_to('create-fleet')) ?>" id="fleet" method="post" accept-charset="utf-8" enctype="multipart/form-data">
                        <?php echo $this->include('common/security') ?>
        
                        <div class="row">
                            <div class="col-md-6 col-lg-4">
                                <label for="fleettype" class="form-label"><?php echo lang("Localize.fleet") . " " . lang("Localize.type") ?> <abbr title="Required field">*</abbr></label>
                                <input type="text" placeholder="<?php echo lang("Localize.fleet") . " " . lang("Localize.type") ?>" name="type" value="<?php echo esc(old('type')) ?>" class="form-control" required />
                            </div>
            
                            <div class="col-md-6  col-lg-4">
                                <label for="layout" class="form-label"><?php echo lang("Localize.fleet") . " " . lang("Localize.layout") ?> <abbr title="Required field">*</abbr></label>
                                <select id="layout" class="form-select" name="layout" required="required" >
                                    <option value="" disabled selected><?php echo lang("Localize.seat") ?> <?php echo lang("Localize.type") ?> </option>
 
                                    <?php 
                                    
                                    foreach ($layout as $key => $value) {
                                        echo '<option value="'.$value->id.'">'.$value->layout_number.'</option>';
                                    }
                                    ?>
                                   
                                </select>
                            </div>
        
            
                            <div class="col-md-6  col-lg-4">
                                <label for="total_seat" class="form-label"><?php echo lang("Localize.total") ?> <?php echo lang("Localize.seat") ?>  <abbr title="Required field">*</abbr></label>
                                <input type="number" placeholder="<?php echo lang("Localize.total") ?> <?php echo lang("Localize.seat") ?>" name="total_seat" id="total_seat" value="<?php echo esc(old('type')) ?>" class="form-control" onkeyup="" onchange="" />
                            </div>
            
                            <div class="mb-3">
                                <label for="seat_number" class="form-label"><?php echo lang("Localize.seat") . " " . lang("Localize.number") ?> <abbr title="Required field">*</abbr></label>
                                <textarea class="form-control" name="seat_number" id="seat_number" rows="3" required></textarea>
                            </div>
            
                         
            
                            <div class="col-md-3">
                                <label class="form-group" for="">
                                    <?php echo lang("Localize.status") ?>
                                    <abbr title="Required field">*</abbr>
                                </label>
                                
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status" value="1" checked>
                                    <label class="form-check-label" for="exampleRadios1">
                                        <?php echo lang("Localize.active") ?>
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="status" id="status" value="0">
                                    <label class="form-check-label" for="exampleRadios2">
                                        <?php echo lang("Localize.disable") ?>
                                    </label>
                                </div>
                            </div>
                        </div>
        
                        <div class="text-danger">
                            <?php if (isset($validation)) : ?>
                                <?= $validation->listErrors(); ?>
                            <?php endif ?>
                        </div>
        
                        <div class="col-12 text-center">
                            <button type="submit" class="btn btn-success"><?php echo lang("Localize.submit") ?></button>
                        </div>
        
        
                    </form>
                </div>
            </div>


        </div>
    </div>
<?php echo $this->endSection() ?>

<?php echo $this->section('js') ?>
    <script src="<?php echo base_url('public/js/fleet.js'); ?>"></script>
<?php echo $this->endSection() ?>