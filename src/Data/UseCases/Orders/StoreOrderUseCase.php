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
            $clients = $this->clientRepository->findAll();
            $cars = $this->carRepository->findAll();

            $clientsRaw = array_map(
                function ($client) {
                    $client->lock();
                    return $client->getRaw();
                },
                $clients
            );

            // only get car with one or more inStock
            $carsFiltered = array_filter(
                $cars,
                function ($car) {
                    return $car->getInStock() > 0;
                }
            );

            $carsRaw = array_map(
                function ($car) {
                    $car->lock();
                    return $car->getRaw();
                },
                $carsFiltered
            );

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

                // this shouldn't create errors if the setQuantity and getQuantity are well set
                // we subtract inStock of the car and save it
                $car->setInStock($car->getInStock() - $order->getQuantity());
                $this->carRepository->update(
                    $car->getId(),
                    $car->getName(),
                    $car->getPrice(),
                    $car->getInStock(),
                    $car->getCreatedAt()->format(DateTime::ATOM),
                    $car->getUpdatedAt()->format(DateTime::ATOM)
                );
            }

            $order->lock();

            return [
                "clients" => $clientsRaw,
                "cars" => $carsRaw,
                "order" => $order->getRaw()
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