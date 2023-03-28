<?php

require_once __DIR__ . "/../vendor/autoload.php";

use Dotenv\Dotenv;
use App\Core\Applications\Application;
use App\Data\Repositories\CarRepository;
use App\Data\Repositories\UserRepository;
use App\Data\Repositories\ClientRepository;
use App\Data\Repositories\OrderRepository;
use App\Data\Sources\Users\MysqlUserSource;
use App\Data\Sources\Cars\MySqlCarSource;
use App\Data\Sources\Clients\MySqlClientSource;
use App\Data\Sources\Orders\MySqlOrderSource;
use App\Data\UseCases\Clients\IndexClientUseCase;
use App\Data\UseCases\Clients\StoreClientUseCase;
use App\Data\UseCases\Clients\UpdateClientUseCase;
use App\Data\UseCases\Cars\StoreCarUseCase;
use App\Data\UseCases\Cars\ShowCarUseCase;
use App\Data\UseCases\Cars\IndexCarUseCase;
use App\Data\UseCases\Cars\UpdateCarUseCase;
use App\Data\UseCases\Users\StoreUserUseCase;
use App\Data\UseCases\Orders\CreateOrderUseCase;
use App\Data\UseCases\Orders\StoreOrderUseCase;
use App\Presentation\Controllers\Clients\IndexClientController;
use App\Presentation\Controllers\Clients\StoreClientController;
use App\Presentation\Controllers\Clients\UpdateClientController;
use App\Presentation\Controllers\Users\StoreUserController;
use App\Presentation\Controllers\Cars\StoreCarController;
use App\Presentation\Controllers\Cars\ShowCarController;
use App\Presentation\Controllers\Cars\CreateCarController;
use App\Presentation\Controllers\Cars\IndexCarController;
use App\Presentation\Controllers\Cars\UpdateCarController;
use App\Presentation\Controllers\Cars\EditCarController;
use App\Presentation\Controllers\Orders\StoreOrderController;
use App\Presentation\Controllers\Orders\CreateOrderController;

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
// CreateCarController
$createCarController = new CreateCarController();
// IndexCarUseCase and IndexCarController
$indexCarUseCase = new IndexCarUseCase($carRepository);
$indexCarController = new IndexCarController($indexCarUseCase);
// UpdateCarUseCase and UpdateCarController
$updateCarUseCase = new UpdateCarUseCase($carRepository);
$updateCarController = new UpdateCarController($updateCarUseCase);
// EditCarController
$editCarController = new EditCarController($showCarUseCase);

// Orders
// Source and Repository
$mySqlOrderSource = new MySqlOrderSource($pdo);
$orderRepository = new OrderRepository($mySqlOrderSource);
// StoreOrderUseCase and StoreOrderController
$storeOrderUseCase = new StoreOrderUseCase($orderRepository, $clientRepository, $carRepository);
$storeOrderController = new StoreOrderController($storeOrderUseCase);
// CreateOrderUseCase and CreateOrderController
$createOrderUseCase = new CreateOrderUseCase($clientRepository, $carRepository);
$createOrderController = new CreateOrderController($createOrderUseCase);

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
// change POST /cars to POST /cars/create to make getting the form page and posting it to the same path, only the method differs
$app->router->get("/cars", [$indexCarController, "execute"]); // with search functionality
$app->router->post("/cars/create", [$storeCarController, "execute"]);
$app->router->get("/cars/create", [$createCarController, "execute"]);
$app->router->get("/cars/{carId}/edit", [$editCarController, "execute"]);
$app->router->put("/cars/{carId}/edit", [$updateCarController, "execute"]); // ! this one works on postman, but not in the browser because there is no PUT method from html
$app->router->post("/cars/{carId}/edit", [$updateCarController, "execute"]); // ! this one can works on both, and doesn't have any collision with other paths, note the POST method
$app->router->get("/cars/{carId}", [$showCarController, "execute"]);

// Order routes
$app->router->get("/orders/create", [$createOrderController, "execute"]);
$app->router->post("/orders/create", [$storeOrderController, "execute"]);

$app->run();