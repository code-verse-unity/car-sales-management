<?php

namespace App\Data\Sources\Orders;

use App\Data\Models\OrderModel;
use DateTime;

interface OrderSourceInterface
{
    public function findAll(?DateTime $startAt, ?DateTime $endAt): array;
    public function findById(string $id): OrderModel;
    public function findByClientId(string $clientId): array;
    public function save(string $id, string $clientId, array $carsQuantities, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $clientId, array $carsQuantities, string $createdAt, string $updatedAt): void;
}