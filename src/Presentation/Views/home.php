<?php

?>
<main class="py-2">
  <div class="row py-4">
    <div class="col d-flex flex-column justify-content-between">
      <h1><span class="text-orange">Bienvenue</span> dans le tableau de bord.</h1>
      <h5>Activités</h5>
    </div>
    <div class="col">
      <div class='ca-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Chiffre d'affaire </h4>
        <div class="fw-bold display-4">152 000 000 Ar</div>
      </div>
    </div>
  </div>

  <div class="row py-4">
    <div class="col">
      <div class='clients-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Liste des Clients </h4>
        <div class="fw-bold display-4">15</div>
      </div>
    </div>
    <div class="col">
      <div class='orders-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Liste des achats </h4>
        <div class="fw-bold display-4">120</div>
      </div>
    </div>
    <div class="col">
      <div class='cars-image d-flex flex-column justify-content-center p-4 text-light'>
        <h4>Liste des voitures </h4>
        <div class="fw-bold display-4">120</div>
      </div>
    </div>
  </div>

  <pre>
    <?php var_dump($clientsCount); ?>
    <?php var_dump($carsCount); ?>
    <?php var_dump($ordersCount); ?>
    <?php var_dump($ordersCountForLast6Months); ?>
    <?php var_dump($revenue); ?>
    <?php var_dump($revenueOfLast6Months); ?>
  </pre>
</main>

<!-- Js script to handle navigation to /clients /cars and /orders -->
<script>
  const clients = document.querySelector('.clients-image');
  const cars = document.querySelector('.cars-image');
  const orders = document.querySelector('.orders-image');

  clients.addEventListener('click', () => {
    window.location.href = '/clients';
  });

  cars.addEventListener('click', () => {
    window.location.href = '/cars';
  });

  orders.addEventListener('click', () => {
    window.location.href = '/orders';
  });
</script>