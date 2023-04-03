<?php

namespace App\Data\Sources\Clients;

use App\Data\Models\ClientModel;

interface ClientSourceInterface
{
    public function findAll(): array;
    public function findById(string $id): ClientModel;
    public function getCount(): int;
    public function findByName(string $name): array;
    public function save(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void;
    public function update(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void;
}