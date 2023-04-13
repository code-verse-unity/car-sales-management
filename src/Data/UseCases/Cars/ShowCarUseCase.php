<?php

namespace App\Data\UseCases\Cars;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Core\Utils\Failures\Failure;

class ShowCarUseCase
{
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function execute($id)
    {
        try {
            $car = $this->carRepository->findById($id);

            $car->lock();

            return $car->getRaw();
        } catch (\Throwable $th) {
            if ($th instanceof Failure) {
                return $th;
            } else {
                return new ServerFailure();
            }
        }
    }
}