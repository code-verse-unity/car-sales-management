<?php

namespace App\Presentation\Controllers\Clients;

use App\Core\Requests\Request;
use App\Core\Responses\Response;

class CreateClientController
{
  public function execute(Request $request, Response $response)
  {
    $response->renderView("clients/create");
  }
}
