<?php

namespace App\Controller;

use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $documentManager
    ) {
    }

    /**
     * @return JsonResponse
     */
    #[Route('/users', name: 'user_index', methods: ['GET'])]
    public function index(): JsonResponse
    {
        $userRepository = $this->documentManager->getRepository(User::class);
        $users = $userRepository->findAll();
        return $this->json(
            ['users' => $users],
            Response::HTTP_OK,
            [],
            ['groups' => ['user:read']]
        );
    }

    /**
     * @throws MappingException
     * @throws \Throwable
     * @throws MongoDBException
     * @throws LockException
     */
    #[Route('/users', name: 'user_create', methods: ['POST'])]
    public function createUser(
        Request $request,
        SerializerInterface $serializer,
        ValidatorInterface $validator
    ): Response
    {
        $data = $request->getContent();
        $user = $serializer->deserialize($data, User::class, 'json');

        $errors = $validator->validate($user);

        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $this->documentManager->persist($user);
        $this->documentManager->flush();

        return $this->json($user, Response::HTTP_CREATED, [], ['groups' => ['user:read']]);
    }
}
