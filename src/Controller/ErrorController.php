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

        return $this->json($data, $exception->getStatusCode());
    }
}
