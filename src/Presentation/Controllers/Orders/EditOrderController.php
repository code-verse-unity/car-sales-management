<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\EditOrderUseCase;

class EditOrderController
{
    private EditOrderUseCase $editOrderUseCase;

    public function __construct(EditOrderUseCase $editOrderUseCase)
    {
        $this->editOrderUseCase = $editOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {
        $params = $request->params;

        $useCaseResult = $this->editOrderUseCase->execute($params["orderId"]);

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
            $order = $useCaseResult["order"];

            $response->renderView(
                "orders/edit",
                [
                    "order" => $order,
                    "clients" => $clients,
                    "cars" => $cars,
                ]
            );
        }
    }
}
