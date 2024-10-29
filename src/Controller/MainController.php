<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'main', methods: ['GET'])]
    public function index(): JsonResponse
    {
        return $this->json([
            'articles' => '/articles',
            'userProfile' => '/me',
            'userProfileUpdate' => '/update-profile',
        ]);
    }
}