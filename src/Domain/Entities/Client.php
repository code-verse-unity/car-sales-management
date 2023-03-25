<?php

namespace App\Domain\Entities;

use App\Core\Utils\Failures\ServerFailure;
use DateTime;

abstract class Client implements EntityInterface
{
  const ID_LENGTH = 21;
  const ID_CHARACTERS = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ_";
  const NAME_MIN_LENGTH = 1;
  const CONTACT_MIN_LENGTH = 1;

  private $id;
  private $name;
  private $contact;
  private $createdAt;
  private $updatedAt;

  private array $errors = [];
  private bool $locked = false;

  /*
  ! It accepts any type of parameter,
  ! but it validate them when creating a new instance
  ! It doesn't throw an exception, but instead, it store in errors
  ! So use the hasErrors method and set fix the error before saving in a repository
  ! If id, createdAt, updatedAt are not valid, mostly it's an error from the developers,
  ! and there is no way to fix it from through this class,
  ! instead fix the errors and create a new instance
  */
  public function __construct($id, $name, $contact, $createdAt, $updatedAt)
  {
    $this->id = $this->validateId($id);
    $this->name = $this->validateName($name);
    $this->contact = $this->validateContact($contact);
    $this->createdAt = $this->validateCreatedAt($createdAt);
    $this->updatedAt = $this->validateUpdatedAt($updatedAt);
  }

  private function validateId($id)
  {
    if (!is_string($id)) {
      $this->addErrorByAttribute("id", "L'identifiant d'un client doit être une chaîne de caractères."); // TODO set to more human readable message
    }

    if (strlen($id) !== self::ID_LENGTH) {
      $this->addErrorByAttribute("id", "L'identifiant d'un client doit avoir" . self::ID_LENGTH . " caractères.");
    }

    if (!$this->containsOnlyChars($id, str_split(self::ID_CHARACTERS))) {
      $this->addErrorByAttribute("id", "L'identifiant d'un client contient des caractères non autorisées.");
    }

    return $id;
  }

  private function validateName($name)
  {
    $trimmed = trim($name);

    if (!isset($name) || $name === null || empty($trimmed)) {
      $this->addErrorByAttribute("name", "Le nom du client est obligatoire.");
    }

    if (!is_string($name)) {
      $this->addErrorByAttribute("name", "Le nom du client doit être une chaîne de caractères.");
    }

    if (strlen($trimmed) < self::NAME_MIN_LENGTH) {
      $this->addErrorByAttribute("name", "La longueur minimale pour le nom du client est de " . self::NAME_MIN_LENGTH . " caractères.");
    }

    return $trimmed;
  }

  private function validateContact($contact)
  {
    $trimmed = trim($contact);

    if (!isset($contact) || $contact === null || empty($trimmed)) {
      $this->addErrorByAttribute("contact", "Le contact du client est obligatoire.");
    }

    if (!is_string($contact)) {
      $this->addErrorByAttribute("contact", "Le contact du client doit être une chaîne de caractères.");
    }

    if (strlen($trimmed) < self::CONTACT_MIN_LENGTH) {
      $this->addErrorByAttribute("contact", "La longueur minimale pour le contact du client est de " . self::CONTACT_MIN_LENGTH . " caractères.");
    }

    return $trimmed;
  }

  private function validateCreatedAt($createdAt)
  {
    if (!$createdAt instanceof Datetime) {
      $this->addErrorByAttribute("createdAt", "La date de creation du client n'est pas valide.");
    }

    return $createdAt;
  }

  private function validateUpdatedAt($updatedAt)
  {
    if (!$updatedAt instanceof Datetime) {
      $this->addErrorByAttribute("updatedAt", "La date de creation du client n'est pas valide.");
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

    // we remove existing errors and validate
    $this->removeErrorsByAttribute("name");
    $this->name = $this->validateName($name);

    $this->triggerUpdate();
  }

  private function triggerUpdate()
  {
    $this->updatedAt = new DateTime();
  }

  public function getContact()
  {
    return $this->contact;
  }

  public function setContact($contact)
  {
    if ($this->locked) {
      throw new ServerFailure("instance locked");
    }

    $this->removeErrorsByAttribute("contact");
    $this->contact = $this->validateContact($contact);

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
      "contact" => $this->getContact(),
      "createdAt" => $this->getCreatedAt(),
      "updatedAt" => $this->getUpdatedAt(),
      "errors" => $this->getErrors(), // also add errors as raw
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

  /*
  if the attribute does not exist in the errors, it will throw a TypeError
  so use hasError($attribute) before using it
  */
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