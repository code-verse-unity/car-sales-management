<?php

?>

<!-- Search functionality, already handled by IndexOrderUseCase and IndexOrderController -->
<form action="/orders" method="GET" id="index-order-form">
    <label for="startAt">Début</label>
    <input type="date" name="startAt" id="startAt" value="<?= $startAt ? $startAt->format("Y-m-d") : "" ?>">

    <label for="endAt">Fin</label>
    <input type="date" name="endAt" id="endAt" value="<?= $endAt ? $endAt->format("Y-m-d") : "" ?>">


    <input type="submit" value="Rechercher">

    <a href="/orders">Effacer</a>
</form>

<script>
    // ! this script only works because the form's method is GET, so we can use window.location.href
    const startAt = document.getElementById("startAt");
    const endAt = document.getElementById("endAt");
    const form = document.getElementById("index-order-form");
    const basePath = "/orders";

    form.addEventListener("submit", (event) => {
        event.preventDefault();
        const startAtValue = startAt.value;
        const endAtValue = endAt.value;
        let path = basePath + "?";

        if (startAtValue) {
            path += `startAt=${startAtValue}&`;
        }

        if (endAtValue) {
            path += `endAt=${endAtValue}&`;
        }

        window.location.href = path;
    });
</script>

<?php if (count($orders) === 0): ?>
    <p>
        <?php if ($starAt || $endAt): ?>
            Il n'y a pas d'achats correspondant à votre recherche.
        <?php else: ?>
            Il n'y a pas encore d'achats effectués.
        <?php endif; ?>
    </p>
<?php else: ?>
    <ul>
        <?php foreach ($orders as $order): ?>
            <li>
                <pre>
                            <?php print_r($order) ?>
                        </pre>

                <a href="<?= "/orders/" . $order["id"] ?>">Voir les détails</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>