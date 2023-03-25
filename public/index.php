<?php

require_once __DIR__ . "/../vendor/autoload.php";

use App\Core\Applications\Application;
use App\Data\Repositories\ClientRepository;
use Dotenv\Dotenv;
use App\Data\Sources\Users\MysqlUserSource;
use App\Data\Repositories\UserRepository;
use App\Data\Sources\Clients\MySqlClientSource;
use App\Data\UseCases\Clients\IndexClientUseCase;
use App\Data\UseCases\Clients\StoreClientUseCase;
use App\Data\UseCases\Users\StoreUserUseCase;
use App\Presentation\Controllers\Clients\IndexClientController;
use App\Presentation\Controllers\Clients\StoreClientController;
use App\Presentation\Controllers\Users\StoreUserController;

$dotenv = Dotenv::createImmutable(__DIR__ . "/../");
$dotenv->load();

$dsn = $_ENV["DB_DSN"];
$user = $_ENV["DB_USER"];
$password = $_ENV["DB_PASSWORD"];

$pdo = new PDO($dsn, $user, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mysqlUserSource = new MysqlUserSource($pdo);
$userRepository = new UserRepository($mysqlUserSource);
$storeUserUseCase = new StoreUserUseCase($userRepository);
$storeUserController = new StoreUserController($storeUserUseCase);

// ClientUseCase
$mysqlClientSource = new MySqlClientSource($pdo);
$clientRepository = new ClientRepository($mysqlClientSource);
// IndexClientUseCase
$indexClientUseCase = new IndexClientUseCase($clientRepository);
$indexClientController = new IndexClientController($indexClientUseCase);
// StoreClientUseCase
$storeClientUseCase = new StoreClientUseCase($clientRepository);
$storeClientController = new StoreClientController($storeClientUseCase);

$app = new Application();

$app->router->post("/users", [$storeUserController, "execute"]);

$app->router->get("/clients", [$indexClientController, "execute"]);
$app->router->post("/clients", [$storeClientController, "execute"]);


$app->run();
