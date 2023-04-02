<?php

namespace App\Presentation\Controllers\Home;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Data\UseCases\Home\ShowHomeUseCase;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;

class ShowHomeController
{
  private ShowHomeUseCase $showHomeUseCase;

  public function __construct($showHomeUseCase)
  {
    $this->showHomeUseCase = $showHomeUseCase;
  }

  public function execute(Request $request, Response $response)
  {
    $useCaseResult = $this->showHomeUseCase->execute();

    if ($useCaseResult instanceof Failure) {
      $response->setStatusCode($useCaseResult->getStatusCode());
      if ($useCaseResult instanceof NotFoundFailure) {
        $response->renderView("_404", ["fatalError" => $useCaseResult->getRaw()]);
      } else if ($useCaseResult instanceof ServerFailure) {
        $response->renderView("_500", ["fatalError" => $useCaseResult->getRaw()]);
      }
    } else {
      $response->renderView(
        "home",
        [
          "clientsCount" => $useCaseResult["clientCount"],
          "carsCount" => $useCaseResult["carsCount"],
          "ordersCount" => $useCaseResult["ordersCount"],
          "ordersCountForLast6Months" => $useCaseResult["ordersCountForLast6Months"],
          "revenue" => $useCaseResult["revenue"],
          "revenueOfLast6Months" => $useCaseResult["revenueOfLast6Months"]
        ]
      );
    }
  }
}