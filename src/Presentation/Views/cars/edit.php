<?php
// echo print_r($car);
?>

<main class="container d-flex flex-column justify-content-center align-items-center py-4 w-75">
    <h1 class="display-6 fw-bold ">Modifier une <span class="text-orange">Voiture</span>.</h1>

    <form action="<?= "/cars/" . $car['id'] . "/edit" ?>" method="post" class="d-flex flex-column gap-2 py-3 w-75" novalidate>
        <!-- To send put request -->
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Désignation de la voiture</label>
            <input type="text" name="name" value=<?= $car['name'] ?> class="form-control p-3 rounded-3" id="exampleFormControlInput1" placeholder="Ferrari">
            <div class="invalid-feedback">
                Le nom de la voiture est obligatoire.
            </div>
        </div>
        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Prix en Ariary</label>
            <input type="number" name="price" value=<?= $car['price'] ?> class="form-control p-3 rounded-3" readonly id="exampleFormControlInput1" placeholder="50000000">
            <div class="invalid-feedback">
                Veuillez entrer un prix correct.
            </div>
        </div>
        <div class="mb-3">
            <label for="stockNumber" class="form-label">Nombre en Stock</label>
            <input type="number" name='inStock' value=<?= $car['inStock'] ?> class="form-control p-3 rounded-3" id="stockNumber" placeholder="10">
            <div class="invalid-feedback">
                Veuillez indiquer le nombre de voitures disponible et il doit être supérieur à 0.
            </div>
        </div>
        <button class="btn btn-primary mt-2">
            Enregistrer
        </button>
    </form>

</main>

<script>
    const nameInput = document.querySelector("input[name='name']");
    const priceInput = document.querySelector("input[name='price']");
    const inStockInput = document.querySelector("input[name='inStock']");

    nameInput.addEventListener("input", (e) => {
        const value = e.target.value;
        if (value.length < 3 || value === '') {
            nameInput.classList.add("is-invalid");
        } else {
            nameInput.classList.remove("is-invalid");
        }
    });

    priceInput.addEventListener("input", (e) => {
        const value = e.target.value;

        if (value === '' || isNaN(value) || value < 0) {
            priceInput.classList.add("is-invalid");
        } else {
            priceInput.classList.remove("is-invalid");
        }
    });

    inStockInput.addEventListener("input", (e) => {
        const value = e.target.value;
        if (value === '' || isNaN(value) || value <= 0) {
            inStockInput.classList.add("is-invalid");
        } else {
            inStockInput.classList.remove("is-invalid");
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