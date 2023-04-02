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
    <form action="/cars" class="container d-flex py-3 gap-3" id='search'>
        <div class="flex-grow-1">
            <input type="search" name="name" id="searchInput" placeholder="ex: Audi" value="<?= $nameQuery ?? "" ?>" class="form-control">
            <div class="invalid-feedback">
                Veuillez spécifier un paramètre de recherche.
            </div>
        </div>

        <div>
            <button type="submit" class="btn btn-primary">
                Rechercher
            </button>
        </div>


        <a href="/cars">
            <button class="btn" type='button'>
                Effacer
            </button>
        </a>
    </form>



    <?php if (empty($cars)) : ?>
        <div class="d-flex justify-content-center p-5">
            <h3 class="text-center">Aucune voiture n'a été enregistrée.</h3>
        </div>
    <?php else : ?>
        <div class="container">
            <div class="row pt-3">
                <div class="col">Désignation</div>
                <div class="col">Prix (Ar)</div>
                <div class="col">Nombre en stock</div>
                <div class="col d-flex justify-content-end">Actions</div>
            </div>
            <?php foreach ($cars as $car) : ?>
                <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
                    <div class="col d-flex align-items-center fw-bold"> <?= $car['name'] ?> </div>
                    <div class="col d-flex align-items-center"> <?= $car['price'] ?></div>
                    <div class="col d-flex align-items-center"> <?= $car['inStock'] ?> </div>
                    <div class="col d-flex justify-content-end">
                        <button class="btn btn-primary mx-3">
                            <img src="assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
                        </button>
                        <button class="btn btn-danger">
                            <img src="assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</main>


<script>
    const addClientButton = document.getElementById('add-car');
    addClientButton.addEventListener('click', () => {
        window.location.href = '/cars/add';
    });

    const form = document.getElementById('search');
    const searchInput = document.getElementById('searchInput');

    searchInput.addEventListener('input', (e) => {
        if (e.target.value === '') {
            searchInput.classList.add("is-invalid");
        } else {
            searchInput.classList.remove("is-invalid");
        }
    })

    form.addEventListener("submit", (event) => {
        event.preventDefault();

        const inputs = form.querySelectorAll("input");
        Array.from(inputs).forEach(input => {
            if (input.value === '') {
                input.classList.add("is-invalid");
            }
        })

        const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));

        if (hasError) {
            event.preventDefault();
            return;
        }

        console.log(searchInput);
        window.location.href = "/cars?name=" + searchInput.value;
    });
</script>