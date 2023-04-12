<?php

use App\Core\Utils\Strings\FormatCurrency;
?>

<main>
    <div class="row my-3">
        <div class="col-9">
            <h1>Détails de la <span class="text-orange">facture</span> </h1>
            <?php require_once __DIR__ . "/../_bill.php" ?>
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