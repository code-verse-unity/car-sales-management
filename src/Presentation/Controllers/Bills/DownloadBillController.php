<?php

namespace App\Presentation\Controllers\Bills;

use App\Core\Requests\Request;
use App\Core\Responses\Response;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Bills\DownloadBillUseCase;

class DownloadBillController
{
    private DownloadBillUseCase $downloadBillUseCase;

    public function __construct(DownloadBillUseCase $downloadBillUseCase)
    {
        $this->downloadBillUseCase = $downloadBillUseCase;
    }

    public function execute(Request $request, Response $response)
    {

        $params = $request->params;

        $useCaseResult = $this->downloadBillUseCase->execute(
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
        }
    }
}