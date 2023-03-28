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
  private Car $car;
  private $quantity;

  private $createdAt;
  private $updatedAt;

  private array $errors = [];
  private bool $locked = false;

  public function __construct($id, Client $client, Car $car, $quantity, $createdAt, $updatedAt)
  {
    /*
    Client and Car should not have any error,
    but if they have, mostly it's a programmer mistake,
    so we throw a ServerFailure
    */
    if ($client->hasErrors()) {
      throw new ServerFailure();
    }

    if ($car->hasErrors()) {
      throw new ServerFailure();
    }

    $this->client = $client;
    $this->car = $car;

    $this->id = $this->validateId($id);
    $this->quantity = $this->validateQuantity($quantity);
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

  public function getId()
  {
    return $this->id;
  }

  public function getQuantity()
  {
    return $this->quantity;
  }

  public function setQuantity($quantity)
  {
    if ($this->locked) {
      throw new ServerFailure("instance locked");
    }

    $this->removeErrorsByAttribute("quantity");
    $this->quantity = $this->validateQuantity($quantity);

    $this->triggerUpdate();
  }

  private function validateQuantity($quantity)
  {
    if (!isset($quantity) || $quantity === null) {
      $this->addErrorByAttribute("quantity", "La quantité de la voiture est obligatoire.");
    }

    if (!is_numeric($quantity)) {
      $this->addErrorByAttribute("quantity", "La quantité de la voiture doit être un nombre entier.");

      return $quantity;
    } else {
      $quantity_int = intval($quantity);

      if ($quantity_int < 0) {
        $this->addErrorByAttribute("quantity", "La quantité de la voiture doit être un nombre positif.");
      }

      if ($quantity_int > $this->car->getInStock()) {
        $this->addErrorByAttribute("quantity", "La quantité de la voiture ne doit pas dépasser le nombre en stock.");
      }

      return $quantity_int;
    }
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

  public function getCarId()
  {
    return $this->car->getId();
  }

  public function getCar()
  {
    return $this->car;
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
      "carId" => $this->getCarId(),
      "car" => $this->getCar()->getRaw(),
      "quantity" => $this->getQuantity(),
      "createdAt" => $this->getCreatedAt(),
      "updatedAt" => $this->getUpdatedAt(),
      "errors" => $this->getErrors(),
    ];
  }

  public function lock(): void
  {
    $this->locked = true;
    $this->client->lock();
    $this->car->lock();
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