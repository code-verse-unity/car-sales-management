<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\Failure;

class EditOrderUseCase
{
    private OrderRepositoryInterface $orderRepository;
    private ClientRepositoryInterface $clientRepository;
    private CarRepositoryInterface $carRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        CarRepositoryInterface $carRepository,
        OrderRepositoryInterface $orderRepository,
    ) {
        $this->clientRepository = $clientRepository;
        $this->carRepository = $carRepository;
        $this->orderRepository = $orderRepository;
    }

    public function execute($orderId)
    {
        try {
            $order = $this->orderRepository->findById($orderId);


            $clients = $this->clientRepository->findAll();
            $clientsRaw = array_map(
                function ($client) {
                    $client->lock();
                    return $client->getRaw();
                },
                $clients
            );

            $orderCarsQuantities = $order->getCarsQuantities();

            // we get all cars even if they have 0 inStock
            $cars = $this->carRepository->findAll();
            $carsRaw = array_filter(
                array_map(
                    function ($car) use ($orderCarsQuantities) {
                        // * Re-add the quantity ordered in the stock
                        foreach ($orderCarsQuantities as $orderCarQuantity) {
                            if ($orderCarQuantity["car"]->getId() === $car->getId()) {
                                $car->setInStock($car->getInStock() + $orderCarQuantity["quantity"]);
                                break;
                            }
                        }
                        $car->lock();
                        return $car->getRaw();
                    },
                    $cars
                ),
                // filter only the cars with one or more inStock
                function ($car) {
                    return $car["inStock"] > 0;
                }
            );

            // refresh inStock of cars
            // it should not have any error
            $order->setCarsQuantities(
                array_map(
                    function ($carQuantity) use ($cars) {
                        $newCar = null;
                        foreach ($cars as $car) {
                            if ($car->getId() === $carQuantity["car"]->getId()) {
                                $carQuantity["car"]->setInStock($car->getInStock());
                                $newCar = $carQuantity["car"];
                            }
                        }

                        return [
                            "car" => $newCar ?? $carQuantity["car"],
                            "quantity" => $carQuantity["quantity"],
                        ];
                    },
                    $orderCarsQuantities
                )
            );

            $order->lock();
            $orderRaw = $order->getRaw();

            return [
                "order" => $orderRaw,
                "clients" => $clientsRaw,
                "cars" => $carsRaw,
            ];
        } catch (\Throwable $th) {
            var_dump($th);
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}
