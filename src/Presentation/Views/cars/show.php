<?php

?>

<pre>
    <?php
    print_r($car);
    ?>

    <!-- TODO list all orders with that car, and the incomes -->

    <a href="<?= "/cars/" . $car["id"] . "/edit" ?>">Éditer</a> <!-- Not implemented yet -->
</pre>