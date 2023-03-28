<?php

namespace App\Data\UseCases\Orders;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Strings\RandomString;
use App\Data\Models\OrderModel;
use App\Data\Models\CarModel;
use App\Core\Utils\Failures\Failure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Domain\Repositories\ClientRepositoryInterface;
use DateTime;

class CreateOrderUseCase
{
    private ClientRepositoryInterface $clientRepository;
    private CarRepositoryInterface $carRepository;

    public function __construct(
        ClientRepositoryInterface $clientRepository,
        CarRepositoryInterface $carRepository
    ) {
        $this->clientRepository = $clientRepository;
        $this->carRepository = $carRepository;
    }

    public function execute()
    {
        try {
            $clients = $this->clientRepository->findAll();
            $cars = $this->carRepository->findByMinInStock(1); // get cars with one or more inStock

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

            return [
                "clients" => $clientsRaw,
                "cars" => $carsRaw
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