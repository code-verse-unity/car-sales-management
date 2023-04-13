<?php

namespace App\Data\UseCases\Cars;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use DateTime;

class UpdateCarUseCase
{
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    // for now, only name and inStock can be updated
    // ? because updating the price will modify not only future orders but also orders already made, which change the incomes
    public function execute($id, $name, $inStock)
    {
        try {
            $car = $this->carRepository->findById($id);

            $car->setName($name);
            $car->setInStock($inStock);

            if (!$car->hasErrors()) {
                $this->carRepository->update(
                    $car->getId(),
                    $car->getName(),
                    $car->getPrice(),
                    $car->getInStock(),
                    $car->getCreatedAt()->format(DateTime::ATOM),
                    $car->getUpdatedAt()->format(DateTime::ATOM)
                );
            }

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