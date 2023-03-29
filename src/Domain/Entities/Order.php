<?php

namespace App\Domain\Entities;

use App\Core\Utils\Failures\ServerFailure;
use DateTime;

abstract class Order implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

  private $id;
  private Client $client;
  private array $carsQuantities;
  private $createdAt;
  private $updatedAt;

  /*
  $errors can have keys: id, createdAt, updatedAt, and carsQuantities
  carsQuantities is like this:
  [
  "mBwSV__ZbkMho_h_yq4Dx" => "La quantité de la voiture ne doit pas dépasser le nombre en stock.",
  ]
  with the carId as key and the message as value
  */
  private array $errors = [];
  private bool $locked = false;

  public function __construct(
    $id,
    Client $client,
    array $carsQuantities,
    // associative array with car and quantity as keys
    $createdAt,
    $updatedAt
  ) {
    /*
    Client and Car should not have any error,
    but if they have, mostly it's a programmer mistake,
    so we throw a ServerFailure
    */
    if ($client->hasErrors()) {
      throw new ServerFailure();
    }

    $this->client = $client;
    $this->carsQuantities = $this->validateCarsQuantities($carsQuantities);

    $this->id = $this->validateId($id);
    $this->createdAt = $this->validateCreatedAt($createdAt);
    $this->updatedAt = $this->validateUpdatedAt($updatedAt);
  }

  private function validateId($id)
  {
    if (!is_string($id)) {
      $this->addErrorByAttribute("id", "L'identifiant d'un achat doit être une chaîne de caractères.");
    }

    if (strlen($id) !== self::ID_LENGTH) {
      $this->addErrorByAttribute("id", "L'identifiant d'un achat doit avoir" . self::ID_LENGTH . " caractères.");
    }

    if (!$this->containsOnlyChars($id, str_split(self::ID_CHARACTERS))) {
      $this->addErrorByAttribute("id", "L'identifiant d'un achat contient des caractères non autorisées.");
    }

    return $id;
  }

  public function validateCarsQuantities($carsQuantities)
  {
    if (
      !isset($carsQuantities) ||
      $carsQuantities === null ||
      !is_array($carsQuantities)
    ) {
      throw new ServerFailure();
    }

    return array_map(
      function ($carQuantity) {
        if (
          !isset($carQuantity) ||
          $carQuantity === null ||
          !is_array($carQuantity) ||
          !isset($carQuantity["car"]) ||
          $carQuantity["car"] === null ||
          !is_array($carQuantity["car"]) ||
          !isset($carQuantity["quantity"]) ||
          $carQuantity["quantity"] === null ||
          !is_array($carQuantity["quantity"])
        ) {
          throw new ServerFailure();
        }

        $car = $carQuantity["car"];
        $quantity = $carQuantity["quantity"];

        $result = [
          "car" => $car,
          "quantity" => $quantity
        ];

        if (!$car instanceof Car) {
          throw new ServerFailure();
        } else if ($car->hasErrors()) {
          throw new ServerFailure();
        }

        if (!isset($quantity) || $quantity === null) {
          $this->addCarQuantityError($car->getId(), "La quantité de la voiture est obligatoire.");
        }

        if (!is_numeric($quantity)) {
          $this->addCarQuantityError($car->getId(), "La quantité de la voiture doit être un nombre entier.");
        } else {
          $quantity_int = intval($quantity);

          if ($quantity_int < 0) {
            $this->addCarQuantityError($car->getId(), "La quantité de la voiture doit être un nombre positif.");
          }

          if ($quantity_int > $car->getInStock()) {
            $this->addCarQuantityError($car->getId(), "La quantité de la voiture ne doit pas dépasser le nombre en stock.");
          }

          $result["quantity"] = $quantity_int;
        }

        return $result;
      },
      $carsQuantities
    );
  }

  public function getCarsQuantities()
  {
    return $this->carsQuantities;
  }

  public function getId()
  {
    return $this->id;
  }

  public function getCars(): array
  {
    return array_map(function ($carQuantity) {
      return $carQuantity["car"];
    }, $this->carsQuantities);
  }

  public function getQuantities(): array
  {
    return array_map(function ($carQuantity) {
      return $carQuantity["quantity"];
    }, $this->carsQuantities);
  }

  public function setCarsQuantities($carsQuantities)
  {
    if ($this->locked) {
      throw new ServerFailure("instance locked");
    }

    $this->removeCarQuantityErrors();
    $this->carsQuantities = $this->validateCarsQuantities($carsQuantities);

    $this->triggerUpdate();
  }

  private function validateCreatedAt($createdAt)
  {
    if (!$createdAt instanceof Datetime) {
      $this->addErrorByAttribute("createdAt", "La date de creation de l'achat n'est pas valide.");
    }

    return $createdAt;
  }

  private function validateUpdatedAt($updatedAt)
  {
    if (!$updatedAt instanceof Datetime) {
      $this->addErrorByAttribute("updatedAt", "La date de la dernière modification de l'achat n'est pas valide.");
    }

    return $updatedAt;
  }

  private function triggerUpdate()
  {
    $this->updatedAt = new DateTime();
  }

  public function getClientId()
  {
    return $this->client->getId();
  }

  public function getClient()
  {
    return $this->client;
  }

  public function getCarsIds()
  {
    return array_map(function ($carQuantity) {
      return $carQuantity["car"]->getId();
    }, $this->carsQuantities);
  }

  public function getCreatedAt()
  {
    return $this->createdAt;
  }

  public function getUpdatedAt()
  {
    return $this->updatedAt;
  }

  public function getRaw()
  {
    return [
      "id" => $this->getId(),
      "clientId" => $this->getClientId(),
      "client" => $this->getClient()->getRaw(),
      "carsIds" => $this->getCarsIds(),
      "cars" => array_map(
        function ($car) {
          return $car->getRaw();
        },
        $this->getCars()
      ),
      "quantities" => $this->getQuantities(),
      "carsQuantities" => array_map(
        function ($carQuantity) {
          return [
            "quantity" => $carQuantity["quantity"],
            "car" => $carQuantity["car"]->getRaw()
          ];
        },
        $this->getCarsQuantities()
      ),
      "createdAt" => $this->getCreatedAt(),
      "updatedAt" => $this->getUpdatedAt(),
      "errors" => $this->getErrors(),
    ];
  }

  public function lock(): void
  {
    $this->locked = true;
    $this->client->lock();
    foreach ($this->carsQuantities as $carQuantity) {
      $carQuantity["car"]->lock();
    }
  }

  public function isLocked(): bool
  {
    return $this->locked;
  }

  public function hasErrors(): bool
  {
    return count($this->errors) > 0;
  }

  public function getErrors(): array
  {
    return $this->errors;
  }

  public function hasError(string $attribute): bool
  {
    return !!$this->errors[$attribute];
  }

  public function getError(string $attribute): array
  {
    return $this->errors[$attribute];
  }

  public function getFirstError(string $attribute): string
  {
    return $this->errors[$attribute][0];
  }

  protected function addErrorByAttribute(string $attribute, string $message)
  {
    if (array_key_exists($attribute, $this->errors)) {
      $this->errors[$attribute][] = $message;
    } else {
      $this->errors[$attribute] = [$message];
    }
  }

  protected function addCarQuantityError(string $carId, string $message)
  {
    if (!array_key_exists("carsQuantities", $this->errors)) {
      $this->errors["carsQuantities"] = [];
    }

    if (!array_key_exists($carId, $this->errors["carsQuantities"])) {
      $this->errors["carsQuantities"][$carId] = [];
    }

    $this->errors["carsQuantities"][$carId][] = $message;
  }

  public function removeCarQuantityErrors()
  {
    unset($this->errors["carsQuantities"]);
  }

  private function containsOnlyChars(string $string, array $allowedChars)
  {
    $length = strlen($string);
    for ($i = 0; $i < $length; $i++) {
      if (!in_array($string[$i], $allowedChars)) {
        return false;
      }
    }
    return true;
  }

  private function removeErrorsByAttribute(string $attribute)
  {
    unset($this->errors[$attribute]);
  }
}