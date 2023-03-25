<?php

namespace App\Data\Repositories;


use App\Data\Models\ClientModel;
use App\Data\Sources\Clients\ClientSourceInterface;
use App\Domain\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private ClientSourceInterface $source;

    public function __construct(ClientSourceInterface $source)
    {
        $this->source = $source;
    }

    public function findAll(): array
    {
        return $this->source->findAll();
    }

    public function findById(string $id): ClientModel
    {
        return $this->source->findById($id);
    }
    public function findByName(string $name): array
    {
        return $this->source->findByName($name);
    }
    public function save($id, string $name, string $contact, $createdAt, $updatedAt): void
    {
        $this->source->save($id, $name, $contact, $createdAt, $updatedAt);
    }

    public function update(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void
    {
        $this->source->update($id, $name, $contact, $createdAt, $updatedAt);
    }
}
