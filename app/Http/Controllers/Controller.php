<?php

namespace App\Http\Controllers;



/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         title="Blaaize Wallet API",
 *         version="1.0.0",
 *         description="This is the API documentation for Blaaize Wallet."
 *     )
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
abstract class Controller
{
    //
}
