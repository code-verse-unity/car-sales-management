<?php

namespace App\Data\UseCases\Cars;

use App\Core\Utils\Failures\ServerFailure;
use App\Core\Utils\Strings\RandomString;
use App\Domain\Repositories\CarRepositoryInterface;
use App\Data\Models\CarModel;
use App\Core\Utils\Failures\Failure;
use DateTime;

class StoreCarUseCase
{
    private CarRepositoryInterface $carRepository;

    public function __construct(CarRepositoryInterface $carRepository)
    {
        $this->carRepository = $carRepository;
    }

    public function execute($name, $price, $inStock)
    {
        try {
            $randomStringGenerator = new RandomString(CarModel::ID_CHARACTERS);
            $id = $randomStringGenerator->generate(CarModel::ID_LENGTH);

            $car = new CarModel($id, $name, $price, $inStock, null, null);

            if (!$car->hasErrors()) {
                $this->carRepository->save(
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