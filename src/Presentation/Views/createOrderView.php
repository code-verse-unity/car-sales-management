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

<form action="/orders/create" method="POST">
    <div>
        <label for="name">Client:</label>
        <select name="clientId">
            <?php foreach ($clients as $client): ?>
                <option value="<?= $client["id"] ?>" <?= $order["clientId"] === $client["id"] ? "selected" : "" ?>>
                    <?= $client["name"] ?>
                    <span> <!-- ? Maybe the id is not necessary -->
                        (
                        <?= $client["id"] ?>
                        )
                    </span>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div>
        <p>Voiture:</p>
        <div id="carOrdersContainer">
        </div>

        <button id="addCar">
            Ajouter une voiture
        </button>

        <div>
            <input type="submit" value="Créer">
            <a href="/orders">Annuler</a>
        </div>
    </div>
</form>

<script>
    const cars = <?= carsToJSCars($cars) ?>;
    const carOrdersContainer = document.getElementById("carOrdersContainer");
    let carOrderCount = 0;
    let carOrdersElements = [];

    // function to add another field for carId and quantity
    const addCarOrder = (carIdSelected, quantity, errorMessage) => {
        carOrderCount++;

        const carOrderContainer = document.createElement("div");
        carOrderContainer.classList.add("carOrderContainer");

        const carIdLabel = document.createElement("label");
        carIdLabel.setAttribute("for", "carId");
        carIdLabel.innerText = "Designation";

        const carIdSelect = document.createElement("select");
        carIdSelect.setAttribute("name", "carId");

        cars.forEach(car => {
            const carIdOption = document.createElement("option");
            carIdOption.setAttribute("value", car.id);
            if (carIdSelected === car.id) {
                carIdOption.setAttribute("selected", true);
            }
            carIdOption.innerText = `${car.name} (${car.inStock} disponibles) (${car.id})`;
            carIdSelect.appendChild(carIdOption);
        });

        const quantityLabel = document.createElement("label");
        quantityLabel.setAttribute("for", "quantity");
        quantityLabel.innerText = "Quantité :";

        const quantityInput = document.createElement("input");
        quantityInput.setAttribute("type", "number");
        quantityInput.setAttribute("name", "quantity");
        quantityInput.setAttribute("id", "quantity");
        if (quantity) {
            quantityInput.setAttribute("value", quantity);
        }

        const deleteCarOrderButton = document.createElement("button");
        deleteCarOrderButton.innerText = "Supprimer";
        deleteCarOrderButton.addEventListener("click", (event) => {
            event.preventDefault();

            if (carOrderCount > 1) {
                const childToRemove = carOrdersContainer.children[carOrderCount - 1];
                carOrdersContainer.removeChild(childToRemove);
                carOrderCount--;
            }
        });

        [carIdLabel, carIdSelect, quantityLabel, quantityInput].forEach((child) => {
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

    <?php if ($order): ?> /* if the order exists we just add all necessary field for cars of that order */
        <?php foreach ($order["carsQuantities"] as $carQuantity): ?>
            addCarOrder(
                <?= "\"" . ($carQuantity["car"]["id"] ?? "null") . "\"" ?>,
                <?= "\"" . ($carQuantity["quantity"] ?? "null") . "\"" ?>,
                <?= "\"" . ($order["errors"]["carsQuantities"][$carQuantity["car"]["id"]][0] ?? "null") . "\"" ?>
            );
        <?php endforeach; ?>
    <?php else: ?>
        addCarOrder(); // by default, add one field for carId and quantity
    <?php endif; ?>
</script>