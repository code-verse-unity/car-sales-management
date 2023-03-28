<?php

namespace App\Data\Sources\Cars;

use App\Core\Utils\Failures\NotFoundFailure;
use App\Data\Sources\Cars\CarSourceInterface;
use App\Data\Models\CarModel;
use PDO;

class MySqlCarSource implements CarSourceInterface
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll(): array
    {
        $order = "name";
        $statement = $this->pdo->prepare("SELECT * FROM " . CarModel::TABLE_NAME . " ORDER BY " . $order . ";");
        $statement->execute();
        $clientFetched = $statement->fetchAll(PDO::FETCH_ASSOC);
        return array_map(function ($car) {
            return new CarModel($car["id"], $car["name"], $car["price"], $car["inStock"], $car["createdAt"], $car["updatedAt"]);
        }, $clientFetched);
    }

    public function findById(string $id): CarModel
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . CarModel::TABLE_NAME . " WHERE id = :id LIMIT 1;");
        $statement->bindValue("id", $id);
        $statement->execute();
        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($fetched)) {
            throw new NotFoundFailure();
        }

        $car = $fetched[0];

        return new CarModel($car["id"], $car["name"], $car["price"], $car["inStock"], $car["createdAt"], $car["updatedAt"]);
    }

    public function findByName(string $name): array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . CarModel::TABLE_NAME . " WHERE name LIKE :name;");
        $statement->bindValue("name", "%" . $name . "%");
        $statement->execute();
        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($car) {
            return new CarModel($car["id"], $car["name"], $car["price"], $car["inStock"], $car["createdAt"], $car["updatedAt"]);
        }, $fetched);
    }

    public function save(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare("INSERT INTO " . CarModel::TABLE_NAME . " (id, name, price, inStock, createdAt, updatedAt) VALUES (:id, :name, :price, :inStock, :createdAt, :updatedAt);");

        $statement->bindValue("id", $id);
        $statement->bindValue("name", $name);
        $statement->bindValue("price", $price);
        $statement->bindValue("inStock", $inStock);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }

    public function update(string $id, string $name, int $price, int $inStock, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare("UPDATE " . CarModel::TABLE_NAME . " SET name = :name, price = :price, inStock = :inStock, createdAt = :createdAt, updatedAt = :updatedAt WHERE id = :id;");

        $statement->bindValue("id", $id);
        $statement->bindValue("name", $name);
        $statement->bindValue("price", $price);
        $statement->bindValue("inStock", $inStock);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }

    public function findByMinInStock(int $minInStock): array
    {
        $order = "name";
        $statement = $this->pdo->prepare(
            "SELECT
                *
            FROM " . CarModel::TABLE_NAME .
            " WHERE inStock >= :minInStock
            ORDER BY " .
            $order .
            ";"
        );

        $statement->bindValue("minInStock", $minInStock);
        $statement->execute();

        $clientFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(
            function ($car) {
                return new CarModel(
                    $car["id"],
                    $car["name"],
                    $car["price"],
                    $car["inStock"],
                    $car["createdAt"],
                    $car["updatedAt"]
                );
            },
            $clientFetched
        );
    }
}