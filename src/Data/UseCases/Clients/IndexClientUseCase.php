<?php

namespace App\Data\UseCases\Clients;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Strings\RandomString;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Data\Models\ClientModel;
use App\Core\Utils\Failures\Failure;
use DateTime;

class IndexClientUseCase
{
  private ClientRepositoryInterface $clientRepository;

  public function __construct(ClientRepositoryInterface $clientRepository)
  {
    $this->clientRepository = $clientRepository;
  }

  public function execute()
  {
    try {
      $randomStringGenerator = new RandomString(ClientModel::ID_CHARACTERS);
      $id = $randomStringGenerator->generate(ClientModel::ID_LENGTH);

      // $client = new ClientModel($id, $firstName, $lastName, $email, $password, null, null, null);

      return $this->clientRepository->findAll();

      // ! lock to make read only
      // $client->lock();

      // return $client->getRaw();

    } catch (\Throwable $th) {
      if ($th instanceof Failure) {
        return $th;
      } else {
        return new ServerFailure();
      }
    }
  }
}
