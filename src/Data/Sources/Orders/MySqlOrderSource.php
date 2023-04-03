<?php

namespace App\Data\Sources\Orders;

use App\Core\Utils\Failures\NotFoundFailure;
use App\Core\Utils\Failures\ServerFailure;
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
        $orderCarsTableName = "order_cars";

        $intervalQuery = "";

        if ($startAt && $endAt) {
            $intervalQuery =
                "WHERE $orderTableName.createdAt
                    BETWEEN :startAt AND :endAt ";
        } else if ($startAt) {
            $intervalQuery = "WHERE $orderTableName.createdAt >= :startAt ";
        } else if ($endAt) {
            $intervalQuery = "WHERE $orderTableName.createdAt <= :endAt";
        }

        $statement = $this->pdo->prepare(
            "SELECT
                $orderTableName.id AS orderId,
                $orderTableName.clientId,
                $orderTableName.createdAt AS orderCreatedAt,
                $orderTableName.updatedAt As orderUpdatedAt,
                $clientTableName.id AS clientId,
                $clientTableName.name AS clientName,
                $clientTableName.contact AS clientContact,
                $clientTableName.createdAt AS clientCreatedAt,
                $clientTableName.updatedAt AS clientUpdatedAt,
                $carTableName.id AS carId,
                $carTableName.price AS carPrice,
                $carTableName.inStock AS carInStock,
                $carTableName.name AS carName,
                $carTableName.createdAt AS carCreatedAt,
                $carTableName.updatedAt AS carUpdatedAt,
                $orderCarsTableName.quantity
            FROM $orderTableName
            INNER JOIN $clientTableName
                ON $orderTableName.clientId = $clientTableName.id
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            $intervalQuery
            ORDER BY
                $orderTableName.createdAt DESC,
                $clientTableName.name ASC,
                $carTableName.name ASC
            ;"
        );

        if ($startAt) {
            $statement->bindValue("startAt", $startAt->format(DateTime::ATOM));
        }

        if ($endAt) {
            $statement->bindValue("endAt", $endAt->format(DateTime::ATOM));
        }

        $statement->execute();
        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->createOrdersFromArrayFetched($arrayFetched);
    }

    public function findById(string $id): OrderModel
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $intervalQuery = "";

        $statement = $this->pdo->prepare(
            "SELECT
                $orderTableName.id AS orderId,
                $orderTableName.clientId,
                $orderTableName.createdAt AS orderCreatedAt,
                $orderTableName.updatedAt As orderUpdatedAt,
                $clientTableName.id AS clientId,
                $clientTableName.name AS clientName,
                $clientTableName.contact AS clientContact,
                $clientTableName.createdAt AS clientCreatedAt,
                $clientTableName.updatedAt AS clientUpdatedAt,
                $carTableName.id AS carId,
                $carTableName.price AS carPrice,
                $carTableName.inStock AS carInStock,
                $carTableName.name AS carName,
                $carTableName.createdAt AS carCreatedAt,
                $carTableName.updatedAt AS carUpdatedAt,
                $orderCarsTableName.quantity
            FROM $orderTableName
            INNER JOIN $clientTableName
                ON $orderTableName.clientId = $clientTableName.id
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            WHERE $orderTableName.id = :id
            ORDER BY
                $orderTableName.createdAt DESC,
                $clientTableName.name ASC,
                $carTableName.name ASC
            ;"
        );
        $statement->bindValue("id", $id);
        $statement->execute();

        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        if (empty($arrayFetched)) {
            throw new NotFoundFailure();
        }

        return $this->createOrdersFromArrayFetched($arrayFetched)[0];
    }

    public function findByClientId(string $clientId): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $clientTableName = ClientModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $statement = $this->pdo->prepare(
            "SELECT
                $orderTableName.id AS orderId,
                $orderTableName.clientId,
                $orderTableName.createdAt AS orderCreatedAt,
                $orderTableName.updatedAt As orderUpdatedAt,
                $clientTableName.id AS clientId,
                $clientTableName.name AS clientName,
                $clientTableName.contact AS clientContact,
                $clientTableName.createdAt AS clientCreatedAt,
                $clientTableName.updatedAt AS clientUpdatedAt,
                $carTableName.id AS carId,
                $carTableName.price AS carPrice,
                $carTableName.inStock AS carInStock,
                $carTableName.name AS carName,
                $carTableName.createdAt AS carCreatedAt,
                $carTableName.updatedAt AS carUpdatedAt,
                $orderCarsTableName.quantity
            FROM $orderTableName
            INNER JOIN $clientTableName
                ON $orderTableName.clientId = $clientTableName.id
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            WHERE $orderTableName.clientId = :clientId
            ORDER BY
                $orderTableName.createdAt DESC,
                $clientTableName.name ASC,
                $carTableName.name ASC
            ;"
        );

        $statement->bindValue("clientId", $clientId);

        $statement->execute();

        /*
        ! if it's empty,
        ! that means the clients didn't make any order
        ! OR
        ! the client doesn't exist at all
        */
        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $this->createOrdersFromArrayFetched($arrayFetched);
    }

    public function save(
        string $id,
        string $clientId,
        array $orderCarsIds,
        array $carsIds,
        array $quantities,
        string $createdAt,
        string $updatedAt
    ): void {
        $orderTableName = OrderModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        // save the order
        $statement = $this->pdo->prepare(
            "INSERT INTO $orderTableName 
                (id, clientId, createdAt, updatedAt)
            VALUES
                (:id, :clientId, :createdAt, :updatedAt);"
        );
        $statement->bindValue("id", $id);
        $statement->bindValue("clientId", $clientId);
        $statement->bindValue("createdAt", $createdAt);
        $statement->bindValue("updatedAt", $updatedAt);
        $statement->execute();

        $length = count($orderCarsIds);
        for ($i = 0; $i < $length; $i++) {
            $statement = $this->pdo->prepare(
                "INSERT INTO $orderCarsTableName
                    (id, orderId, carId, quantity, createdAt, updatedAt)
                VALUES
                    (:id, :orderId, :carId, :quantity, :createdAt, :updatedAt);"
            );

            $statement->bindValue("id", $orderCarsIds[$i]);
            $statement->bindValue("orderId", $id);
            $statement->bindValue("carId", $carsIds[$i]);
            $statement->bindValue("quantity", $quantities[$i]);
            $statement->bindValue("createdAt", $createdAt);
            $statement->bindValue("updatedAt", $updatedAt);
            $statement->execute();
        }
    }

    public function update(
        string $id,
        string $clientId,
        array $orderCarsIds,
        array $carsIds,
        array $quantities,
        string $createdAt,
        string $updatedAt
    ): void {
        throw new ServerFailure("Not implemented yet.");
    }

    /*
    for this case, deleting an order === cancelling the order,
    so all cars in the order must be returned to the stock
    ! if deleting an order === deleting only the order, the logic won't work
    */
    public function delete(string $id): void
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $now = new DateTime();

        $statement = $this->pdo->prepare(
            "SELECT * FROM $orderCarsTableName WHERE orderId = :id;"
        );
        $statement->bindValue("id", $id);
        $statement->execute();
        $orderCars = $statement->fetchAll(PDO::FETCH_ASSOC);

        // return all cars to the stock
        foreach ($orderCars as $orderCar) {
            $statement = $this->pdo->prepare(
                "UPDATE $carTableName
                SET inStock = inStock + :quantity,
                    updatedAt = :updatedAt
                WHERE id = :carId;"
            );

            $statement->bindValue("quantity", $orderCar["quantity"]);
            $statement->bindValue("updatedAt", $now->format(DateTime::ATOM));
            $statement->bindValue("carId", $orderCar["carId"]);
            $statement->execute();
        }

        // delete the orderCars
        $statement = $this->pdo->prepare(
            "DELETE FROM $orderCarsTableName WHERE orderId = :orderId;"
        );
        $statement->bindValue("orderId", $id);
        $statement->execute();

        // delete the order
        $statement = $this->pdo->prepare(
            "DELETE FROM $orderTableName WHERE id = :id;"
        );
        $statement->bindValue("id", $id);
        $statement->execute();
    }

    private function createOrdersFromArrayFetched($arrayFetched)
    {
        $orderIdArr = [];

        foreach ($arrayFetched as $fetched) {
            if (array_key_exists($fetched["orderId"], $orderIdArr)) {
                $orderIdArr[$fetched["orderId"]]["cars"][$fetched["carId"]] =
                    [
                        "car" => [
                            "id" => $fetched["carId"],
                            "price" => $fetched["carPrice"],
                            "inStock" => $fetched["carInStock"],
                            "name" => $fetched["carName"],
                            "createdAt" => $fetched["carCreatedAt"],
                            "updatedAt" => $fetched["carUpdatedAt"],
                        ],
                        "quantity" => $fetched["quantity"]
                    ];
            } else {
                $orderIdArr[$fetched["orderId"]] = [
                    "id" => $fetched["orderId"],
                    "clientId" => $fetched["clientId"],
                    "createdAt" => $fetched["orderCreatedAt"],
                    "updatedAt" => $fetched["orderUpdatedAt"],
                    "client" => [
                        "id" => $fetched["clientId"],
                        "name" => $fetched["clientName"],
                        "contact" => $fetched["clientContact"],
                        "createdAt" => $fetched["clientCreatedAt"],
                        "updatedAt" => $fetched["clientUpdatedAt"],
                    ],
                    "cars" => [
                        $fetched["carId"] => [
                            "car" => [
                                "id" => $fetched["carId"],
                                "price" => $fetched["carPrice"],
                                "inStock" => $fetched["carInStock"],
                                "name" => $fetched["carName"],
                                "createdAt" => $fetched["carCreatedAt"],
                                "updatedAt" => $fetched["carUpdatedAt"],
                            ],
                            "quantity" => $fetched["quantity"]
                        ],
                    ]
                ];
            }
        }

        return array_values(
            array_map(
                function ($orderArr) {
                    return (new OrderModel(
                        $orderArr["id"],
                        new ClientModel(
                            $orderArr["client"]["id"],
                            $orderArr["client"]["name"],
                            $orderArr["client"]["contact"],
                            $orderArr["client"]["createdAt"],
                            $orderArr["client"]["updatedAt"]
                        ),
                        array_map(
                            function ($carIdArr) {
                                $car = $carIdArr["car"];
                                $quantity = $carIdArr["quantity"];

                                return [
                                    "car" => new CarModel(
                                        $car["id"],
                                        $car["name"],
                                        $car["price"],
                                        $car["inStock"],
                                        $car["createdAt"],
                                        $car["updatedAt"],
                                    ),
                                    "quantity" => $quantity
                                ];
                            },
                            array_values($orderArr["cars"])
                        ),
                        $orderArr["createdAt"],
                        $orderArr["updatedAt"]
                    )
                    );
                },
                $orderIdArr
            )
        );
    }

    public function getCount(): int
    {
        $orderTableName = OrderModel::TABLE_NAME;

        $statement = $this->pdo->prepare(
            "SELECT COUNT(*) AS ordersCount FROM $orderTableName;"
        );

        $statement->execute();

        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $fetched[0]["ordersCount"];
    }

    public function getCountByLastMonths(int $lastMonths): int
    {
        $orderTableName = OrderModel::TABLE_NAME;

        $statement = $this->pdo->prepare(
            "SELECT
                COUNT(*) AS ordersCount
            FROM $orderTableName
            WHERE
                createdAt >= DATE_SUB(
                    CURDATE(),
                    INTERVAL :lastMonths MONTH
                );"
        );

        $statement->bindValue("lastMonths", $lastMonths);

        $statement->execute();

        $fetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return $fetched[0]["ordersCount"];
    }

    public function getRevenue(): int
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $statement = $this->pdo->prepare(
            "SELECT
                $carTableName.price,
                $orderCarsTableName.quantity
            FROM $orderTableName
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            ;"
        );

        $statement->execute();

        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_reduce(
            $arrayFetched,
            function ($prev, $priceQuantity) {
                return $prev + $priceQuantity["price"] * $priceQuantity["quantity"];
            },
            0
        );
    }

    public function getRevenueByLastMonths(int $lastMonths): int
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $statement = $this->pdo->prepare(
            "SELECT
                $carTableName.price,
                $orderCarsTableName.quantity
            FROM $orderTableName
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            WHERE
                $orderTableName.createdAt >= DATE_SUB(
                    CURDATE(),
                    INTERVAL :lastMonths MONTH
                );"
        );

        $statement->bindValue("lastMonths", $lastMonths);

        $statement->execute();

        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        return array_reduce(
            $arrayFetched,
            function ($prev, $priceQuantity) {
                return $prev + $priceQuantity["price"] * $priceQuantity["quantity"];
            },
            0
        );
    }

    public function getRevenuePerMonth(): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $statement = $this->pdo->prepare(
            "SELECT
                MAX(createdAt) AS maxCreatedAt,
                MIN(createdAt) AS minCreatedAt
            FROM $orderTableName;"
        );
        $statement->execute();
        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

        $dateAmountAssocDefault = [];

        // if there is one or more order
        if (!empty($arrayFetched)) {

            $minMaxCreatedAt = $arrayFetched[0];
            $minCreatedAt = new DateTime($minMaxCreatedAt["minCreatedAt"]);
            $minCreatedAt->modify("first day of this month");
            $minCreatedAt->setTime(0, 0, 0, 0);
            $maxCreatedAt = new DateTime($minMaxCreatedAt["maxCreatedAt"]);
            $maxCreatedAt->modify("first day of this month");
            $maxCreatedAt->setTime(0, 0, 0, 0);

            $interval = $minCreatedAt->diff($maxCreatedAt);
            $monthsDiff = $interval->m + ($interval->y * 12) + 1;

            $dateAmountAssocDefault[$maxCreatedAt->format(DateTime::ATOM)] = [
                "date" => $maxCreatedAt->format(DateTime::ATOM),
                "amount" => 0
            ];
            for ($i = 0; $i < $monthsDiff - 1; $i++) {
                $maxCreatedAt->modify("-1 month");
                $dateAmountAssocDefault[$maxCreatedAt->format(DateTime::ATOM)] = [
                    "date" => $maxCreatedAt->format(DateTime::ATOM),
                    "amount" => 0
                ];
            }

            $statement = $this->pdo->prepare(
                "SELECT
                    $carTableName.price,
                    $orderCarsTableName.quantity,
                    $orderTableName.createdAt
                FROM $orderTableName
                INNER JOIN $orderCarsTableName
                    ON $orderTableName.id = $orderCarsTableName.orderId
                INNER JOIN $carTableName
                    ON $orderCarsTableName.carId = $carTableName.id
                ;"
            );

            $statement->execute();

            $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);

            $dateAmountAssocFetched = $this->createDateAmountFromArrayFetched($arrayFetched);

            // we merge them
            foreach ($dateAmountAssocFetched as $key => $value) {
                if (array_key_exists($key, $dateAmountAssocDefault)) {
                    $dateAmountAssocDefault[$key]["amount"] = $value["amount"];
                }
            }
        }

        $dateAmountValue = array_values($dateAmountAssocDefault);

        return $dateAmountValue;
    }

    public function getRevenuePerMonthByLastMonths(int $lastMonths): array
    {
        $orderTableName = OrderModel::TABLE_NAME;
        $carTableName = CarModel::TABLE_NAME;
        $orderCarsTableName = "order_cars";

        $whereQuery = "";

        if ($lastMonths > 1) {
            $whereQuery = "WHERE
            $orderTableName.createdAt BETWEEN DATE_ADD(
                DATE_SUB(
                    CURDATE(),
                    INTERVAL :lastMonths MONTH
                ),
                INTERVAL - DAY(
                    DATE_SUB(
                        CURDATE(),
                        INTERVAL :lastMonths MONTH
                    )
                ) + 1 DAY
            )
            AND LAST_DAY(NOW())";
        } else {
            $whereQuery = "WHERE
                MONTH($orderTableName.createdAt) = MONTH(CURDATE())
            AND 
                YEAR($orderTableName.createdAt) = YEAR(CURDATE())";
        }

        $statement = $this->pdo->prepare(
            "SELECT
                $carTableName.price,
                $orderCarsTableName.quantity,
                $orderTableName.createdAt
            FROM $orderTableName
            INNER JOIN $orderCarsTableName
                ON $orderTableName.id = $orderCarsTableName.orderId
            INNER JOIN $carTableName
                ON $orderCarsTableName.carId = $carTableName.id
            $whereQuery
            ;"
        );

        if ($lastMonths > 1) {
            $statement->bindValue("lastMonths", $lastMonths);
        }

        $statement->execute();

        // we fetch from the db
        $arrayFetched = $statement->fetchAll(PDO::FETCH_ASSOC);
        $dateAmountAssocFetched = $this->createDateAmountFromArrayFetched($arrayFetched);

        // we create the default result
        $dateAmountAssocDefault = [];
        $now = new DateTime();
        $now->modify("first day of this month");
        $now->setTime(0, 0, 0, 0);
        $dateAmountAssocDefault[$now->format(DateTime::ATOM)] = [
            "date" => $now->format(DateTime::ATOM),
            "amount" => 0
        ];
        for ($i = 0; $i < $lastMonths - 1; $i++) {
            $now->modify("-1 month");
            $dateAmountAssocDefault[$now->format(DateTime::ATOM)] = [
                "date" => $now->format(DateTime::ATOM),
                "amount" => 0
            ];
        }

        // we merge them
        foreach ($dateAmountAssocFetched as $key => $value) {
            if (array_key_exists($key, $dateAmountAssocDefault)) {
                $dateAmountAssocDefault[$key]["amount"] = $value["amount"];
            }
        }

        $dateAmountValue = array_values($dateAmountAssocDefault);

        return $dateAmountValue;
    }

    /* It creates an associative array with date and amount as keys*/
    private function createDateAmountFromArrayFetched($arrayFetched)
    {

        $dateAmountAssocFetched = [];

        foreach ($arrayFetched as $fetched) {
            $firstDate = new DateTime($fetched["createdAt"]);
            $firstDate->modify("first day of this month");
            $firstDate->setTime(0, 0, 0, 0);
            $dateString = $firstDate->format(DateTime::ATOM);

            if (array_key_exists($dateString, $dateAmountAssocFetched)) {
                $dateAmountAssocFetched[$dateString]["amount"] += $fetched["price"] * $fetched["quantity"];
            } else {
                $dateAmountAssocFetched[$dateString] = [
                    "amount" => $fetched["price"] * $fetched["quantity"],
                    "date" => $firstDate
                ];
            }
        }

        return $dateAmountAssocFetched;
    }
}
