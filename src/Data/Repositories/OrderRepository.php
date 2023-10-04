<?php

namespace App\Data\Repositories;

use App\Data\Models\OrderModel;
use App\Data\Sources\Orders\OrderSourceInterface;
use App\Domain\Repositories\OrderRepositoryInterface;
use DateTime;

class OrderRepository implements OrderRepositoryInterface
{
    private OrderSourceInterface $source;

    public function __construct(OrderSourceInterface $source)
    {
        $this->source = $source;
    }

    public function findAll(?DateTime $startAt, ?DateTime $endAt): array
    {
        return $this->source->findAll($startAt, $endAt);
    }

    public function findById(string $id): OrderModel
    {
        return $this->source->findById($id);
    }

    public function findByClientId(string $clientId): array
    {
        return $this->source->findByClientId($clientId);
    }

    public function save(string $id, string $clientId, array $orderCarsIds, array $carsIds, array $quantities, string $createdAt, string $updatedAt): void
    {
        $this->source->save($id, $clientId, $orderCarsIds, $carsIds, $quantities, $createdAt, $updatedAt);
    }

    public function update(string $id, string $clientId, array $orderCarsIds, array $carsIds, array $quantities, string $createdAt, string $updatedAt): void
    {
        $this->source->update($id, $clientId, $orderCarsIds, $carsIds, $quantities, $createdAt, $updatedAt);
    }

    public function delete(string $id): void
    {
        $this->source->delete($id);
    }

    public function getCount(): int
    {
        return $this->source->getCount();
    }

    public function getCountByLastMonths(int $lastMonths): int
    {
        return $this->source->getCountByLastMonths($lastMonths);
    }

    public function getRevenue(): int
    {
        return $this->source->getRevenue();
    }

    public function getRevenueByLastMonths(int $lastMonths): int
    {
        return $this->source->getRevenueByLastMonths($lastMonths);
    }

    public function getRevenuePerMonth(): array
    {
        return $this->source->getRevenuePerMonth();
    }

    public function getRevenuePerMonthByLastMonths(int $lastMonths): array
    {
        return $this->source->getRevenuePerMonthByLastMonths($lastMonths);
    }
}