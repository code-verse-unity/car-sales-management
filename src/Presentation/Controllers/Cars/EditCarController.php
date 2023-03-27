<?php

namespace App\Presentation\Controllers\Cars;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Cars\ShowCarUseCase;

class EditCarController
{
    private ShowCarUseCase $showCarUseCase;

    public function __construct(ShowCarUseCase $showCarUseCase)
    {
        $this->showCarUseCase = $showCarUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $params = $request->params;

        $useCaseResult = $this->showCarUseCase->execute(
            $params["carId"],
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

            $response->renderView(
                "editCarView",
                [
                    "car" => $car
                ]
            );
        }
    }
}