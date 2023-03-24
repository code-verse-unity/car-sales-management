<?php

namespace App\Domain\Entities;

use DateTime;
use Exception;

abstract class Order implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

  private string $id;
  private Client $client;
  private Car $car;
  private int $quantity;

  private DateTime $createdAt;
  private DateTime $updatedAt;

  private array $errors;
  private bool $locked;

  public function __construct($id, Client $client, Car $car, int $quantity, DateTime $createdAt, DateTime $updatedAt)
  {
    $this->id = $this->validateId($id);

    $this->quantity = $quantity;
    $this->client = $client;
    $this->car = $car;

    $this->createdAt = $createdAt;
    $this->updatedAt = $updatedAt;
  }

  private function validateId($id)
  {
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

  public function setQuantity($quantity) {
    if ($this->locked) {
      throw new Exception("instance locked");
    }

    $this->quantity = $quantity;
    $this->triggerUpdate();
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
      "carId" => $this->getClientId(),
      "car" => $this->car->getRaw(),
      "quantity" => $this->getQuantity(),
      "createdAt" => $this->getCreatedAt(),
      "updatedAt" => $this->getUpdatedAt()
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
    return count($this->errors) === 1;
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
}