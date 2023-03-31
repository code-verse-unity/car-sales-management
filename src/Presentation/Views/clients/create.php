<?php

?>

<main class="container d-flex flex-column justify-content-center py-4 w-75">
  <h1 class="display-5 fw-bold">Ajouter un nouveau <span class="text-orange">Client</span>.</h1>

  <form action="<?= "/clients" ?>" method="post" class="d-flex flex-column gap-2 py-3 needs-validation" novalidate>
    <div class="mb-3">
      <label for="exampleFormControlInput1" class="form-label">Nom et Prénoms</label>
      <input type="text" name="name" required class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="John Doe">
      <div class="invalid-feedback">
        Le nom est requis et doit contenir au moins 3 caractères.
      </div>
    </div>
    <div class="mb-3">
      <label for="exampleFormControlInput1" class="form-label">Contact</label>
      <input type="number" name="contact" class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="0346632470">
      <div class="invalid-feedback">
        Le numéro de téléphone est requis et doit contenir au moins 10 chiffres.
      </div>
    </div>
    <button class="btn btn-primary" type="submit">
      Enregistrer
    </button>
  </form>
</main>

<script>
  const nameInput = document.querySelector("input[name='name']");
  const contactInput = document.querySelector("input[name='contact']");

  nameInput.addEventListener("input", (e) => {
    const value = e.target.value;
    if (value.length < 3 || value === '') {
      nameInput.classList.add("is-invalid");
    } else {
      nameInput.classList.remove("is-invalid");
    }
  });

  contactInput.addEventListener("input", (e) => {
    const value = e.target.value;
    if (value.length < 10) {
      contactInput.classList.add("is-invalid");
    } else {
      contactInput.classList.remove("is-invalid");
    }
  });

  const form = document.querySelector("form");
  form.addEventListener("submit", (e) => {
    const inputs = form.querySelectorAll("input");
    Array.from(inputs).forEach(input => {
      if (input.value === '') {
        input.classList.add("is-invalid");
      }
    })

    const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));
    if (hasError) {
      e.preventDefault();
    }
  });
</script>