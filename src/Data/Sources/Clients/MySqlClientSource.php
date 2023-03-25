<?php

namespace App\Data\Sources\Clients;

use App\Data\Sources\Clients\ClientSourceInterface;
use App\Data\Models\ClientModel;
use PDO;

class MySqlClientSource implements ClientSourceInterface
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $order = "name"; // ? maybe add more option by query
        $statement = $this->pdo->prepare("SELECT * FROM " . ClientModel::TABLE_NAME . " ORDER BY " . $order . ";"); // ! binding the table name won't work as well as the order
        $statement->execute();
        $clientFetched = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($client) {
            $client =
                new ClientModel($client["id"], $client["name"], $client["contact"], $client["createdAt"], $client["updatedAt"]);
            return $client->getRaw();
        }, $clientFetched);
    }

    public function findById(string $id): ClientModel
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . ClientModel::TABLE_NAME . " WHERE id = :id LIMIT 1;");
        $statement->bindValue("id", $id);
        $statement->execute();
        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        // TODO throw new NotFoundException
        if (empty($fetched)) {
            throw new \Exception();
        }

        $client = $fetched[0];

        return new ClientModel($client["id"], $client["name"], $client["contact"], $client["createdAt"], $client["updatedAt"]);
    }

    public function findByName(string $name): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . ClientModel::TABLE_NAME . " WHERE name LIKE :name;");
        $statement->bindValue("name", "%" . $name . "%");
        $statement->execute();
        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($client) {
            return new ClientModel($client["id"], $client["name"], $client["contact"], $client["createdAt"], $client["updatedAt"]);
        }, $fetched);
    }

    public function save(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare("INSERT INTO " . ClientModel::TABLE_NAME . " (id, name, contact, createdAt, updatedAt) VALUES (:id, :name, :contact, :createdAt, :updatedAt);");

        $statement->bindValue("id", $id);
        $statement->bindValue("name", $name);
        $statement->bindValue("contact", $contact);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }

    public function update(string $id, string $name, string $contact, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare("UPDATE " . ClientModel::TABLE_NAME . " SET name = :name, contact = :contact, createdAt = :createdAt, updatedAt = :updatedAt WHERE id = :id;");

        $statement->bindValue("id", $id);
        $statement->bindValue("name", $name);
        $statement->bindValue("contact", $contact);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }
}
