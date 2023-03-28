<?php

namespace App\Data\Models;

use App\Domain\Entities\Car;
use \DateTime;

class CarModel extends Car
{
  const TABLE_NAME = "cars";

  public function __construct($id, $name, $price, $inStock, $createdAt, $updatedAt)
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

    parent::__construct($id, $name, $price, $inStock, new DateTime($createdAt), new DateTime($updatedAt));
  }
}