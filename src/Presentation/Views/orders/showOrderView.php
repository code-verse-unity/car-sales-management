<?php

use App\Core\Utils\Strings\FormatCurrency;
?>

<?php
// ob_start();
// require_once __DIR__ . "/../_bill.php"; // a preview of the bill
// echo ob_get_clean();
?>

<!-- <form action="/orders/<?= $order["id"] ?>/delete" method="POST">
    <input type="submit" value="Annuler cet achat" style="color:red;">
</form>

<a href="/bills/<?= $order["id"] ?>/download">Télécharger la facture</a> -->

<!-- <pre>
    <?php print_r($order) ?>
</pre> -->

<main>
    <div class="row my-3">
        <div class="col-9">
            <h1>Détails de la <span class="text-orange">facture</span> </h1>
            <div class="bg-white rounded-3 p-4 shadow">
                <ol class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Réference de la facture </div>
                            <?= $order["id"] ?>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Information du client</div>
                            <ul>
                                <li class="list-item"> Nom et prénoms : <?= $order["client"]["name"] ?></li>
                                <li class="list-item">Téléphone : <?= $order["client"]["contact"] ?></li>
                            </ul>
                        </div>

                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Détails de(s) voiture(s)</div>
                            <ul>
                                <li class="list-item"> Nombre de voitures : <?= count($order["carsIds"]) ?></li>
                                <?php foreach ($order["carsQuantities"] as $carQuantity) : ?>
                                    <li class="list-item"> <?= $carQuantity['car']['name'] ?> ( <?= $carQuantity['quantity'] ?> ) : <?= FormatCurrency::format($carQuantity['subtotal']) ?> </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-start">
                        <div class="ms-2 me-auto">
                            <div class="fw-bold">Somme Totale reçue</div>
                            <?= FormatCurrency::format($order["total"]) ?>
                        </div>
                    </li>
                </ol>
            </div>

        </div>
        <div class="col">
            <h1>Actions</h1>
            <form action="/orders/<?= $order["id"] ?>/delete" method="POST" class="mb-3">

                <!-- Button trigger modal -->
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#exampleModal">
                    Annuler cet achat
                </button>

                <!-- Modal -->
                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Annulation d'achat ?</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p>Vous êtes sur le point d'annuler cet achat. Cette action est irréversible.</p>
                                <p>Si vous êtes sur de vouloir annuler cet achat, cliquez sur le bouton "Oui, annuler".</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Non</button>
                                <input type="submit" value="Oui, annuler" class="btn btn-danger">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <a href="/bills/<?= $order["id"] ?>/download" class="btn btn-primary">Télécharger la facture</a>
        </div>
    </div>
</main>