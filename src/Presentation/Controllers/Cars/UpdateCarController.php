<?php

namespace App\Presentation\Controllers\Cars;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Cars\UpdateCarUseCase;

class UpdateCarController
{
    private UpdateCarUseCase $updateCarUseCase;

    public function __construct(UpdateCarUseCase $updateCarUseCase)
    {
        $this->updateCarUseCase = $updateCarUseCase;
    }

    public function execute(Request $request, Response $response)
    {
        $body = $request->body;
        $params = $request->params;
        $useCaseResult = $this->updateCarUseCase->execute(
            $params["carId"],
            $body["name"],
            $body["inStock"],
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
                $response->setStatusCode(400);

                $response->renderView(
                    "cars/edit",
                    [
                        "car" => $car
                    ]
                );
            }
        }
    }
}
