<?php

namespace App\Presentation\Controllers\Clients;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Clients\ShowClientUseCase;

class EditClientController
{
  private ShowClientUseCase $showClientUseCase;

  public function __construct(ShowClientUseCase $showClientUseCase)
  {
    $this->showClientUseCase = $showClientUseCase;
  }

  public function execute(Request $request, Response $response)
  {

    $params = $request->params;

    $useCaseResult = $this->showClientUseCase->execute($params['clientId']);

    if ($useCaseResult instanceof Failure) {
      $response->setStatusCode($useCaseResult->getStatusCode());
      if ($useCaseResult instanceof NotFoundFailure) {
        $response->renderView("_404", ["fatalError" => $useCaseResult->getRaw()]);
      } else if ($useCaseResult instanceof ServerFailure) {
        $response->renderView("_500", ["fatalError" => $useCaseResult->getRaw()]);
      }
    } else {
      $response->renderView("clients/edit", ["client" => $useCaseResult]);
    }
  }
}
