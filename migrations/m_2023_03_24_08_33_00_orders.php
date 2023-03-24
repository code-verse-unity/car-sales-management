<?php

class m_2023_03_24_08_33_00_orders
{
  const TABLE_NAME = "orders";

  public function up(PDO $pdo)
  {
    // TODO: Review relationship
    $statement = $pdo->prepare(
      "CREATE TABLE " . self::TABLE_NAME .
        " (
                id VARCHAR(255) PRIMARY KEY UNIQUE NOT NULL,
                clientId VARCHAR(255) NOT NULL,
                carId VARCHAR(255) NOT NULL,
                quantity INT NOT NULL,
                createdAt DATETIME DEFAULT CURRENT_TIMESTAMP,
                updatedAt DATETIME DEFAULT CURRENT_TIMESTAMP
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
