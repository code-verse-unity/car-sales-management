<?php
// ! be aware, $order can exists or not, depending on the controller that uses this view
?>
<form action="<?= "/orders/create" ?>" method="POST">
    <label for="name">Client:</label>
    <select name="clientId">
        <?php foreach ($clients as $client): ?>
            <option value="<?= $client["id"] ?>" <?= $order["clientId"] === $client["id"] ? "selected" : "" ?>>
                <?= $client["name"] ?>
                <span> <!-- ? Maybe the id is not necessary -->
                    (
                    <?= $client["id"] ?>
                    )
                </span>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="name">Voiture:</label>
    <select name="carId">
        <?php foreach ($cars as $car): ?>
            <option value="<?= $car["id"] ?>" <?= $order["carId"] === $car["id"] ? "selected" : "" ?>>
                <?= $car["name"] ?>
                <span> <!-- TODO add color indicating the number in stock, like red < 10, yellow < 20, green otherwise -->
                    (
                    <?= $car["inStock"] ?> disponibles
                    )
                </span>
                <span><!-- ? Maybe the id is not necessary -->
                    (
                    <?= $car["id"] ?>
                    )
                </span>
            </option>
        <?php endforeach; ?>
    </select>

    <label for="quantity">Quantité</label>
    <input type="number" name="quantity" id="quantity" value="<?= $order["quantity"] ?? "" ?>">

    <?php if (isset($order["errors"]["quantity"])): ?>
        <span style="color:red;">
            <?= $order["errors"]["quantity"][0] ?>
        </span>
    <?php endif; ?>

    <input type="submit" value="Créer">
    <a href="/orders">Annuler</a>
</form>