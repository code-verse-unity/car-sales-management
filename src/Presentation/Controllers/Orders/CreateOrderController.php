<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\CreateOrderUseCase;

class CreateOrderController
{
    private CreateOrderUseCase $createOrderUseCase;

    public function __construct(CreateOrderUseCase $createOrderUseCase)
    {
        $this->createOrderUseCase = $createOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {
        $useCaseResult = $this->createOrderUseCase->execute();

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
            $clients = $useCaseResult["clients"];
            $cars = $useCaseResult["cars"];

            $response->renderView(
                "createOrderView",
                [
                    "clients" => $clients,
                    "cars" => $cars,
                ]
            );
        }
    }
}