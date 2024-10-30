<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ErrorController extends AbstractController
{
    public function show(\Throwable $exception): Response
    {
        $data = [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ];

        $statusCode = method_exists($exception, 'getStatusCode') ? $exception->getStatusCode() : Response::HTTP_INTERNAL_SERVER_ERROR;
        return $this->json($data, $statusCode);
    }
}
