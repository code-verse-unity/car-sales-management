<?php
// $car can exists or not, depending on the controller that uses this view
?>

<form action="<?= "/cars/" . $car["id"] . "/edit" ?>" method="POST">
    <input type="hidden" name="_METHOD" value="PUT">

    <label for="name">Nom:</label>
    <input type="text" name="name" id="name" value="<?= $car["name"] ?>">

    <?php if (isset($car["errors"]["name"])): ?>
        <span style="color:red;">
            <?= $car["errors"]["name"][0] ?>
        </span>
    <?php endif; ?>

    <label for="inStock">En stock</label>
    <input type="number" name="inStock" id="inStock" value="<?= $car["inStock"] ?? "" ?>">

    <?php if (isset($car["errors"]["inStock"])): ?>
        <span style="color:red;">
            <?= $car["errors"]["inStock"][0] ?>
        </span>
    <?php endif; ?>

    <input type="submit" value="Save">
    <a href="<?= "/cars/" . $car["id"] ?>">Cancel</a>
</form>