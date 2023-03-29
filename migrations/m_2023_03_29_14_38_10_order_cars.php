<?php

class m_2023_03_29_14_38_10_order_cars
{
    const TABLE_NAME = "order_cars";

    public function up(PDO $pdo)
    {
        // TODO: Review relationship
        $statement = $pdo->prepare(
            "CREATE TABLE " . self::TABLE_NAME .
            " (
                id VARCHAR(255) PRIMARY KEY UNIQUE NOT NULL,
                orderId VARCHAR(255) NOT NULL,
                carId VARCHAR(255) NOT NULL,
                quantity INT NOT NULL,
                createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (orderId) REFERENCES orders(id),
                FOREIGN KEY (carId) REFERENCES cars(id)
            );"
        );
        $statement->execute();
    }

    public function down(PDO $pdo)
    {
        $statement = $pdo->prepare("DROP TABLE " . self::TABLE_NAME . " ;");
        $statement->execute();
    }
}