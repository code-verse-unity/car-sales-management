<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Strings\RandomString;
use App\Data\Models\OrderModel;
use App\Data\UseCases\Bills\UpdateBillUseCase;
use App\Domain\Repositories\OrderRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Domain\Repositories\ClientRepositoryInterface;
use DateTime;

class UpdateOrderUseCase
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

    public function execute($orderId, $clientId, $carsIds, $quantities)
    {
        try {
            $oldOrder = $this->orderRepository->findById($orderId);
            $oldCarsQuantities = $oldOrder->getCarsQuantities();

            $newClient = $this->clientRepository->findById($clientId);

            // format the new carsQuantities
            $carsQuantities = [];
            // if there are many cars and many quantities, they must have the same length
            if (is_array($carsIds) && is_array($quantities) && count($carsIds) === count($quantities)) {
                /*
                maybe the carsId is duplicated, so we sum the quantities,
                ! but quantities must be digit string or int
                ! that should not happen if the front and the request is set correctly
                */
                $unique = [];

                foreach ($carsIds as $i => $carId) {
                    if (array_key_exists($carId, $unique)) {
                        $unique[$carId] += intval($quantities[$i]);
                    } else {
                        $unique[$carId] = intval($quantities[$i]);
                    }
                }

                foreach ($unique as $carId => $quantity) {
                    // TODO reset inStock of the oldCars
                    $carsQuantities[] = [
                        "car" => $this->carRepository->findById($carId),
                        "quantity" => intval($quantity),
                    ];
                }
            } else if (is_string($carsIds) && is_string($quantities)) { // used for a single car and a single quantity
                $carsQuantities[] = [
                    "car" => $this->carRepository->findById($carsIds),
                    "quantity" => $quantities,
                ];
            } else {
                throw new ServerFailure("There is an (developer) error from the inputs.");
            }

            /* Cars that from the old order but not included in the new order */
            $excludedCars = [];

            foreach ($oldCarsQuantities as $oldCarQuantity) {
                $isRetaken = false;
                foreach ($carsQuantities as $carQuantity) {
                    // * Re-add the quantity ordered to the stock
                    if ($carQuantity["car"]->getId() === $oldCarQuantity["car"]->getId()) {
                        $isRetaken = true;
                        $carQuantity["car"]->setInStock($carQuantity["car"]->getInStock() + $oldCarQuantity["quantity"]);
                        break;
                    }
                }

                if (!$isRetaken) {
                    $actualCar = $this->carRepository->findById($oldCarQuantity["car"]->getId());
                    // Re-add the quantity ordered to the stock
                    $actualCar->setInStock($actualCar->getInStock() + $oldCarQuantity["quantity"]);
                    $excludedCars[] = $actualCar;
                }
            }

            $oldOrder->setCarsQuantities($carsQuantities);
            $newCarsQuantities = $oldOrder->getCarsQuantities();

            if (!$oldOrder->hasErrors()) {
                $randomStringGenerator = new RandomString(OrderModel::ID_CHARACTERS);

                $length = count($oldOrder->getCars());
                $orderCarsIds = [];
                $carOrdersIdLength = 21;
                for ($i = 0; $i < $length; $i++) {
                    $orderCarsIds[] = $randomStringGenerator->generate($carOrdersIdLength);
                }

                $this->orderRepository->update(
                    $oldOrder->getId(),
                    $oldOrder->getClientId(),
                    $orderCarsIds,
                    $oldOrder->getCarsIds(),
                    $oldOrder->getQuantities(),
                    $oldOrder->getCreatedAt()->format(DateTime::ATOM),
                    $oldOrder->getUpdatedAt()->format(DateTime::ATOM)
                );

                // save the new state of cars
                foreach ($newCarsQuantities as $carQuantity) {
                    $car = $carQuantity["car"];
                    $quantity = $carQuantity["quantity"];

                    $car->setInStock($car->getInStock() - $quantity); // the number in stock decrease
                    $this->carRepository->update(
                        $car->getId(),
                        $car->getName(),
                        $car->getPrice(),
                        $car->getInStock(),
                        $car->getCreatedAt()->format(DateTime::ATOM),
                        $car->getUpdatedAt()->format(DateTime::ATOM)
                    );
                }

                // also save the new state of cars excluded
                foreach ($excludedCars as $excludedCar) {
                    $this->carRepository->update(
                        $excludedCar->getId(),
                        $excludedCar->getName(),
                        $excludedCar->getPrice(),
                        $excludedCar->getInStock(),
                        $excludedCar->getCreatedAt()->format(DateTime::ATOM),
                        $excludedCar->getUpdatedAt()->format(DateTime::ATOM)
                    );
                }

                $updateBillUseCase = new UpdateBillUseCase($oldOrder);
                $useCaseResult = $updateBillUseCase->execute();
                if ($useCaseResult instanceof Failure) {
                    return $useCaseResult;
                }
            }

            $clients = $this->clientRepository->findAll();
            $clientsRaw = array_map(
                function ($client) {
                    $client->lock();
                    return $client->getRaw();
                },
                $clients
            );

            $cars = $this->carRepository->findAll();
            $carsRaw = array_filter(
                array_map(
                    function ($car) use ($newCarsQuantities) {
                        // * Re-add the quantity ordered in the stock
                        foreach ($newCarsQuantities as $carQuantity) {
                            if ($carQuantity["car"]->getId() === $car->getId()) {
                                $car->setInStock($car->getInStock() + $carQuantity["quantity"]);
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

            $oldOrder->setCarsQuantities(
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
                    $newCarsQuantities
                )
            );

            $oldOrder->lock();
            $orderRaw = $oldOrder->getRaw();

            return [
                "clients" => $clientsRaw,
                "cars" => $carsRaw,
                "order" => $orderRaw
            ];
        } catch (\Throwable $th) {
            exit;
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}
