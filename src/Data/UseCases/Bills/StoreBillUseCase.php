<?php

namespace App\Data\UseCases\Bills;

use App\Core\Utils\Failures\ServerFailure;
use App\Data\Models\OrderModel;
use App\Core\Utils\Failures\Failure;
use Knp\Snappy\Pdf;

class StoreBillUseCase
{
    private OrderModel $order;

    public function __construct(
        OrderModel $order
    ) {
        $this->order = $order;
    }

    public function execute()
    {
        try {
            // ! Do not delete
            $order = $this->order->getRaw();

            ob_start();
            require_once __DIR__ . "/../../../Presentation/Views/_bill.php";
            $content = ob_get_clean();

            $filename = "facture_" . $order["id"] . ".pdf";

            // link to the wkhtmltopdf-amd64 executable
            $snappy = new Pdf(__DIR__ . "/../../../../vendor/bin/wkhtmltopdf-amd64");
            $snappy->generateFromHtml($content, __DIR__ . "/../../../../private/bills/$filename");
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}