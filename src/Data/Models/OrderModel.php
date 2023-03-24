<?php

namespace App\Data\Models;

use App\Domain\Entities\Order;
use \DateTime;

class OrderModel extends Order
{
  const TABLE_NAME = "orders";

  public function __construct($id, ClientModel $client, CarModel $car, int $quantity, $createdAt, $updatedAt)
  {
    $now = (new DateTime())->getTimestamp();

    $createdAtValue = new DateTime();
    if ($createdAt) {
      $createdAtValue = new DateTime($createdAt);
    } else {
      $createdAtValue->setTimestamp($now);
    }

    $updatedAtValue = new DateTime();
    if ($updatedAt) {
      $updatedAtValue = new DateTime($updatedAt);
    } else {
      $updatedAtValue->setTimestamp($now);
    }

    parent::__construct($id, $client, $car, $quantity, new DateTime($createdAt), new DateTime($updatedAt));
  }
}
