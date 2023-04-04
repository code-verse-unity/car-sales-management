<?php

?>

<!-- <pre>
    <?php print_r($client); ?>
    <hr>
    <?php print_r($orders); ?>
</pre> -->

<main class="container py-5">
    <div class="row">
        <div class="col">
            <div class="d-flex flex-column gap-2">
                <h3><span class="text-orange">Information</span> générale</h3>

                <div class="mb-3">
                    <div class="mb-3">Identifiant</div>
                    <div class="bg-white rounded-3 p-3">
                        <?= $client['id'] ?>
                    </div>
                </div>

                <div class="row mb-2 d-flex justify-content-between">
                    <div class="col-7">Nom et prénoms</div>
                    <div class="col">Contact</div>
                </div>

                <div class="row mb-2 d-flex justify-content-between bg-white rounded-3 p-3 mx-1">
                    <div class="col-7"><?= $client['name'] ?></div>
                    <div class="col"><?= $client['contact'] ?></div>
                </div>
                <div class="mt-3">
                    <a href="<?= "/clients/" . $client['id'] . "/edit" ?>">
                        <button class="btn btn-primary">Mettre à jour</button>
                    </a>
                </div>
            </div>
        </div>

        <div class="col">
            <div class="d-flex flex-column gap-2">
                <h3 class=""><span class="text-orange">Achats</span> effectués</h3>

                <div class="row mb-2 d-flex justify-content-between">
                    <div class="col-7">Désignation de la voiture</div>
                    <div class="col">Quantité</div>
                </div>

                <?php if (empty($orders)) : ?>
                    <div>Aucun achat effectué.</div>
                <?php else : ?>

                    <?php foreach ($orders as $order) : ?>
                        <a href="<?= "/orders/" . $order['id'] ?>" class="bg-white rounded-3 p-3 mx-1 mb-2 text-decoration-none text-body">
                            <div class="row d-flex justify-content-between">
                                <?php foreach ($order['carsQuantities'] as $carQuantity) : ?>
                                    <div class="col-7 fw-bold "><?= $carQuantity['car']['name'] ?></div>
                                    <div class="col "> <?= $carQuantity['quantity'] ?> </div>
                                <?php endforeach; ?>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>



            </div>
        </div>
    </div>
</main>