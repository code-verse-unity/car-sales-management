<?php

namespace App\Data\UseCases\Clients;

use App\Core\Utils\Failures\ServerFailure;
use App\Domain\Repositories\ClientRepositoryInterface;
use App\Core\Utils\Failures\Failure;
use App\Domain\Repositories\OrderRepositoryInterface;

class ShowClientUseCase
{
  private ClientRepositoryInterface $clientRepository;
  private OrderRepositoryInterface $orderRepository;

  public function __construct(
    ClientRepositoryInterface $clientRepository,
    OrderRepositoryInterface $orderRepository
  ) {
    $this->clientRepository = $clientRepository;
    $this->orderRepository = $orderRepository;
  }

  public function execute(string $id)
  {
    try {
      $client = $this->clientRepository->findById($id);
      $orders = $this->orderRepository->findByClientId($id);

      $client->lock();
      $rawClient = $client->getRaw();

      $rawOrders = array_map(
        function ($order) {
          $order->lock();
          return $order->getRaw();
        },
        $orders
      );

      return [
        "client" => $rawClient,
        "orders" => $rawOrders
      ];
    } catch (\Throwable $th) {
      if ($th instanceof Failure) {
        return $th;
      } else {
        return new ServerFailure();
      }
    }
  }
}