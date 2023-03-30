<?php

?>

<!-- Search functionality, already handled by IndexCarUseCase and IndexCarController -->
<!-- <form action="/cars">
    <input type="search" name="name" id="id" value="<?= $nameQuery ?? "" ?>">

    <input type="submit" value="Search">

    <a href="/cars">Effacer</a>
</form>

<?php if (count($cars) === 0) : ?>
    <p>
        <?php if ($nameQuery) : ?>
            Il n'y a pas de voitures correspondant à votre recherche.
        <?php else : ?>
            Il n'y a pas encore de voitures.
        <?php endif; ?>
    </p>
<?php else : ?>
    <ul>
        <?php foreach ($cars as $car) : ?>
            <li>
                <pre>
                    <?php
                    print_r($car);
                    ?>
                </pre>

                <a href="<?= "/cars/" . $car["id"] ?>">Voir les détails</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?> -->

<?php

?>

<main class="py-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="display-4 fw-bold">Liste des <span class="text-orange">Voitures</span>.</h1>
        <div>
            <button class="btn btn-primary">Ajouter</button>
        </div>
    </div>

    <div class="container">
        <div class="row pt-3">
            <div class="col">Désignation</div>
            <div class="col">Prix (Ar)</div>
            <div class="col">Nombre en stock</div>
            <div class="col d-flex justify-content-end">Actions</div>
        </div>

        <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
            <div class="col d-flex align-items-center fw-bold">Ferrari M22</div>
            <div class="col d-flex align-items-center">82 000 000</div>
            <div class="col d-flex align-items-center">8</div>
            <div class="col d-flex justify-content-end">
                <button class="btn btn-primary mx-3">
                    <img src="assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
                </button>
                <button class="btn btn-danger">
                    <img src="assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
                </button>
            </div>
        </div>
    </div>

</main>