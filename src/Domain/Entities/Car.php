<?php

namespace App\Domain\Entities;

use DateTime;
use Exception;

abstract class Car implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

  private string $id;
  private string $name;
  private DateTime $createdAt;
  private DateTime $updatedAt;
  private int $price;
  private int $inStock;

  private array $errors;
  private bool $locked;

  public function __construct($id, string $name, int $price, int $inStock, DateTime $createdAt, DateTime $updatedAt)
  {
    $this->id = $this->validateId($id);

    $this->name = $name;
    $this->price = $price;
    $this->inStock = $inStock;

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

  public function getName()
  {
    return $this->name;
  }

  public function setName($name)
  {
    if ($this->locked) {
      throw new Exception("instance locked");
    }

    $this->name = $name;
  }

  public function getPrice()
  {
    return $this->price;
  }

  public function setPrice($price)
  {
    if ($this->locked) {
      throw new Exception("instance locked");
    }

    $this->price = $price;
    $this->triggerUpdate();
  }

  public function triggerUpdate()
  {
    $this->updatedAt = new DateTime();
  }

  public function getInStock()
  {
    return $this->inStock;
  }

  public function setInStock($inStock)
  {
    if ($this->locked) {
      throw new Exception("instance locked");
    }

    $this->inStock = $inStock;
    $this->triggerUpdate();
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
      "name" => $this->getName(),
      "price" => $this->getPrice(),
      "inStock" => $this->getInStock(),
      "createdAt" => $this->getCreatedAt(),
      "updatedAt" => $this->getUpdatedAt()
    ];
  }

  public function lock(): void
  {
    $this->locked = true;
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