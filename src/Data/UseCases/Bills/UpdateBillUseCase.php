<?php

namespace App\Data\UseCases\Bills;

use App\Core\Utils\Failures\ServerFailure;
use App\Data\Models\OrderModel;
use App\Core\Utils\Failures\Failure;
use Knp\Snappy\Pdf;

class UpdateBillUseCase
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
            $outputPath = __DIR__ . "/../../../../private/bills/$filename";

            // remove if the file exists
            if (file_exists($outputPath)) {
                unlink($outputPath);
            }

            // link to the wkhtmltopdf-amd64 executable
            $snappy = new Pdf(__DIR__ . "/../../../../vendor/bin/wkhtmltopdf-amd64");

            // generate the pdf
            $snappy->generateFromHtml($content, $outputPath);
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}
