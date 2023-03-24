<?php

class m_2023_03_24_08_19_02_clients
{
  const TABLE_NAME = "clients";

  public function up(PDO $pdo)
  {
    $statement = $pdo->prepare(
      "CREATE TABLE " . self::TABLE_NAME .
        " (
                id VARCHAR(255) PRIMARY KEY UNIQUE NOT NULL,
                name VARCHAR(255) NOT NULL,
                contact VARCHAR(255) NOT NULL,
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
