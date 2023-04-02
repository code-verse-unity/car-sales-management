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
use App\Data\UseCases\Orders\IndexOrderUseCase;
use App\Data\UseCases\Orders\ShowOrderUseCase;
use App\Data\UseCases\Orders\DestroyOrderUseCase;
use App\Data\UseCases\Bills\DownloadBillUseCase;
use App\Presentation\Controllers\Clients\IndexClientController;
use App\Presentation\Controllers\Clients\StoreClientController;
use App\Presentation\Controllers\Clients\UpdateClientController;
use App\Presentation\Controllers\HomeController;
use App\Presentation\Controllers\Users\StoreUserController;
use App\Presentation\Controllers\Cars\StoreCarController;
use App\Presentation\Controllers\Cars\ShowCarController;
use App\Presentation\Controllers\Cars\CreateCarController;
use App\Presentation\Controllers\Cars\IndexCarController;
use App\Presentation\Controllers\Cars\UpdateCarController;
use App\Presentation\Controllers\Cars\EditCarController;
use App\Presentation\Controllers\Clients\CreateCarController as ClientsCreateCarController;
use App\Presentation\Controllers\Clients\CreateClientController;
use App\Presentation\Controllers\Orders\StoreOrderController;
use App\Presentation\Controllers\Orders\CreateOrderController;
use App\Presentation\Controllers\Orders\IndexOrderController;
use App\Presentation\Controllers\Orders\ShowOrderController;
use App\Presentation\Controllers\Orders\DestroyOrderController;
use App\Presentation\Controllers\Bills\DownloadBillController;

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
// Create a new client view
$createClientController = new CreateClientController();

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
// IndexOrderUseCase and IndexOrderController
$indexOrderUseCase = new IndexOrderUseCase($orderRepository);
$indexOrderController = new IndexOrderController($indexOrderUseCase);
// ShowOrderUseCase and ShowOrderController
$showOrderUseCase = new ShowOrderUseCase($orderRepository);
$showOrderController = new ShowOrderController($showOrderUseCase);
// DestroyOrderUseCase and DestroyOrderController
$destroyOrderUseCase = new DestroyOrderUseCase($orderRepository);
$destroyOrderController = new DestroyOrderController($destroyOrderUseCase);

// Bills
$downloadBillUseCase = new DownloadBillUseCase($orderRepository);
$downloadBillController = new DownloadBillController($downloadBillUseCase);
$homeController = new HomeController();

$app = new Application();

$app->router->post("/users", [$storeUserController, "execute"]);


/*
This is the path to update a client,
The clientId is automatically in $request->params.
So there is no need to add id in the body of the request, it won't be used
*/
$app->router->get('/', [$homeController, 'execute']);
// List all clients
$app->router->get("/clients", [$indexClientController, "execute"]);
// Show the view to create a new client
$app->router->get("/clients/add", [$createClientController, "execute"]);
// Update a client
$app->router->put("/clients/{clientId}", [$updateClientController, "execute"]);
// Store a new client
$app->router->post("/clients", [$storeClientController, "execute"]);

// Car routes
// change POST /cars to POST /cars/create to make getting the form page and posting it to the same path, only the method differs
$app->router->get("/cars", [$indexCarController, "execute"]); // with search functionality
$app->router->get("/cars/add", [$createCarController, "execute"]);
$app->router->post("/cars", [$storeCarController, "execute"]);
$app->router->get("/cars/{carId}/edit", [$editCarController, "execute"]);
$app->router->put("/cars/{carId}/edit", [$updateCarController, "execute"]); // ! this one works on postman, but not in the browser because there is no PUT method from html
$app->router->post("/cars/{carId}/edit", [$updateCarController, "execute"]); // ! this one can works on both, and doesn't have any collision with other paths, note the POST method
$app->router->get("/cars/{carId}", [$showCarController, "execute"]);

// Order routes
$app->router->get("/orders", [$indexOrderController, "execute"]);
$app->router->get("/orders/create", [$createOrderController, "execute"]);
$app->router->post("/orders/create", [$storeOrderController, "execute"]);
$app->router->post("/orders/{orderId}/delete", [$destroyOrderController, "execute"]);
$app->router->delete("/orders/{orderId}/delete", [$destroyOrderController, "execute"]);
$app->router->get("/orders/{orderId}", [$showOrderController, "execute"]);

// Bills routes
$app->router->get("/bills/{orderId}/download", [$downloadBillController, "execute"]);

$app->run();
