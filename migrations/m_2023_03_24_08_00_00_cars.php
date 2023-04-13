<?php

class m_2023_03_24_08_00_00_cars
{
  const TABLE_NAME = "cars";

  public function up(PDO $pdo)
  {
    $statement = $pdo->prepare(
      "CREATE TABLE " . self::TABLE_NAME .
        " (
                id VARCHAR(255) PRIMARY KEY UNIQUE NOT NULL,
                name VARCHAR(255) NOT NULL,
                price INT NOT NULL,
                inStock INT NOT NULL,
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
