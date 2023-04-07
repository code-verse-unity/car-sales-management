<?php
?>

<main class="py-4">
  <div class="d-flex justify-content-between align-items-center">
    <h1 class="fw-bold">Liste des <span class="text-orange">Clients</span>.</h1>
    <div>
      <button class="btn btn-primary" id='add-client'>Ajouter</button>
    </div>
  </div>

  <div class="container">
    <div class="row pt-3">
      <div class="col">Nom et Prénom(s)</div>
      <div class="col">Contact</div>
      <div class="col d-flex justify-content-end">Actions</div>
    </div>


    <?php if (empty($clients)) : ?>
      <div class="d-flex justify-content-center p-5">
        <h3 class="text-center">Aucun client n'a été enregistré.</h3>
      </div>
    <?php else : ?>
      <?php foreach ($clients as $client) : ?>
        <div class="row my-3 py-3 px-2 bg-white rounded-3 shadow-sm">
          <div class="col d-flex align-items-center fw-bold"><?= $client['name'] ?></div>
          <div class="col d-flex align-items-center"><?= $client['contact'] ?></div>
          <div class="col d-flex justify-content-end">
            <a href="<?= "/clients/" . $client['id'] . "/edit" ?>">
              <button class="btn btn-primary ">
                <img src="assets/icons/edit.svg" alt="edit client" srcset="" class="icon">
              </button>
            </a>
            <a href="<?= "/clients/" . $client['id'] ?>">
              <button class="btn btn-success mx-3">
                <img src="assets/icons/show.svg" alt="show client" srcset="" class="icon">
              </button>
            </a>
            <!-- TODO need review -->
            <!-- <button class="btn btn-danger">
              <img src="assets/icons/delete.svg" alt="delete client" srcset="" class="icon">
            </button> -->
          </div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>

</main>

<script>
  const addClientButton = document.getElementById('add-client');
  addClientButton.addEventListener('click', () => {
    window.location.href = '/clients/add';
  });
</script>