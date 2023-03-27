<?php
// ! be aware, $car can exists or not, depending on the controller that uses this view
?>

<form action="<?= "/cars/create" ?>" method="POST">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $car["name"] ?? "" ?>">

    <?php if (isset($car["errors"]["name"])): ?>
        <span style="color:red;">
            <?= $car["errors"]["name"][0] ?>
        </span>
    <?php endif; ?>

    <label for="price">Prix</label>
    <input type="number" name="price" id="price" value="<?= $car["price"] ?? "" ?>">

    <?php if (isset($car["errors"]["price"])): ?>
        <span style="color:red;">
            <?= $car["errors"]["price"][0] ?>
        </span>
    <?php endif; ?>

    <label for="inStock">Stock</label>
    <input type="number" name="inStock" id="inStock" value="<?= $car["inStock"] ?? "" ?>">

    <?php if (isset($car["errors"]["inStock"])): ?>
        <span style="color:red;">
            <?= $car["errors"]["inStock"][0] ?>
        </span>
    <?php endif; ?>

    <input type="submit" value="Créer">
    <a href="/cars">Annuler</a>
</form>