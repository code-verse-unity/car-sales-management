<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use App\Core\Applications\Application;
use App\Data\Repositories\CarRepository;
use App\Data\Repositories\UserRepository;
use App\Data\Repositories\ClientRepository;
use App\Data\Sources\Users\MysqlUserSource;
use App\Data\Sources\Cars\MySqlCarSource;
use App\Data\Sources\Clients\MySqlClientSource;
use App\Data\UseCases\Clients\IndexClientUseCase;
use App\Data\UseCases\Clients\StoreClientUseCase;
use App\Data\UseCases\Clients\UpdateClientUseCase;
use App\Data\UseCases\Cars\StoreCarUseCase;
use App\Data\UseCases\Cars\ShowCarUseCase;
use App\Data\UseCases\Users\StoreUserUseCase;
use App\Presentation\Controllers\Clients\IndexClientController;
use App\Presentation\Controllers\Clients\StoreClientController;
use App\Presentation\Controllers\Clients\UpdateClientController;
use App\Presentation\Controllers\Users\StoreUserController;
use App\Presentation\Controllers\Cars\StoreCarController;
use App\Presentation\Controllers\Cars\ShowCarController;

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
// UpdateClientUseCase
$updateClientUseCase = new UpdateClientUseCase($clientRepository);
$updateClientController = new UpdateClientController($updateClientUseCase);

// Car
// Source and repository
$mySqlCarSource = new MySqlCarSource($pdo);
$carRepository = new CarRepository($mySqlCarSource);
// StoreCarUseCase and StoreCarController
$storeCarUseCase = new StoreCarUseCase($carRepository);
$storeCarController = new StoreCarController($storeCarUseCase);
// ShowCarUseCase and ShowCarController
$showCarUseCase = new ShowCarUseCase($carRepository);
$showCarController = new ShowCarController($showCarUseCase);

$app = new Application();

$app->router->post("/users", [$storeUserController, "execute"]);

$app->router->get("/clients", [$indexClientController, "execute"]);
$app->router->post("/clients", [$storeClientController, "execute"]);

/*
This is the path to update a client,
The clientId is automatically in $request->params.
So there is no need to add id in the body of the request, it won't be used
*/
$app->router->put("/clients/{clientId}", [$updateClientController, "execute"]);

// Car routes
$app->router->post("/cars", [$storeCarController, "execute"]);
$app->router->get("/cars/{carId}", [$showCarController, "execute"]);

$app->run();