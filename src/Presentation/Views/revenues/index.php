<?php

use App\Core\Utils\Strings\FormatCurrency;
?>

<main class="my-4">
    <div class="row">
        <div class="col-8 ">
            <div class="sticky-top">
                <h1 class="fw-bold mb-4"><span class="text-orange">Recette</span> totale accumulé des <span class="text-orange">6 derniers mois</span>.</h1>
                <div class="bg-white p-3 rounded-3 shadow ">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col">
            <?php foreach ($revenuePerMonthForLast6Months as $dateAmount) : ?>
                <div class="bg-white p-3 mb-3 rounded-3 shadow">
                    <h6><?= $dateAmount["date"]->format("F Y") ?></h6>
                    <?php if ($dateAmount["amount"] === 0) : ?>
                        <p class="text-secondary">Aucune vente effectué</p>
                    <?php else : ?>
                        <p class="fw-bold fs-4 text-orange"><?= FormatCurrency::format($dateAmount["amount"]) ?></p>
                    <?php endif ?>
                </div>
            <?php endforeach; ?>

        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('myChart');

    const MONTHS = ['Janvier', 'Fevrier', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Decembre']

    const revenues = <?= json_encode($revenuePerMonthForLast6Months) ?>;
    const dates = revenues.map((revenue) => MONTHS[new Date(revenue.date.date).getMonth()]).reverse();
    const data = revenues.map((revenue) => revenue.amount).reverse();

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                borderWidth: 1,
                label: 'Recettes par mois',
                fill: false,
                borderColor: '#ff953f',
                tension: 0.1,
                borderWidth: 3,
                data,
            }],

        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>