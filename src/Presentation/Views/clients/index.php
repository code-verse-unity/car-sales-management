<?php

?>

<main class="py-4">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="display-4 fw-bold">Liste des <span class="text-orange">Clients</span>.</h1>
    <div>
      <button class="btn btn-primary">Ajouter</button>
    </div>
  </div>

  <div class="container">
    <div class="row pt-3">
      <div class="col">Nom et Prénom(s)</div>
      <div class="col">Contact</div>
      <div class="col d-flex justify-content-end">Actions</div>
    </div>

    <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
      <div class="col d-flex align-items-center fw-bold">John Doe</div>
      <div class="col d-flex align-items-center">+23648464548</div>
      <div class="col d-flex justify-content-end">
        <button class="btn btn-primary mx-3">
          <img src="assets/icons/edit.svg" alt="edit cars" srcset="" class="icon">
        </button>
        <button class="btn btn-danger">
          <img src="assets/icons/delete.svg" alt="delete cars" srcset="" class="icon">
        </button>
      </div>
    </div>
  </div>

</main>