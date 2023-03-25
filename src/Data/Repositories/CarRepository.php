<?php

namespace App\Data\Repositories;


use App\Data\Models\CarModel;
use App\Data\Sources\Cars\CarSourceInterface;
use App\Domain\Repositories\CarRepositoryInterface;

class CarRepository implements CarRepositoryInterface
{
    private CarSourceInterface $source;

    public function __construct(CarSourceInterface $source)
    {
        $this->source = $source;
    }

    public function findAll(): array
    {
        return $this->source->findAll();
    }

    public function findById(string $id): CarModel
    {
        return $this->source->findById($id);
    }
    public function findByName(string $name): array
    {
        return $this->source->findByName($name);
    }
    public function save(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void
    {
        $this->source->save($id, $name, $price, $inStock, $createdAt, $updatedAt);
    }

    public function update(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void
    {
        $this->source->update($id, $name, $price, $inStock, $createdAt, $updatedAt);
    }
}