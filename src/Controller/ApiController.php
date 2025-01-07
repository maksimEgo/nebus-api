<?php

namespace App\Controller;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    description: "Документация для API по управлению организациями, зданиями и видами деятельности.",
    title: "Organization API"
)]
#[OA\Server(
    url: "http://localhost:8000",
    description: "Локальный сервер"
)]
class ApiController
{

}