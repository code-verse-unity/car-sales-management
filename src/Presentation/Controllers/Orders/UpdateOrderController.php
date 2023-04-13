<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\UpdateOrderUseCase;

class UpdateOrderController
{
    private UpdateOrderUseCase $updateOrderUseCase;

    public function __construct(UpdateOrderUseCase $updateOrderUseCase)
    {
        $this->updateOrderUseCase = $updateOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $body = $request->body;
        $params = $request->params;

        $useCaseResult = $this->updateOrderUseCase->execute(
            $params["orderId"],
            $body["clientId"],
            $body["carsIds"],
            $body["quantities"]
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
            $order = $useCaseResult["order"];
            $clients = $useCaseResult["clients"];
            $cars = $useCaseResult["cars"];

            if (empty($order["errors"])) {
                $response->redirect("/orders"); // redirect to orders list
            } else {
                $response->setStatusCode(400);

                $response->renderView(
                    "orders/edit",
                    [
                        "clients" => $clients,
                        "cars" => $cars,
                        "order" => $order
                    ]
                );
            }
        }
    }
}
