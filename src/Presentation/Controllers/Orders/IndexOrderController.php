<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\IndexOrderUseCase;
use DateTime;

class IndexOrderController
{
    private IndexOrderUseCase $indexOrderUseCase;

    public function __construct(IndexOrderUseCase $indexOrderUseCase)
    {
        $this->indexOrderUseCase = $indexOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {
        $query = $request->query;

        $useCaseResult = $this->indexOrderUseCase->execute(
            $query["startAt"],
            $query["endAt"]
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
            $orders = $useCaseResult;

            $startAtValue = $query["startAt"] ? new DateTime($query["startAt"]) : null;
            $endAtValue = $query["endAt"] ? new DateTime($query["endAt"]) : null;

            $response->renderView(
                "orders/indexOrderView",
                [
                    "startAt" => $startAtValue,
                    "endAt" => $endAtValue,
                    "orders" => $orders,
                ]
            );
        }
    }
}