<?php

namespace App\Presentation\Controllers\Clients;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Clients\IndexClientUseCase;
use App\Data\UseCases\Clients\StoreClientUseCase;

class StoreClientController
{
  private StoreClientUseCase $storeClientUseCase;

  public function __construct(StoreClientUseCase $storeClientUseCase)
  {
    $this->storeClientUseCase = $storeClientUseCase;
  }

  public function execute(Request $request, Response $response)
  {

    $body = $request->body;

    $useCaseResult = $this->storeClientUseCase->execute($body['name'], $body['contact']);

    if ($useCaseResult instanceof Failure) {
      $response->setStatusCode($useCaseResult->getStatusCode());
      if ($useCaseResult instanceof NotFoundFailure) {
        $response->renderView("_404", ["fatalError" => $useCaseResult->getRaw()]);
      } else if ($useCaseResult instanceof ServerFailure) {
        $response->renderView("_500", ["fatalError" => $useCaseResult->getRaw()]);
      }
    } else {
      $indexClientUseCase = new IndexClientUseCase($this->storeClientUseCase->getRepository());
      $clients = $indexClientUseCase->execute();

      $response->renderView("clients/index", ["clients" => $clients]);
    }
  }
}
