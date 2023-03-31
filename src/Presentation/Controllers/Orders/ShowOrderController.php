<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\ShowOrderUseCase;

class ShowOrderController
{
    private ShowOrderUseCase $showOrderUseCase;

    public function __construct(ShowOrderUseCase $showOrderUseCase)
    {
        $this->showOrderUseCase = $showOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {
        $params = $request->params;

        $useCaseResult = $this->showOrderUseCase->execute(
            $params["orderId"],
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
            $order = $useCaseResult;

            $response->renderView(
                "orders/showOrderView",
                [
                    "order" => $order,
                ]
            );
        }
    }
}