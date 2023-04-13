<?php

namespace App\Presentation\Controllers\Orders;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Orders\DestroyOrderUseCase;

class DestroyOrderController
{
    private DestroyOrderUseCase $destroyOrderUseCase;

    public function __construct(DestroyOrderUseCase $destroyOrderUseCase)
    {
        $this->destroyOrderUseCase = $destroyOrderUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $params = $request->params;

        $useCaseResult = $this->destroyOrderUseCase->execute(
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
            $response->redirect("/orders"); // redirect to orders list
        }
    }
}