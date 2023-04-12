<?php

use App\Core\Utils\Strings\FormatCurrency;

?>
<main class="py-2">
  <div class="row py-4">
    <div class="col d-flex flex-column justify-content-between">
      <h1><span class="text-orange">Bienvenue</span> dans le tableau de bord.</h1>
      <h5>Activités</h5>
    </div>
    <div class="col">
      <div class='ca-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Chiffre d'affaires des 6 derniers mois </h4>
        <div class="fw-bold display-5"><?= FormatCurrency::format($revenueOfLast6Months) ?></div>
      </div>
    </div>
  </div>

  <div class="row py-4">
    <div class="col">
      <div class='clients-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Nombre de clients </h4>
        <div class="fw-bold display-4"><?= $clientsCount ?></div>
      </div>
    </div>

    <div class="col">
      <div class='cars-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Nombre de voitures </h4>
        <div class="fw-bold display-4"><?= $carsCount ?></div>
      </div>
    </div>

    <div class="col">
      <div class='orders-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Liste des achats </h4>
        <div class="fw-bold display-4"><?= $ordersCount ?></div>
      </div>
    </div>
  </div>
</main>

<!-- Js script to handle navigation to /clients /cars, /orders and /revenues -->
<script>
  const clients = document.querySelector('.clients-image');
  const cars = document.querySelector('.cars-image');
  const orders = document.querySelector('.orders-image');
  const revenues = document.querySelector('.ca-image');

  clients.addEventListener('click', () => {
    window.location.href = '/clients';
  });

  cars.addEventListener('click', () => {
    window.location.href = '/cars';
  });

  orders.addEventListener('click', () => {
    window.location.href = '/orders';
  });

  revenues.addEventListener('click', () => {
    window.location.href = '/revenues';
  });
</script>