<?php echo $this->extend('template/admin/main') ?>

<?php echo $this->section('content') ?>

<?php echo $this->include('common/message') ?>
<style>
    .cardd {
        border: 0;
        box-shadow: 0 .125rem .75rem rgba(25, 135, 84, .2)
    }

    .cardd-body {
        padding: 1.5rem;
    }
</style>
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <form action="<?php echo base_url(route_to('create-layout-details')) ?>" id="layout_details" method="post" class="row g-3" accept-charset="utf-8" enctype="multipart/form-data">
                    <?php echo $this->include('common/security') ?>
                    <input type="hidden" name="layout_id" value="<?php echo $layout->id ?>">
                    <h3><?php echo lang('Layout - ') . $layout->layout_number  ?></h3>

                    <?php for ($row = 1; $row <= $layout->total_row; $row++) : ?>
                        <div class="mt-4">
                            <div class="cardd">
                                <div class="cardd-body">
                                    <div class="row">
                                        <?php for ($column = 1; $column <= $layout->total_column; $column++) : ?>
                                            <div class="col-md-4">
                                                <b><label for="row_<?php echo $row ?>_col_<?php echo $column ?>">Row <?php echo $row ?>, Column <?php echo $column ?> </label><abbr title="Required field">*</abbr></b>
                                                <select name="columns[<?php echo $row ?>][<?php echo $column ?>]" id="row_<?php echo $row ?>_col_<?php echo $column ?>" class="form-control seat-selector">
                                                    <?php foreach ($seat_element as $element) : ?>
                                                        <option value="<?php echo $element->id ?>" data-element-id="<?php echo $element->id ?>"><?php echo $element->element ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                                <!-- Add another input field for seat_no -->
                                                <input type="text" name="seat_no[<?php echo $row ?>][<?php echo $column ?>]" id="seat_no_<?php echo $row ?>_col_<?php echo $column ?>" class="form-control additional-field mt-2" placeholder="Seat No" style="display: none;">
                                            </div>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endfor; ?>

                    <div class="text-danger">
                        <?php if (isset($validation)) : ?>
                            <?= $validation->listErrors(); ?>
                        <?php endif; ?>
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
<script>
    $(document).ready(function() {
        // Set the values of all input elements within the form to an empty string
        document.getElementById('layout_details').reset();

    });

    document.addEventListener('DOMContentLoaded', function() {
        var seatSelectors = document.querySelectorAll('.seat-selector');

        seatSelectors.forEach(function(selector) {
            selector.addEventListener('change', function() {
                var selectedOption = this.options[this.selectedIndex];
                var additionalField = this.parentNode.querySelector('.additional-field');

                // Check if element_id is 2
                if (selectedOption.getAttribute('data-element-id') == 2) {
                    additionalField.style.display = 'block';

                    // Generate and set auto seat number based on row and column
                    var row = this.id.split('_')[1]; // Extract the row number from the element's ID
                    var column = this.id.split('_')[3]; // Extract the column number from the element's ID
                    var seatNumber = 's' + row + column;
                    additionalField.value = seatNumber;
                } else {
                    additionalField.style.display = 'none';
                    additionalField.value = '';
                }
            });
        });
    });
</script>
<?php echo $this->endSection() ?>