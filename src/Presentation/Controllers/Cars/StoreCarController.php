<?php

namespace App\Presentation\Controllers\Cars;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Cars\StoreCarUseCase;

class StoreCarController
{
    private StoreCarUseCase $storeCarUseCase;

    public function __construct(StoreCarUseCase $storeCarUseCase)
    {
        $this->storeCarUseCase = $storeCarUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $body = $request->body;

        $useCaseResult = $this->storeCarUseCase->execute(
            $body["name"],
            $body["price"],
            $body["inStock"]
        );

        if ($useCaseResult instanceof Failure) {
            $response->setStatusCode($useCaseResult->getStatusCode());
            if ($useCaseResult instanceof NotFoundFailure) {
                $response->renderView(
                    "_404",
                    [
                        "fatalError" => $useCaseResult->getRaw()
                    ]
                );
            } else if ($useCaseResult instanceof ServerFailure) {
                $response->renderView(
                    "_500",
                    [
                        "fatalError" => $useCaseResult->getRaw()
                    ]
                );
            }
        } else {
            $car = $useCaseResult;
            if (empty($car["errors"])) {
                $response->redirect("/cars");
            } else {
                $response->renderView(
                    "cars/create",
                    [
                        "car" => $car
                    ]
                );
            }
        }
    }
}
