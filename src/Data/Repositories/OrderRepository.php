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

    public function save(string $id, string $clientId, array $carsQuantities, string $createdAt, string $updatedAt): void
    {
        $this->source->save($id, $clientId, $carsQuantities, $createdAt, $updatedAt);
    }

    public function update(string $id, string $clientId, array $carsQuantities, string $createdAt, string $updatedAt): void
    {
        $this->source->update($id, $clientId, $carsQuantities, $createdAt, $updatedAt);
    }
}