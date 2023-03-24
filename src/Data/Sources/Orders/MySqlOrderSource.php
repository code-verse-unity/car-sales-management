<?php

namespace App\Data\Sources\Orders;

use App\Data\Sources\Orders\OrderSourceInterface;
use App\Data\Models\OrderModel;
use App\Data\Models\ClientModel;
use App\Data\Models\CarModel;
use DateTime;
use PDO;

class MySqlOrderSource implements OrderSourceInterface
{

    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    // * startAt and endAt defines an interval, they are optional
    public function findAll(?DateTime $startAt = null, ?DateTime $endAt = null): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;

        $haveInterval = false;
        $intervalQuery = "";

        if ($startAt && $endAt) {
            $haveInterval = true;
            $intervalQuery = "
                WHERE $orderTableName.createdAt
                BETWEEN :startAt AND :endAt ";
        }

        // * we rename some columns since order, client, and car can have the same column name
        $statement = $this->pdo->prepare("
            SELECT
                $orderTableName.*,
                $clientTableName.*,
                $clientTableName.name AS clientName,
                $clientTableName.createdAt AS clientCreatedAt,
                $clientTableName.updatedAt AS clientUpdatedAt,
                $carTableName.*,
                $carTableName.name AS carName,
                $carTableName.createdAt AS carCreatedAt,
                $carTableName.updatedAt AS carUpdatedAt
            FROM $orderTableName
            INNER JOIN $clientTableName
                ON $orderTableName.clientId = $clientTableName.id
            INNER JOIN $carTableName
                ON $orderTableName.carId = $carTableName.id
            $intervalQuery
            ORDER BY
                $orderTableName.createdAt DESC,
                $clientTableName.name ASC,
                $carTableName.name ASC;");

        if ($haveInterval) {
            $statement->bindValue("startAt", $startAt->format(DateTime::ATOM));
            $statement->bindValue("endAt", $endAt->format(DateTime::ATOM));
        }

        $statement->execute();
        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($fetched) {
            return (
                new OrderModel(
                    $fetched["id"],
                    new ClientModel(
                        $fetched["clientId"],
                        $fetched["clientName"],
                        $fetched["contact"],
                        $fetched["clientCreatedAt"],
                        $fetched["clientUpdatedAt"]
                    ),
                    new CarModel(
                        $fetched["carId"],
                        $fetched["carName"],
                        $fetched["price"],
                        $fetched["inStock"],
                        $fetched["carCreatedAt"],
                        $fetched["carUpdatedAt"]
                    ),
                    $fetched["quantity"],
                    $fetched["createdAt"],
                    $fetched["updatedAt"]
                )
            )->getRaw();
        }, $arrayFetched);
    }

    public function findById(string $id): OrderModel
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;

        $statement = $this->pdo->prepare("
        SELECT
            $orderTableName.*,
            $clientTableName.*,
            $clientTableName.name AS clientName,
            $clientTableName.createdAt AS clientCreatedAt,
            $clientTableName.updatedAt AS clientUpdatedAt,
            $carTableName.*,
            $carTableName.name AS carName,
            $carTableName.createdAt AS carCreatedAt,
            $carTableName.updatedAt AS carUpdatedAt
        FROM $orderTableName
        INNER JOIN $clientTableName
            ON $orderTableName.clientId = $clientTableName.id
        INNER JOIN $carTableName
            ON $orderTableName.carId = $carTableName.id
        WHERE $orderTableName.id = :id
        LIMIT 1;");

        $statement->bindValue("id", $id);
        $statement->execute();

        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        // TODO throw new NotFoundException
        if (empty($fetched)) {
            throw new \Exception();
        }

        $order = $fetched[0];

        return new OrderModel(
            $order["id"],
            new ClientModel(
                $order["clientId"],
                $order["clientName"],
                $order["contact"],
                $order["clientCreatedAt"],
                $order["clientUpdatedAt"]
            ),
            new CarModel(
                $order["carId"],
                $order["carName"],
                $order["price"],
                $order["inStock"],
                $order["carCreatedAt"],
                $order["carUpdatedAt"]
            ),
            $order["quantity"],
            $order["createdAt"],
            $order["updatedAt"]
        );
    }

    public function findByClientId(string $clientId): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;

        $statement = $this->pdo->prepare("
        SELECT
            $orderTableName.*,
            $clientTableName.*,
            $clientTableName.name AS clientName,
            $clientTableName.createdAt AS clientCreatedAt,
            $clientTableName.updatedAt AS clientUpdatedAt,
            $carTableName.*,
            $carTableName.name AS carName,
            $carTableName.createdAt AS carCreatedAt,
            $carTableName.updatedAt AS carUpdatedAt
        FROM $orderTableName
        INNER JOIN $clientTableName
            ON $orderTableName.clientId = $clientTableName.id
        INNER JOIN $carTableName
            ON $orderTableName.carId = $carTableName.id
        WHERE $clientTableName.id = :clientId
        ORDER BY
            $orderTableName.createdAt DESC,
            $clientTableName.name ASC,
            $carTableName.name ASC;");

        $statement->bindValue("clientId", $clientId);
        $statement->execute();

        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($fetched) {
            return (
                new OrderModel(
                    $fetched["id"],
                    new ClientModel(
                        $fetched["clientId"],
                        $fetched["clientName"],
                        $fetched["contact"],
                        $fetched["clientCreatedAt"],
                        $fetched["clientUpdatedAt"]
                    ),
                    new CarModel(
                        $fetched["carId"],
                        $fetched["carName"],
                        $fetched["price"],
                        $fetched["inStock"],
                        $fetched["carCreatedAt"],
                        $fetched["carUpdatedAt"]
                    ),
                    $fetched["quantity"],
                    $fetched["createdAt"],
                    $fetched["updatedAt"]
                )
            )->getRaw();
        }, $arrayFetched);
    }

    public function findByCarId(string $carId): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;

        $statement = $this->pdo->prepare(
        "SELECT
            $orderTableName.*,
            $clientTableName.*,
            $clientTableName.name AS clientName,
            $clientTableName.createdAt AS clientCreatedAt,
            $clientTableName.updatedAt AS clientUpdatedAt,
            $carTableName.*,
            $carTableName.name AS carName,
            $carTableName.createdAt AS carCreatedAt,
            $carTableName.updatedAt AS carUpdatedAt
        FROM $orderTableName
        INNER JOIN $clientTableName
            ON $orderTableName.clientId = $clientTableName.id
        INNER JOIN $carTableName
            ON $orderTableName.carId = $carTableName.id
        WHERE $carTableName.id = :carId
        ORDER BY
            $orderTableName.createdAt DESC,
            $clientTableName.name ASC,
            $carTableName.name ASC;"
        );

        $statement->bindValue("carId", $carId);
        $statement->execute();

        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($fetched) {
            return (
                new OrderModel(
                    $fetched["id"],
                    new ClientModel(
                        $fetched["clientId"],
                        $fetched["clientName"],
                        $fetched["contact"],
                        $fetched["clientCreatedAt"],
                        $fetched["clientUpdatedAt"]
                    ),
                    new CarModel(
                        $fetched["carId"],
                        $fetched["carName"],
                        $fetched["price"],
                        $fetched["inStock"],
                        $fetched["carCreatedAt"],
                        $fetched["carUpdatedAt"]
                    ),
                    $fetched["quantity"],
                    $fetched["createdAt"],
                    $fetched["updatedAt"]
                )
            )->getRaw();
        }, $arrayFetched);
    }

    public function save(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare(
            "INSERT INTO " . OrderModel::TABLE_NAME .
            " (id, clientId, carId, quantity, createdAt, updatedAt)
            VALUES (:id, :clientId, :carId, :quantity, :createdAt, :updatedAt);"
        );

        $statement->bindValue("id", $id);
        $statement->bindValue("clientId", $clientId);
        $statement->bindValue("carId", $carId);
        $statement->bindValue("quantity", $quantity);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }

    public function update(string $id, string $clientId, string $carId, int $quantity, string $createdAt, string $updatedAt): void
    {
        $statement = $this->pdo->prepare(
            "UPDATE " . OrderModel::TABLE_NAME .
            " SET clientId = :clientId,
                carId = :carId,
                quantity = :quantity,
                createdAt = :createdAt,
                updatedAt = :updatedAt
            WHERE id = :id;"
        );

        $statement->bindValue("id", $id);
        $statement->bindValue("clientId", $clientId);
        $statement->bindValue("carId", $carId);
        $statement->bindValue("quantity", $quantity);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);

        $statement->execute();
    }
}