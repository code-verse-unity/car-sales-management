<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Data\UseCases\Bills\DestroyBillUseCase;
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
            $order = $this->orderRepository->findById($orderId);

            $this->orderRepository->delete($order->getId());

            $destroyBillUseCase = new DestroyBillUseCase();
            $useCaseResult = $destroyBillUseCase->execute($order->getId());

            if ($useCaseResult instanceof Failure) {
                return $useCaseResult;
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