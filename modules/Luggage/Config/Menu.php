<?php
$db = db_connect();
$websetting = $db->query('Select * from websettings')->getRow();
$db->close();
?>
<?php if ($websetting && $websetting->luggage_service == 1) : ?>
    <?php if ($rolepermissionLibrary->menu("luggage")) : ?>
        <li>
            <a class="has-arrow material-ripple" href="#">
                <i class="fas fa-suitcase"></i>
                <?php echo lang("Localize.luggage") ?>
            </a>

            <?php if ($rolepermissionLibrary->menu("luggage")) : ?>
                <!-- Front-end menu  -->
                <ul class="nav-second-level">
                    <?php if ($rolepermissionLibrary->read("luggage_setting") == true) : ?>
                        <li class="<?php echo $menuname == "luggage_setting" ? "mm-active" : ""  ?>">
                            <a href="<?php echo base_url(route_to('new-luggagesetting')) ?>">
                                <?php echo lang("Localize.settings") ?>
                            </a>
                        </li>
                    <?php endif ?>


                </ul>
            <?php endif ?>
        </li>
    <?php endif ?>
<?php endif ?>