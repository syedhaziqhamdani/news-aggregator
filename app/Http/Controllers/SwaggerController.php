<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="News Aggregator API",
 *     description="This is the API documentation for the News Aggregator application, providing endpoints for articles, authentication, and user preferences."
 * )
 *
 * @OA\Server(
 *     url="http://localhost/api",
 *     description="Local development server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT",
 *     description="Enter the token in the format: Bearer {token}"
 * )
 */
class SwaggerController
{
    // This class serves only as a container for Swagger annotations
}
