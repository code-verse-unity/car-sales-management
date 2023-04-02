<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Order;
use DateTime;

interface OrderRepositoryInterface
{
    public function findAll(?DateTime $startAt, ?DateTime $endAt): array;
    public function findById(string $id): Order;
    public function getCount(): int;
    public function getCountByLastMonths(int $lastMonths): int;
    public function getRevenue(): int;
    public function getRevenueByLastMonths(int $lastMonths): int;
    public function findByClientId(string $clientId): array;
    // public function findByCarId(string $carId): array; // require more time to implement and maybe not necessary
    public function save(string $id, string $clientId, array $orderCarsIds, array $carsIds, array $quantities, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $clientId, array $orderCarsIds, array $carsIds, array $quantities, string $createdAt, string $updatedAt): void;
    public function delete(string $id): void;
}