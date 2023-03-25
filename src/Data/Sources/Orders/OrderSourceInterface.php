<?php

namespace App\Data\Sources\Orders;

use App\Data\Models\OrderModel;
use DateTime;

interface OrderSourceInterface
{
    public function findAll(?DateTime $startAt, ?DateTime $endAt): array;
    public function findById(string $id): OrderModel;
    public function findByClientId(string $clientId): array;
    public function findByCarId(string $carId): array;
    public function save(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void;
}