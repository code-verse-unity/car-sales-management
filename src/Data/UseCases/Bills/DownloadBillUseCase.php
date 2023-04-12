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

            $path = __DIR__ . "/../../../../private/bills/$filename";

            // we create the bill if it doesn't exist
            if (!file_exists($path)) {
                $storeBillUseCase = new StoreBillUseCase($order);

                /*
                ! if DownloadBillUseCase and StoreBillUseCase are in different directory (not the actual case),
                ! the bill pdf will be created in a wrong directory
                */
                $useCaseResult = $storeBillUseCase->execute();

                if ($useCaseResult instanceof Failure) {
                    return $useCaseResult;
                }
            }

            header("Content-Type: application/pdf");
            header("Content-Disposition: attachement; filename=\"" . $filename . "\"");
            readfile($path);
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
