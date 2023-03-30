<?php

?>

<pre>
    <?php print_r($order) ?>

    <form action="/orders/<?= $order["id"] ?>/delete" method="POST">
        <input type="submit" value="Annuler cet achat" style="color:red;">
    </form>
</pre>