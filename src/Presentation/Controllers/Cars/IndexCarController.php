<?php

namespace App\Presentation\Controllers\Cars;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Cars\IndexCarUseCase;

class IndexCarController
{
    private IndexCarUseCase $indexCarUseCase;

    public function __construct(IndexCarUseCase $indexCarUseCase)
    {
        $this->indexCarUseCase = $indexCarUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $query = $request->query;

        $useCaseResult = $this->indexCarUseCase->execute(
            $query["name"] ?? null,
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
            $cars = $useCaseResult;

            $response->renderView(
                "indexCarView",
                [
                    // pass the name query as nameQuery
                    "nameQuery" => $query["name"] ?? null,
                    "cars" => $cars
                ]
            );
        }
    }
}