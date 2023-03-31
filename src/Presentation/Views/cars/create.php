<?php
// ! be aware, $car can exists or not, depending on the controller that uses this view
?>

<!-- <form action="<?= "/cars/create" ?>" method="POST">
    <label for="name">Name</label>
    <input type="text" name="name" id="name" value="<?= $car["name"] ?? "" ?>">

    <?php if (isset($car["errors"]["name"])) : ?>
        <span style="color:red;">
            <?= $car["errors"]["name"][0] ?>
        </span>
    <?php endif; ?>

    <label for="price">Prix</label>
    <input type="number" name="price" id="price" value="<?= $car["price"] ?? "" ?>">

    <?php if (isset($car["errors"]["price"])) : ?>
        <span style="color:red;">
            <?= $car["errors"]["price"][0] ?>
        </span>
    <?php endif; ?>

    <label for="inStock">Stock</label>
    <input type="number" name="inStock" id="inStock" value="<?= $car["inStock"] ?? "" ?>">

    <?php if (isset($car["errors"]["inStock"])) : ?>
        <span style="color:red;">
            <?= $car["errors"]["inStock"][0] ?>
        </span>
    <?php endif; ?>

    <input type="submit" value="Créer">
    <a href="/cars">Annuler</a>
</form> -->

<main class="container d-flex flex-column justify-content-center py-4 w-75">
    <h1 class="display-5 fw-bold">Ajouter une nouvelle <span class="text-orange">Voiture</span>.</h1>

    <form action="" class="d-flex flex-column gap-2 py-3">
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Désignation de la voiture</label>
            <input type="email" class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="Ferrari">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Prix en Ariary</label>
            <input type="number" class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="50000000">
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Nombre en Stock</label>
            <input type="number" class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="10">
        </div>
        <button class="btn btn-primary">
            Enregistrer
        </button>
    </form>

</main>