<?php

namespace App\Data\UseCases\Revenues;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\Failure;

class IndexRevenueUseCase
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        OrderRepositoryInterface $orderRepository,
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        try {
            // ! we get only the last 6 months
            $revenuePerMonthForLast6Months = $this->orderRepository->getRevenuePerMonthByLastMonths(6);

            return [
                "revenuePerMonthForLast6Months" => $revenuePerMonthForLast6Months
            ];
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}
