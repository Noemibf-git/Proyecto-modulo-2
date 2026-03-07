<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(title: 'API Recetas', version: '1.0.0', description: 'API REST para gestión de recetas de cocina')]
#[OA\SecurityScheme(securityScheme: 'bearerAuth', type: 'http', scheme: 'bearer')]
abstract class Controller
{
    //
}
