<?php

?>

<form action="<?= "/clients/" . $client["id"] ?>" method="POST">
    <input type="hidden" name="_method" value="PUT">

    <!-- TODO add error state to the inputs if errors[<attribute>] exists -->

    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $client["name"] ?>">

    <?php if (isset($client["errors"]["name"])): ?>
        <!--
            here we just show the first error of the attribute name,
            but we can show all of them if necessary
        -->
        <span style="color:red;">
            <?= $client["errors"]["name"][0] ?>
        </span>
    <?php endif; ?>

    <label for="contact">Contact</label>
    <input type="text" name="contact" id="contact" value="<?= $client["contact"] ?>">

    <?php if (isset($client["errors"]["contact"])): ?>
        <span style="color:red;">
            <?= $client["errors"]["contact"][0] ?>
        </span>
    <?php endif; ?>

    <input type="submit" value="Save">
</form>