<?php

namespace App\Data\UseCases\Home;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Domain\Repositories\OrderRepositoryInterface;

class ShowHomeUseCase
{
    private ClientRepositoryInterface $clientRepository;
    private CarRepositoryInterface $carRepository;
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        CarRepositoryInterface $carRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->carRepository = $carRepository;
        $this->orderRepository = $orderRepository;
    }

    public function execute()
    {
        try {
            $clientCount = $this->clientRepository->getCount();
            $carsCount = $this->carRepository->getCount();
            $ordersCount = $this->orderRepository->getCount();
            $ordersCountForLast6Months = $this->orderRepository->getCountByLastMonths(6);
            $revenue = $this->orderRepository->getRevenue();
            $revenueOfLast6Months = $this->orderRepository->getRevenueByLastMonths(6);

            return [
                "clientCount" => $clientCount,
                "carsCount" => $carsCount,
                "ordersCount" => $ordersCount,
                "ordersCountForLast6Months" => $ordersCountForLast6Months,
                "revenue" => $revenue,
                "revenueOfLast6Months" => $revenueOfLast6Months
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