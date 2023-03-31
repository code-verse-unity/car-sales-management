<?php

namespace App\Data\UseCases\Bills;

use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Failures\Failure;

class DownloadBillUseCase
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute($id)
    {
        try {
            $order = $this->orderRepository->findById($id);

            $filename = "facture_" . $order->getId() . ".pdf";

            header("Content-Type: application/pdf");
            header("Content-Disposition: attachement; filename=\"" . $filename . "\"");

            require_once __DIR__ . "/../../../../private/bills/$filename";
            exit;
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}