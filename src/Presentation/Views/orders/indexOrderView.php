<?php

?>



<main class="py-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="fw-bold">Liste des <span class="text-orange">Achats</span> effectuées.</h1>
        <div>
            <button class="btn btn-primary" id='add-order'>Ajouter</button>
        </div>
    </div>

    <div class="py-4 px-2 my-2 border rounded-3 search-container">
        <form action="/orders" method="GET" id="search" class="container d-flex gap-3" novalidate>
            <div>
                <label for="startAt" class="py-1">Début</label>
                <input type="date" name="startAt" id="startAt" value="<?= $startAt ? $startAt->format("Y-m-d") : "" ?>" class="form-control">


            </div>

            <div>
                <label for="endAt" class="py-1">Fin</label>
                <input type="date" name="endAt" id="endAt" value="<?= $endAt ? $endAt->format("Y-m-d") : "" ?>" class="form-control">
            </div>

            <div class="align-self-end">
                <button type="submit" class="btn btn-primary">Rechercher</button>
            </div>

            <div class="align-self-end ml-2">
                <a href="/orders">
                    <button class="btn " type="button ">
                        Effacer
                    </button>
                </a>
            </div>
        </form>
        <div class="invalid-feedback container ">
            Veuillez spécifier une date de début et de fin.
        </div>
    </div>



    <script>
        // ! this script only works because the form's method is GET, so we can use window.location.href
        const startAt = document.getElementById("startAt");
        const endAt = document.getElementById("endAt");
        const form = document.getElementById("search");
        const feedback = document.getElementsByClassName('invalid-feedback');
        const basePath = "/orders";

        startAt.addEventListener('input', (e) => {
            if (e.target.value === '') {
                startAt.classList.add("is-invalid");
            } else {
                startAt.classList.remove("is-invalid");
                if (endAt.value !== '') {
                    endAt.classList.remove("is-invalid");
                    feedback[0].style.display = 'none';
                }
            }
        })


        endAt.addEventListener('input', (e) => {
            if (e.target.value === '') {
                endAt.classList.add("is-invalid");
            } else {
                endAt.classList.remove("is-invalid");
                if (startAt.value !== '') {
                    startAt.classList.remove("is-invalid");
                    feedback[0].style.display = 'none';
                }
            }
        })


        form.addEventListener("submit", (event) => {
            event.preventDefault();
            const startAtValue = startAt.value;
            const endAtValue = endAt.value;

            const inputs = form.querySelectorAll("input");
            Array.from(inputs).forEach(input => {
                if (input.value === '') {
                    input.classList.add("is-invalid");
                }
            })

            const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));

            if (hasError) {
                event.preventDefault();
                feedback[0].style.display = 'block';
            } else {
                if (startAtValue && endAtValue) {
                    const startAtDate = new Date(startAtValue);
                    const endAtDate = new Date(endAtValue);

                    if (startAtDate > endAtDate) {
                        event.preventDefault();
                        feedback[0].style.display = 'block';
                        feedback[0].innerHTML = "La date de début doit être inférieure à la date de fin.";

                        Array.from(inputs).forEach(input => {
                            console.log('called');
                            input.classList.add("is-invalid");
                        })
                        return;
                    }
                }


                feedback[0].style.display = 'none';
                let path = basePath + "?";

                if (startAtValue) {
                    path += `startAt=${startAtValue}&`;
                }

                if (endAtValue) {
                    path += `endAt=${endAtValue}&`;
                }

                window.location.href = path;
            }


        });
    </script>





    <?php if (empty($orders)) : ?>
        <?php if ($starAt || $endAt) : ?>
            <div class="d-flex justify-content-center p-5">
                <h3 class="text-center">Il n'y a pas d'achats correspondant à votre recherche.</h3>
            </div>
        <?php else : ?>
            <div class="d-flex justify-content-center p-5">
                <h3 class="text-center">Aucun achat n'a été enregistré.</h3>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="container">
            <div class="row pt-3">
                <div class="col-3">Identifiant</div>
                <div class="col">Nom du client</div>
                <div class="col">Voitures</div>
                <div class="col">Quantité</div>
                <div class="col d-flex justify-content-end">Actions</div>
            </div>
            <?php foreach ($orders as $order) : ?>
                <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
                    <div class="col-3 d-flex align-items-center align-self-start"><?= $order['id'] ?></div>
                    <div class="col d-flex align-items-center align-self-start fw-bold"><?= $order['client']['name'] ?></div>
                    <div class="col pt-2">
                        <?php foreach ($order['cars'] as $car) : ?>
                            <div class="mb-2"> <?= $car['name'] ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col pt-2">
                        <?php foreach ($order['quantities'] as $quantity) : ?>
                            <div class="mb-2"> <?= $quantity ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col d-flex justify-content-end align-self-start">
                        <a href="<?= "/orders/" . $order['id'] . "/edit" ?>">
                            <button class="btn btn-primary mx-3">
                                <img src="/assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
                            </button>
                        </a>

                        <button class="btn btn-danger">
                            <img src="/assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-end ">
                    <a href="<?= "/bills/" . $order['id'] . '/download' ?>" class="btn btn-link">
                        Télécharger la facture.
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>


</main>

<script>
    const addOrderButton = document.getElementById('add-order');
    addOrderButton.addEventListener('click', e => {
        console.log('c');
        window.location.href = '/orders/create';
    })
</script>