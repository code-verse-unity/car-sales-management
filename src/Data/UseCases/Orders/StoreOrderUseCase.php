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

    public function execute($clientId, $carsIds, $quantities)
    {
        try {
            $client = $this->clientRepository->findById($clientId);

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

            $randomStringGenerator = new RandomString(OrderModel::ID_CHARACTERS);

            $order = new OrderModel($randomStringGenerator->generate(OrderModel::ID_LENGTH), $client, $carsQuantities, null, null);

            $carsIds = $order->getCarsIds();
            $length = count($carsIds);
            $orderCarsIds = [];

            $carOrdersIdLength = 21;

            for ($i = 0; $i < $length; $i++) {
                $orderCarsIds[] = $randomStringGenerator->generate($carOrdersIdLength);
            }

            if (!$order->hasErrors()) {
                $this->orderRepository->save(
                    $order->getId(),
                    $order->getClientId(),
                    $orderCarsIds,
                    $carsIds,
                    $order->getQuantities(),
                    $order->getCreatedAt()->format(DateTime::ATOM),
                    $order->getUpdatedAt()->format(DateTime::ATOM)
                );

                $carsQuantities = $order->getCarsQuantities(); // formatted by the Order entity, have extra property subtotal

                foreach ($carsQuantities as $carQuantity) {
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
            }

            $clients = $this->clientRepository->findAll();
            $cars = $this->carRepository->findByMinInStock(1); // we get only cars with one or more inStock    

            $clientsRaw = array_map(
                function ($client) {
                    $client->lock();
                    return $client->getRaw();
                },
                $clients
            );

            $carsRaw = array_map(
                function ($car) {
                    $car->lock();
                    return $car->getRaw();
                },
                $cars
            );

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