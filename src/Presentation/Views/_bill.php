<?php
// ! $order is required

// ! This file is used to create the PDF, and also can beb used to make a preview of the bill

require_once __DIR__ . "/../../../vendor/autoload.php";

use App\Data\Models\OrderModel;

function formatCurrency($amount)
{
    // ! the dutch (Germany) format is more close to the Malagasy format than the "mg-MG" itself
    $formatter = new NumberFormatter(
        "de-DE",
        NumberFormatter::CURRENCY
    );

    // ! so we replace € to the actual currency
    return str_replace(
        "€",
        OrderModel::CURRENCY_CODE,
        $formatter->formatCurrency($amount, "EUR")
    );
}

function formatDate(DateTime $date)
{
    $currentTimeZone = $_ENV["CURRENT_TIMEZONE_NAME"];

    $formatter = new IntlDateFormatter(
        'fr_FR',
        IntlDateFormatter::FULL,
        IntlDateFormatter::FULL,
        $currentTimeZone,
        IntlDateFormatter::GREGORIAN,
        "dd MMM yyyy"
    );

    return $formatter->format($date);
}

function numberToText($number)
{
    $formatter = new NumberFormatter(
        "fr",
        NumberFormatter::SPELLOUT
    );

    return $formatter->format($number);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>
        Facture :
        <?= $order["id"] ?>
    </title>
    <!-- ! Need to add directly the bootstrap files (not links) here to apply style for the PDF -->
    <style>
        .bill-title {
            font-weight: bold;
            text-align: center;
        }

        .table-container {
            margin: 5rem 0;
        }

        .bill-table {
            width: 90%;
            margin: 0 auto;
            border-collapse: collapse;
            border-spacing: 0;
        }

        th {
            text-align: center;
            padding: 1rem;
        }

        td {
            padding: 0.7rem;
        }

        th,
        td {
            border: solid 2px #000;
        }

        .cell-void {
            border: none;
        }

        .car-quantity {
            text-align: center;
        }

        .car-price {
            text-align: right;
        }

        .order-subtotal {
            text-align: right;
        }

        .order-total {
            padding: 1rem;
            font-weight: bold;
            text-align: right;
        }

        .total-text {
            font-weight: bold;
        }

        .bill-container {
            margin: 1rem;
            padding: 1rem 2rem 7rem 2rem;
            border: dotted 5px #000;
        }
    </style>
</head>

<body>
    <div class="bill-container">
        <h1 class="bill-title">
            Facture :
            <?= $order["id"] ?>
        </h1>

        <div>
            <h2>Date de facturation :
                <?= formatDate($order["createdAt"]) ?>
            </h2>

            <h2>Nom du client :
                <?= $order["client"]["name"] ?>
            </h2>

            <h2>Identifiant du client :
                <?= $order["client"]["id"] ?>
            </h2>

            <h2>Contact :
                <?= $order["client"]["contact"] ?>
            </h2>
        </div>

        <div class="table-container">
            <table class="bill-table">
                <thead>
                    <tr>
                        <th class="tg-j1i3">Désignation</th>
                        <th class="tg-j1i3">Quantité</th>
                        <th class="tg-j1i3">Prix Unitaire</th>
                        <th class="tg-j1i3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order["carsQuantities"] as $carQuantity): ?>
                        <tr>
                            <td class="car-name">
                                <?= $carQuantity["car"]["name"] ?>
                            </td>
                            <td class="car-quantity">
                                <?= $carQuantity["quantity"] ?>
                            </td>
                            <td class="car-price">
                                <?= formatCurrency($carQuantity["car"]["price"]) ?>
                            </td>
                            <td class="order-subtotal">
                                <?= formatCurrency($carQuantity["subtotal"]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td class="cell-void"></td>
                        <td class="cell-void"></td>
                        <td class="cell-void"></td>
                        <td class="order-total">
                            <?= formatCurrency($order["total"]) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p>Arrêté par la présente facture à la somme de
            <span class="total-text">
                <?= numberToText($order["total"]) ?>
            </span>
            <?= OrderModel::CURRENCY_CODE ?>.
        </p>
    </div>
</body>

</html>