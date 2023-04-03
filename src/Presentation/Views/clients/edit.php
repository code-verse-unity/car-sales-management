<?php

?>

<main class="container d-flex flex-column justify-content-center align-items-center py-4 my-4 w-75">
    <h2 class="fw-bold">Mettre à jour les informations du <span class="text-orange">Client</span>.</h2>

    <form action="<?= "/clients/" . $client['id'] . "/edit" ?>" method="post" class="d-flex flex-column gap-2 py-3 w-75" novalidate>
        <div class="mb-3">
            <label for="name" class="form-label">Nom et Prénoms</label>
            <input type="text" name="name" value="<?= $client['name'] ?>" required class="form-control p-3 rounded-3" id="name" placeholder="John Doe">
            <div class="invalid-feedback">
                Le nom est requis et doit contenir au moins 3 caractères.
            </div>
        </div>
        <div class="mb-3">
            <label for="contact" class="form-label">Contact</label>
            <input type="number" name="contact" value="<?= $client['contact'] ?>" class="form-control p-3 rounded-3" id="contact" placeholder="0346632470">
            <div class="invalid-feedback">
                Le numéro de téléphone est requis et doit contenir au moins 10 chiffres.
            </div>
        </div>
        <button class="btn btn-primary mt-2" type="submit">
            Enregistrer les modifications
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