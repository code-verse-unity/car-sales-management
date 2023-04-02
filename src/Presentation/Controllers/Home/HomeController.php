<?php

namespace App\Presentation\Controllers\Home;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;

class HomeController
{
  // private IndexClientUseCase $indexClientUseCase;

  public function __construct()
  {
    // $this->indexClientUseCase = $indexClientUseCase;
  }

  public function execute(Request $request, Response $response)
  {

    $body = $request->body;
    $response->renderView('home');
    // $useCaseResult = $this->indexClientUseCase->execute();

    // if ($useCaseResult instanceof Failure) {
    //   $response->setStatusCode($useCaseResult->getStatusCode());
    //   if ($useCaseResult instanceof NotFoundFailure) {
    //     $response->renderView("_404", ["fatalError" => $useCaseResult->getRaw()]);
    //   } else if ($useCaseResult instanceof ServerFailure) {
    //     $response->renderView("_500", ["fatalError" => $useCaseResult->getRaw()]);
    //   }
    // } else {
    //   $response->renderView("clients", ["clients" => $useCaseResult]);
    // }
  }
}
