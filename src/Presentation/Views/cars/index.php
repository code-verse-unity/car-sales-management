<?php

?>

<!-- Search functionality, already handled by IndexCarUseCase and IndexCarController -->
<form action="/cars">
    <input type="search" name="name" id="id" value="<?= $nameQuery ?? "" ?>">

    <input type="submit" value="Search">

    <a href="/cars">Effacer</a>
</form>

<?php if (count($cars) === 0): ?>
    <p>
        <?php if ($nameQuery): ?>
            Il n'y a pas de voitures correspondant à votre recherche.
        <?php else: ?>
            Il n'y a pas encore de voitures.
        <?php endif; ?>
    </p>
<?php else: ?>
    <ul>
        <?php foreach ($cars as $car): ?>
            <li>
                <pre>
                    <?php
                    print_r($car);
                    ?>
                </pre>

                <a href="<?= "/cars/" . $car["id"] ?>">Voir les détails</a>
            </li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>