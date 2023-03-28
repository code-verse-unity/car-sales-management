<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\StoreOrderUseCase;

class StoreOrderController
{
    private StoreOrderUseCase $storeOrderUseCase;

    public function __construct(StoreOrderUseCase $storeOrderUseCase)
    {
        $this->storeOrderUseCase = $storeOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $body = $request->body;

        $useCaseResult = $this->storeOrderUseCase->execute(
            $body["clientId"],
            $body["carId"],
            $body["quantity"]
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

            if (empty($order["errors"])) {
                $response->redirect("/orders"); // redirect to orders list
            } else {
                $response->renderView(
                    "createOrderView",
                    [
                        "order" => $order
                    ]
                );
            }
        }
    }
}