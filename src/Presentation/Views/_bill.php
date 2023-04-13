<?php
// ! $order is required

// ! This file is used to create the PDF, and also can beb used to make a preview of the bill

require_once __DIR__ . "/../../../vendor/autoload.php";

use App\Data\Models\OrderModel;
use App\Core\Utils\Strings\DateFormatter;
use App\Core\Utils\Strings\NumberToText;
use App\Core\Utils\Strings\FormatCurrency;

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
            text-align: center;
            font-size: x-large;
            margin-bottom: 3rem;
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

        .order-infos h2 {
            font-size: larger;
        }

        .order-info {
            font-weight: bold;
        }

        .table-head-row {
            background-color: #263238;
            color: #fff;
        }

        .table-head-cell {
            border-right: solid 2px #fff;
        }

        .table-head-cell:last-of-type {
            border-right: none;
        }

        .order-table-row:nth-child(2n + 1) {
            background-color: #cfd8dc;
        }

        .total-text-cell {
            background-color: #263238;
            color: #fff;
            text-align: center;
            text-transform: uppercase;
            font-weight: bold;
            letter-spacing: 0.2rem;
            border: solid 2px #263238;
        }

        .car-name {
            font-weight: bold;
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
            border: solid 2px #263238;
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
            <span class="order-info"><?= $order["id"] ?></span>
        </h1>

        <div class="order-infos">
            <h2>Date de facturation :
                <span class="order-info">
                    <?= DateFormatter::format($order["createdAt"]) ?>
                </span>
            </h2>

            <h2>Nom du client :
                <span class="order-info">
                    <?= $order["client"]["name"] ?>
                </span>
            </h2>

            <h2>Identifiant du client :
                <span class="order-info">
                    <?= $order["client"]["id"] ?>
                </span>
            </h2>

            <h2>Contact :
                <span class="order-info">
                    <?= $order["client"]["contact"] ?>
                </span>
            </h2>
        </div>

        <div class="table-container">
            <table class="bill-table">
                <thead>
                    <tr class="table-head-row">
                        <th class="table-head-cell">Désignation</th>
                        <th class="table-head-cell">Quantité</th>
                        <th class="table-head-cell">Prix Unitaire</th>
                        <th class="table-head-cell">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order["carsQuantities"] as $carQuantity) : ?>
                        <tr class="order-table-row">
                            <td class="car-name">
                                <?= $carQuantity["car"]["name"] ?>
                            </td>
                            <td class="car-quantity">
                                <?= $carQuantity["quantity"] ?>
                            </td>
                            <td class="car-price">
                                <?= FormatCurrency::format($carQuantity["car"]["price"]) ?>
                            </td>
                            <td class="order-subtotal">
                                <?= FormatCurrency::format($carQuantity["subtotal"]) ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="total-table-row">
                        <td class="cell-void"></td>
                        <td class="cell-void"></td>
                        <td class="total-text-cell">Total</td>
                        <td class="order-total">
                            <?= FormatCurrency::format($order["total"]) ?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p>Arrêté par la présente facture à la somme de
            <span class="total-text">
                <?= NumberToText::ToText($order["total"]) ?>
            </span>
            <?= OrderModel::CURRENCY_CODE ?>.
        </p>
    </div>
</body>

</html>