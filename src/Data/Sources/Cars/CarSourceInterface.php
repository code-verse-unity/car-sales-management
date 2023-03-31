<?php

namespace App\Data\Sources\Cars;

use App\Data\Models\CarModel;

interface CarSourceInterface
{
    public function findAll(): array;
    public function findById(string $id): CarModel;
    public function findByName(string $name): array;
    public function findByMinInStock(int $minInStock): array; // find all cars where inStock >= minInStock
    public function save(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void;
}