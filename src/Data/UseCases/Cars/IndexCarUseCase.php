<?php

namespace App\Data\UseCases\Cars;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Core\Utils\Failures\Failure;

class IndexCarUseCase
{
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function execute($nameQuery = null) // * $nameQuery is used to search car by name
    {
        try {
            $cars = [];

            if ($nameQuery) {
                $cars = $this->carRepository->findByName($nameQuery);
            } else {
                $cars = $this->carRepository->findAll();
            }

            return array_map(function ($car) {
                $car->lock();
                return $car->getRaw();
            }, $cars);
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}