<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Order;
use DateTime;

interface OrderRepositoryInterface
{
    public function findAll(?DateTime $startAt, ?DateTime $endAt): array;
    public function findById(string $id): Order;
    public function findByClientId(string $clientId): array;
    public function findByCarId(string $carId): array;
    public function save(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void;
}