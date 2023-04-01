<?php

?>

<main class="py-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="display-4 fw-bold">Liste des <span class="text-orange">Voitures</span>.</h1>
        <div>
            <button class="btn btn-primary" id='add-car'>Ajouter</button>
        </div>
    </div>

    <!-- Search functionality, already handled by IndexCarUseCase and IndexCarController -->
    <form action="/cars" class="container d-flex py-3 gap-3">
        <input type="search" name="name" id="id" placeholder="ex: Audi" value="<?= $nameQuery ?? "" ?>" class="form-control">

        <input type="submit" value="Rechercher" class="btn btn-primary">

        <a href="/cars">
            <button class="btn">
                Effacer
            </button>
        </a>
    </form>

    <div class="container">
        <div class="row pt-3">
            <div class="col">Désignation</div>
            <div class="col">Prix (Ar)</div>
            <div class="col">Nombre en stock</div>
            <div class="col d-flex justify-content-end">Actions</div>
        </div>

        <?php if (empty($cars)) : ?>
            <div class="d-flex justify-content-center p-5">
                <h3 class="text-center">Aucune voiture n'a été enregistrée.</h3>
            </div>
        <?php else : ?>
            <?php foreach ($cars as $car) : ?>
                <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
                    <div class="col d-flex align-items-center fw-bold"> <?= $car['name'] ?> </div>
                    <div class="col d-flex align-items-center"> <?= $car['price'] ?></div>
                    <div class="col d-flex align-items-center"> <?= $car['inStock'] ?> </div>
                    <div class="col d-flex justify-content-end">
                        <a href=<?= "/cars/" . $car['id'] . '/edit' ?>>
                            <button class="btn btn-primary mx-3" id='edit-btn'>
                                <img src="assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
                            </button>
                        </a>

                        <button class="btn btn-danger" id='delete-btn'>
                            <img src="assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

    </div>

</main>


<script>
    const addCarButton = document.getElementById('add-car');
    addCarButton.addEventListener('click', () => {
        window.location.href = '/cars/add';
    });
</script>