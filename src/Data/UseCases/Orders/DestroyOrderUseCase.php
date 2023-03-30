<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\Failure;

class DestroyOrderUseCase
{
    private OrderRepositoryInterface $orderRepository;


    public function __construct(
        OrderRepositoryInterface $orderRepository,
    ) {
        $this->orderRepository = $orderRepository;
    }

    public function execute($orderId)
    {
        try {
            $this->orderRepository->delete($orderId);
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}