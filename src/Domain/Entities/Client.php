<?php

namespace App\Domain\Entities;

use DateTime;
use Exception;

abstract class Client implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";

  private string $id;
  private string $name;
  private string $contact;
  private DateTime $createdAt;
  private DateTime $updatedAt;

  private array $errors;
  private bool $locked;

  public function __construct($id, string $name, string $contact, DateTime $createdAt, DateTime $updatedAt)
  {
    $this->id = $this->validateId($id);

    $this->name = $name;
    $this->contact = $contact;

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

  public function getContact()
  {
    return $this->contact;
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
      "contact" => $this->getContact(),
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
