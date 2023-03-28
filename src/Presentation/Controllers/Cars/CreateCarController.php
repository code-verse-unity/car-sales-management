<?php

namespace App\Presentation\Controllers\Cars;

use App\Core\Requests\Request;
use App\Core\Responses\Response;

class CreateCarController
{
    public function execute(Request $request, Response $response)
    {
        $response->renderView("createCarView");
    }
}