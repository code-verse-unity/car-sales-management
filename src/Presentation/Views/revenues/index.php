<?php
use App\Core\Utils\Strings\FormatCurrency;
?>

<ul>
    <?php foreach($revenuePerMonthForLast6Months as $dateAmount): ?>
        <li>
            <p><?= $dateAmount["date"]->format("F Y") ?></p>
            <p><?= FormatCurrency::format($dateAmount["amount"]) ?></p>
        </li>
    <?php endforeach; ?>
</ul>