<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *      version="1.0.0",
 *      title="Nostalgia API",
 *      description="API documentation for the Nostalgia project",
 *      @OA\Contact(
 *          email="admin@example.com"
 *      )
 * )
 * @OA\SecurityScheme(
 *      securityScheme="sanctum",
 *      type="http",
 *      scheme="bearer",
 *      bearerFormat="JWT",
 * )
 */
abstract class Controller
{
    //
}

/**
 * @OA\Get(
 *     path="/api/user",
 *     operationId="getAuthenticatedUser",
 *     tags={"Authentication"},
 *     summary="Get the authenticated user",
 *     description="Returns the authenticated user's data",
 *     security={{"sanctum":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful operation",
 *         @OA\JsonContent(type="object")
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Unauthenticated"
 *     )
 * )
 */
