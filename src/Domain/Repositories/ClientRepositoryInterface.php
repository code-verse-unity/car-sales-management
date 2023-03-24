<?php

namespace App\Domain\Repositories;

use App\Domain\Entities\Client;

interface ClientRepositoryInterface
{
    public function findAll(): array;
    public function findById(string $id): Client;
    public function findByName(string $name): array; // can find more than one client
    public function save(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void;
}