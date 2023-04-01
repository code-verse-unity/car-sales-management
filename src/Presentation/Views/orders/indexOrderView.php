<?php

?>


<script>
    // ! this script only works because the form's method is GET, so we can use window.location.href
    const startAt = document.getElementById("startAt");
    const endAt = document.getElementById("endAt");
    const form = document.getElementById("index-order-form");
    const basePath = "/orders";

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const startAtValue = startAt.value;
        const endAtValue = endAt.value;
        let path = basePath + "?";

        if (startAtValue) {
            path += `startAt=${startAtValue}&`;
        }

        if (endAtValue) {
            path += `endAt=${endAtValue}&`;
        }

        window.location.href = path;
    });
</script>

<!-- <?php if (count($orders) === 0) : ?>
    <p>
        <?php if ($starAt || $endAt) : ?>
            Il n'y a pas d'achats correspondant à votre recherche.
        <?php else : ?>
            Il n'y a pas encore d'achats effectués.
        <?php endif; ?>
    </p>
<?php else : ?>
    <ul>
        <?php foreach ($orders as $order) : ?>
            <li>
                <pre>
                            <?php print_r($order) ?>
                        </pre>

                <a href="<?= "/orders/" . $order["id"] ?>">Voir les détails</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?> -->

<main class="py-4">
    <div class="d-flex justify-content-between align-items-center">
        <h1 class="display-4 fw-bold">Liste des <span class="text-orange">Achats</span> effectuées.</h1>
        <div>
            <button class="btn btn-primary">Ajouter</button>
        </div>
    </div>
    <!-- Search functionality, already handled by IndexOrderUseCase and IndexOrderController -->
    <form action="/orders" method="GET" id="index-order-form" class="container d-flex py-3 gap-3">
        <div>
            <label for="startAt" class="py-1">Début</label>
            <input type="date" name="startAt" id="startAt" value="<?= $startAt ? $startAt->format("Y-m-d") : "" ?>" class="form-control">
        </div>

        <div>
            <label for="endAt" class="py-1">Fin</label>
            <input type="date" name="endAt" id="endAt" value="<?= $endAt ? $endAt->format("Y-m-d") : "" ?>" class="form-control">
        </div>

        <div class="align-self-end">
            <input type="submit" value="Rechercher" class="btn btn-primary">
        </div>

        <div class="align-self-end ml-2">
            <a href="/orders">
                <button class="btn">
                    Effacer
                </button>
            </a>
        </div>
    </form>

    <div class="container">
        <div class="row pt-3">
            <div class="col">Identifiant</div>
            <div class="col">Nom du client</div>
            <div class="col">Voitures</div>
            <div class="col">Quantité</div>
            <div class="col d-flex justify-content-end">Actions</div>
        </div>


        <?php if (empty($orders)) : ?>
            <div class="d-flex justify-content-center p-5">
                <h3 class="text-center">Aucun achat n'a été enregistré.</h3>
            </div>
        <?php else : ?>
            <?php foreach ($orders as $order) : ?>
                <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
                    <div class="col d-flex align-items-center fw-bold"><?= $order['id'] ?></div>
                    <div class="col d-flex align-items-center"><?= $order['client']['name'] ?></div>
                    <div class="col d-flex align-items-center">
                        <?php foreach ($order['cars'] as $car) : ?>
                            <div> <?= $car['name'] ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col d-flex align-items-center">
                        <?php foreach ($order['quantities'] as $quantity) : ?>
                            <div> <?= $quantity ?></div>
                        <?php endforeach; ?>
                    </div>
                    <div class="col d-flex justify-content-end">
                        <button class="btn btn-primary mx-3">
                            <img src="/assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
                        </button>
                        <button class="btn btn-danger">
                            <img src="/assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
                        </button>
                    </div>
                </div>
                <div class="d-flex justify-content-end ">
                    <button class="btn btn-link">Télécharger la facture.</button>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</main>