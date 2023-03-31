<?php

?>

<?php
ob_start();
require_once __DIR__ . "/_bill.php"; // a preview of the bill
echo ob_get_clean();
?>

<form action="/orders/<?= $order["id"] ?>/delete" method="POST">
    <input type="submit" value="Annuler cet achat" style="color:red;">
</form>

<a href="/bills/<?= $order["id"] ?>/download">Télécharger la facture</a>

<pre>
    <?php print_r($order) ?>
</pre>