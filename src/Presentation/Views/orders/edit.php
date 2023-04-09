<?php
// ! be aware, $order can exists or not, depending on the controller that uses this view
// The main logic is to add multiple cars for a single order, (and for a single client)
/* php $cars to js */
function carsToJSCars($cars)
{
    $jsCars = "[" .
        implode(
            ", ",
            array_map(
                function ($car) {
                    $id = $car["id"];
                    $name = $car["name"];
                    $price = $car["price"];
                    $inStock = $car["inStock"];
                    $createdAt = $car["createdAt"];
                    $updatedAt = $car["updatedAt"];

                    $result = "{" .
                        "id: \"" . $id . "\"," .
                        "name: \"" . $name . "\"," .
                        "price:" . $price . "," .
                        "inStock:" . $inStock . "," .
                        "createdAt: new Date(\"" . $createdAt->format(DateTime::ATOM) . "\")," .
                        "updatedAt: new Date(\"" . $updatedAt->format(DateTime::ATOM) . "\")," .
                        "}";

                    return $result;
                },
                $cars
            )
        ) .
        "]";

    return $jsCars;
}
?>


<main class="container d-flex flex-column justify-content-center align-items-center py-4 my-4 w-75">
    <h1 class="display-6 fw-bold">Enregistrer un nouveau <span class="text-orange">Achat</span>.</h1>

    <form action="/orders/<?= $order["id"] ?>/edit" method="post" class="d-flex flex-column gap-2 py-3 w-75" id='create-order-form' novalidate>
        <div class="mb-3">
            <label for="name" class="mb-3">Client</label>
            <select name="clientId" required class="form-control p-3 rounded-3">
                <?php foreach ($clients as $client) : ?>
                    <option value="<?= $client["id"] ?>" <?= $order["clientId"] === $client["id"] ? "selected" : "" ?>>
                        <?= $client["name"] . " - " . $client["contact"] ?>
                    </option>
                <?php endforeach; ?>
            </select>

        </div>
        <div class="mb-3">
            <p>Voiture(s)</p>
            <div id="carOrdersContainer" class="container">
            </div>
            <div class="invalid-feedback mb-3">
                Veuillez remplir correctement le(s) formulaire(s).
            </div>
            <button id="addCar" class="btn btn-primary">
                +
            </button>
        </div>
        <div class="d-flex flex-column">
            <button class="btn btn-primary mt-2" type="submit">
                Enregistrer
            </button>
            <a href="/orders" class="btn mt-2">
                Retour
            </a>
        </div>

    </form>
</main>

