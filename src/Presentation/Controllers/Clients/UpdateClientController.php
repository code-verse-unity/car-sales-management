<?php

namespace App\Presentation\Controllers\Clients;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Clients\IndexClientUseCase;
use App\Data\UseCases\Clients\UpdateClientUseCase;

class UpdateClientController
{
  private UpdateClientUseCase $updateClientUseCase;

  public function __construct(UpdateClientUseCase $updateClientUseCase)
  {
    $this->updateClientUseCase = $updateClientUseCase;
  }

  public function execute(Request $request, Response $response)
  {

    $body = $request->body;
    $params = $request->params;

    $useCaseResult = $this->updateClientUseCase->execute(
      $params['clientId'],
      $body['name'],
      $body['contact']
    );

    if ($useCaseResult instanceof Failure) {
      $response->setStatusCode($useCaseResult->getStatusCode());
      if ($useCaseResult instanceof NotFoundFailure) {
        $response->renderView("_404", ["fatalError" => $useCaseResult->getRaw()]);
      } else if ($useCaseResult instanceof ServerFailure) {
        $response->renderView("_500", ["fatalError" => $useCaseResult->getRaw()]);
      }
    } else {
      // now we need to handle bad request failure if there is one

      // just to remind, we have a raw format of the client updated, with the new property errors
      $client = $useCaseResult;

      if (empty($client["errors"])) {
        // if there is no bad request error, we keep the flow as it is supposed to be
        $indexClientUseCase = new IndexClientUseCase($this->updateClientUseCase->getRepository());
        $clients = $indexClientUseCase->execute();

        $response->renderView("clients", ["clients" => $clients]);
      } else {
        // otherwise, we send the back the view for editing the client, with the raw client, its (invalid) values , and errors
        // the goal is to make an error state on the inputs (input with red border, error message on the bottom...)

        $response->setStatusCode(400); // bad request http code

        $response->renderView("editClientView", [
          "client" => $client
        ]);
      }
    }
  }
}
