<?php

namespace App\Data\UseCases\Bills;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Failures\Failure;
use App\Core\Utils\Failures\NotFoundFailure;

class DestroyBillUseCase
{
    public function execute($orderId)
    {
        try {
            $filename = "facture_" . $orderId . ".pdf";

            $path = __DIR__ . "/../../../../private/bills/$filename";

            if (file_exists($path)) {
                unlink($path);
            } else {
                throw new NotFoundFailure();
            }
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}