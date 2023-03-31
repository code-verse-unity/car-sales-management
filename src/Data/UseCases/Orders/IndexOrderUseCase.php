<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use DateTime;

class IndexOrderUseCase
{
    private OrderRepositoryInterface $orderRepository;


    public function __construct(
        OrderRepositoryInterface $orderRepository,
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute($startAt, $endAt)
    {
        try {
            $startAtValue = null;
            if ($startAt) {
                $startAtValue = new DateTime($startAt);
                // this is made because we want to search only by date, not date and time
                $startAtValue->setTime(0, 0, 0, 0);
            }

            $endAtValue = null;
            if ($endAt) {
                $endAtValue = new DateTime($endAt);
                $endAtValue->setTime(23, 59, 59, 999999);
            }

            $orders = $this->orderRepository->findAll($startAtValue, $endAtValue);

            $ordersRaw = array_map(
                function ($order) {
                    $order->lock();
                    return $order->getRaw();
                },
                $orders
            );

            return $ordersRaw;
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}