<script>
    let cars = <?= carsToJSCars($cars) ?>;
    const initialCars = cars;

    const carOrdersContainer = document.getElementById("carOrdersContainer");
    const form = document.getElementById("create-order-form");
    const invalidFeedback = document.getElementsByClassName('invalid-feedback');

    let carOrderCount = 0;
    let carOrderRemoved = 0;

    // function to add another field for carId and quantity
    const addCarOrder = (carIdSelected, quantity, errorMessage) => {
        carOrderCount++;

        const carOrderContainer = document.createElement("div");
        carOrderContainer.classList.add("carOrderContainer", "row", 'gap-2');

        const carIdSelect = document.createElement("select");
        carIdSelect.setAttribute("name", "carsIds");
        carIdSelect.classList.add('form-control', 'p-3', 'rounded-3', "col", "mb-3");

        // Map the id and the number in stock of car. { key<string>: value<number> }
        let carInStockMapper = {};

        let defaultSelected = undefined;
        cars.forEach(car => {
            const carIdOption = document.createElement("option");
            carIdOption.setAttribute("value", car.id);

            if (car.inStock === 0) {
                carIdOption.setAttribute('hidden', 'hidden')
            } else if (defaultSelected === undefined) {
                defaultSelected = carIdOption;
                carIdOption.setAttribute('selected', 'selected')
            }

            if (carIdSelected === car.id) {
                carIdOption.setAttribute("selected", true);
            }
            const innerText = `${car.name} (${car.inStock} disponibles)`
            carIdOption.innerText = innerText;
            carIdSelect.appendChild(carIdOption);

            carInStockMapper[car.id] = car.inStock;
        });

        const quantityInput = document.createElement("input");
        quantityInput.setAttribute("type", "number");
        quantityInput.setAttribute("name", "quantities");
        quantityInput.setAttribute("id", "quantity");
        quantityInput.setAttribute("placeholder", "quantité");

        // Validate the quantityInput and modify the cars array
        quantityInput.addEventListener('input', (e) => {
            // Define the min and max value of quantity
            const maxInStock = carInStockMapper[carIdSelect.value]
            quantityInput.setAttribute('min', 1)
            quantityInput.setAttribute('max', maxInStock)

            const value = e.target.value
            if (value === '' || parseInt(value) <= 0 || parseInt(value) > maxInStock) {
                quantityInput.classList.add("is-invalid");
            } else {
                quantityInput.classList.remove("is-invalid");
            }

            // Decrease the inStock property of cars when the user changes the quantity input
            cars = cars.map((car, index) => {
                // When the value is not an empty string
                if (car.id === carIdSelect.value && !isNaN(parseInt(value))) {
                    const inStock = car.inStock === parseInt(value) ? 0 : initialCars[index].inStock - parseInt(value)
                    return {
                        ...car,
                        inStock
                    };

                    // When the value is an empty string  
                } else if (car.id === carIdSelect.value && isNaN(parseInt(value))) {
                    // Initialize the value of cars
                    return initialCars[index];
                } else {
                    return car
                }
            })

            // Show the text feedback when there is an error
            const inputs = document.querySelectorAll('input')
            const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));
            if (hasError) {
                invalidFeedback[0].style.display = 'block';
            } else {
                invalidFeedback[0].style.display = 'none';
            }
        });

        quantityInput.classList.add('form-control', 'p-3', 'rounded-3', 'col', "mb-3");
        if (quantity) {
            quantityInput.setAttribute("value", quantity);
        }

        const deleteCarOrderButton = document.createElement("button");
        deleteCarOrderButton.innerText = "x";
        deleteCarOrderButton.classList.add('col-1', 'btn', 'btn-danger', "mb-3")

        deleteCarOrderButton.addEventListener("click", (event) => {
            event.preventDefault();

            if (carOrderCount > 1) {
                const childToRemove = [
                    ...carOrdersContainer.children
                ].find((child) => child === carOrderContainer);
                carOrdersContainer.removeChild(childToRemove);
                carOrderCount--;
            }
        });

        [carIdSelect, quantityInput].forEach((child) => {
            carOrderContainer.appendChild(child);
        });

        if (errorMessage) {
            errorMessageSpan = document.createElement("span");
            errorMessageSpan.innerText = errorMessage;
            carOrderContainer.appendChild(errorMessageSpan);
        }

        carOrderContainer.appendChild(deleteCarOrderButton);

        carOrdersContainer.appendChild(carOrderContainer);
    }

    const addCarButton = document.getElementById("addCar");
    addCarButton.addEventListener("click", (event) => {
        event.preventDefault();
        addCarOrder();
    });

    // Validate when submiting
    form.addEventListener('submit', (e) => {
        const inputs = form.querySelectorAll("input");
        Array.from(inputs).forEach(input => {
            if (input.value === '') {
                input.classList.add("is-invalid");
            }
        })

        const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));
        if (hasError) {
            e.preventDefault();
            invalidFeedback[0].style.display = 'block'
        }
    })

    <?php if ($order) : ?> /* if the order exists we just add all necessary field for cars of that order */
        <?php foreach ($order["carsQuantities"] as $carQuantity) : ?>
            addCarOrder(
                <?= $carQuantity["car"]["id"] ? "\"" . $carQuantity["car"]["id"] . "\"" : "null" ?>,
                <?= $carQuantity["quantity"] ? "\"" . ($carQuantity["quantity"]) . "\"" : "null" ?>,
                <?= $order["errors"]["carsQuantities"][$carQuantity["car"]["id"]][0] ? "\"" . ($order["errors"]["carsQuantities"][$carQuantity["car"]["id"]][0]) . "\"" : "null" ?>
            );
        <?php endforeach; ?>
    <?php else : ?>
        addCarOrder(); // by default, add one field for carId and quantity
    <?php endif; ?>
</script>

<script>
    const validateInputs = () => {
        const inputs = document.querySelectorAll('input');
        const hasError = Array.from(inputs).some((input) => input.classList.contains("is-invalid"));
        if (hasError) {
            invalidFeedback[0].style.display = 'block'
        } else {
            invalidFeedback[0].style.display = 'none'
        }
    }

    validateInputs()
</script>