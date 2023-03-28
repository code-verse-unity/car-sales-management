<?php

namespace App\Domain\Entities;

use App\Core\Utils\Failures\ServerFailure;
use DateTime;

abstract class Car implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
  const NAME_MIN_LENGTH = 1;
  const PRICE_MIN_VALUE = 1;
  const PRICE_CURRENCY_CODE = "MGA";

  private $id;
  private $name;
  private $createdAt;
  private $updatedAt;
  private $price;
  private $inStock;

  private array $errors = [];
  private bool $locked = false;

  public function __construct($id, $name, $price, $inStock, $createdAt, $updatedAt)
  {
    $this->id = $this->validateId($id);
    $this->name = $this->validateName($name);
    $this->price = $this->validatePrice($price);
    $this->inStock = $this->validateInStock($inStock);
    $this->createdAt = $this->validateCreatedAt($createdAt);
    $this->updatedAt = $this->validateUpdatedAt($updatedAt);
  }

  private function validateId($id)
  {
    if (!is_string($id)) {
      $this->addErrorByAttribute("id", "L'identifiant d'une voiture doit être une chaîne de caractères.");
    }

    if (strlen($id) !== self::ID_LENGTH) {
      $this->addErrorByAttribute("id", "L'identifiant d'une voite doit avoir" . self::ID_LENGTH . " caractères.");
    }

    if (!$this->containsOnlyChars($id, str_split(self::ID_CHARACTERS))) {
      $this->addErrorByAttribute("id", "L'identifiant d'une voiture contient des caractères non autorisées.");
    }

    return $id;
  }

  private function validateName($name)
  {
    $trimmed = trim($name);

    if (!isset($name) || $name === null || empty($trimmed)) {
      $this->addErrorByAttribute("name", "Le nom de la voiture est obligatoire.");
    }

    if (!is_string($name)) {
      $this->addErrorByAttribute("name", "Le nom de la voiture doit être une chaîne de caractères.");
    }

    if (strlen($trimmed) < self::NAME_MIN_LENGTH) {
      $this->addErrorByAttribute("name", "La longueur minimale pour la voiture du client est de " . self::NAME_MIN_LENGTH . " caractères.");
    }

    return $trimmed;
  }

  private function validatePrice($price)
  {
    if (!isset($price) || $price === null) {
      $this->addErrorByAttribute("price", "Le prix de la voiture est obligatoire.");
    }

    if (!is_numeric($price)) { // it accept both int and float
      $this->addErrorByAttribute("price", "Le prix de la voiture doit être en valeur numérique.");

      return $price; // save even if it is not valid
    } else {
      $float_price = floatval($price);

      if ($float_price < self::PRICE_MIN_VALUE) {
        $this->addErrorByAttribute("price", "Le prix de la voiture doit être au moins " . self::PRICE_MIN_VALUE . " " . self::PRICE_CURRENCY_CODE . ".");
      }

      return $float_price;
    }
  }

  private function validateInStock($inStock)
  {
    if (!isset($inStock) || $inStock === null) {
      $this->addErrorByAttribute("inStock", "Le nombre en stock de la voiture est obligatoire.");
    }

    if (!is_numeric($inStock)) {
      $this->addErrorByAttribute("inStock", "Le nombre en stock de la voiture doit être un nombre entier.");

      return $inStock;
    } else {
      $inStock_int = intval($inStock);

      if ($inStock_int < 0) {
        $this->addErrorByAttribute("inStock", "Le nombre en stock de la voiture doit être un nombre positif.");
      }

      return $inStock_int;
    }
  }

  private function validateCreatedAt($createdAt)
  {
    if (!$createdAt instanceof Datetime) {
      $this->addErrorByAttribute("createdAt", "La date de creation de la voiture n'est pas valide.");
    }

    return $createdAt;
  }

  private function validateUpdatedAt($updatedAt)
  {
    if (!$updatedAt instanceof Datetime) {
      $this->addErrorByAttribute("updatedAt", "La date de creation de la voiture n'est pas valide.");
    }

    return $updatedAt;
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
      throw new ServerFailure("instance locked");
    }

    $this->removeErrorsByAttribute("name");
    $this->name = $this->validateName($name);

    $this->triggerUpdate();
  }

  public function getPrice()
  {
    return $this->price;
  }

  public function setPrice($price)
  {
    if ($this->locked) {
      throw new ServerFailure("instance locked");
    }

    $this->removeErrorsByAttribute("price");
    $this->price = $this->validatePrice($price);

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
      throw new ServerFailure("instance locked");
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
      "updatedAt" => $this->getUpdatedAt(),
      "errors" => $this->getErrors()
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