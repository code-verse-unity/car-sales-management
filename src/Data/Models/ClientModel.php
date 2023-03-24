<?php

namespace App\Data\Models;

use App\Domain\Entities\Client;
use \DateTime;

class ClientModel extends Client
{
  const TABLE_NAME = "clients";

  public function __construct($id, string $name, string $contact, $createdAt, $updatedAt)
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

    parent::__construct($id, $name, $contact, new DateTime($createdAt), new DateTime($updatedAt));
  }
}
