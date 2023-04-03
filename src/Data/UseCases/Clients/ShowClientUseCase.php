<?php

namespace App\Data\UseCases\Clients;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Data\Models\ClientModel;
use App\Core\Utils\Failures\Failure;

class ShowClientUseCase
{
  private ClientRepositoryInterface $clientRepository;

  public function __construct(ClientRepositoryInterface $clientRepository)
  {
    $this->clientRepository = $clientRepository;
  }

  public function execute(string $id)
  {
    try {
      $client = $this->clientRepository->findById($id);

      $client->lock();

      return $client->getRaw();
    } catch (\Throwable $th) {
      if ($th instanceof Failure) {
        return $th;
      } else {
        return new ServerFailure();
      }
    }
  }
}
