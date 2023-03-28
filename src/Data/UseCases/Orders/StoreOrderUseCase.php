<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Strings\RandomString;
use App\Data\Models\OrderModel;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Data\Models\CarModel;
use App\Core\Utils\Failures\Failure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Domain\Repositories\ClientRepositoryInterface;
use DateTime;

class StoreOrderUseCase
{
    private OrderRepositoryInterface $orderRepository;
    private ClientRepositoryInterface $clientRepository;
    private CarRepositoryInterface $carRepository;


    public function __construct(
        OrderRepositoryInterface $orderRepository,
        ClientRepositoryInterface $clientRepository,
        CarRepositoryInterface $carRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->clientRepository = $clientRepository;
        $this->carRepository = $carRepository;
    }

    public function execute($clientId, $carId, $quantity)
    {
        try {
            $client = $this->clientRepository->findById($clientId);
            $car = $this->carRepository->findById($carId);

            $randomStringGenerator = new RandomString(CarModel::ID_CHARACTERS);
            $id = $randomStringGenerator->generate(CarModel::ID_LENGTH);

            $order = new OrderModel($id, $client, $car, $quantity, null, null);

            if (!$order->hasErrors()) {
                $this->orderRepository->save(
                    $order->getId(),
                    $order->getClientId(),
                    $order->getCarId(),
                    $order->getQuantity(),
                    $order->getCreatedAt()->format(DateTime::ATOM),
                    $order->getUpdatedAt()->format(DateTime::ATOM)
                );
            }

            $order->lock();

            return $order->getRaw();
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}