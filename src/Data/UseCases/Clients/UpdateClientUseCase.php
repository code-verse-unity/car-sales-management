<?php

namespace App\Data\UseCases\Clients;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use DateTime;

class UpdateClientUseCase
{
  private ClientRepositoryInterface $clientRepository;

  public function __construct(ClientRepositoryInterface $clientRepository)
  {
    $this->clientRepository = $clientRepository;
  }

  public function execute($id, $name, $contact)
  {
    try {
      // we get an intance of Client, otherwise an NotFoundFailure will be thrown
      $client = $this->clientRepository->findById($id);

      // we use the setters, updatedAt will be updated automatically
      $client->setName($name);
      $client->setContact($contact);

      /*
      since there is no exception thrown if there is a bad request,
      we check if there is an error
      If there is no error, we update it on the repository,
      Otherwise we just the return it, and the controller handle the errors
      */
      if (!$client->hasErrors()) {
        // then we update on the repository
        $this->clientRepository->update(
          $client->getId(),
          $client->getName(),
          $client->getContact(),
          $client->getCreatedAt()->format(DateTime::ATOM),
          $client->getUpdatedAt()->format(DateTime::ATOM)
        );
      }

      // ! lock to make read only
      $client->lock();

      /*
      Now, the raw format has a errors property
      if it is empty, it means there is no error
      Otherwise, it has one, or more
      */
      return $client->getRaw();
    } catch (\Throwable $th) {
      if ($th instanceof Failure) {
        return $th;
      } else {
        return new ServerFailure();
      }
    }
  }

  public function getRepository()
  {
    return $this->clientRepository;
  }
}
