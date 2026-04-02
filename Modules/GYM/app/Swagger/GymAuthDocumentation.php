<?php

namespace Modules\GYM\app\Swagger;

/**
 * @OA\Info(
 *     title="FitPaxPro Gym API",
 *     version="1.0.0",
 *     description="Gym module mobile authentication endpoints."
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Application Server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class GymAuthDocumentation
{
}
