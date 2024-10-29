<?php

namespace App\Controller;

use App\Document\Article;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\LockException;
use Doctrine\ODM\MongoDB\Mapping\MappingException;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ArticleController extends AbstractController
{
    public function __construct(
        private readonly DocumentManager $documentManager
    ) {}

    #[Route('/articles', name: 'article_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $articles = $this->documentManager->getRepository(Article::class)->findAll();
        return $this->json($articles, Response::HTTP_OK, [], ['groups' => ['article:read']]);
    }

    /**
     * @throws MappingException
     * @throws \Throwable
     * @throws MongoDBException
     * @throws LockException
     */
    #[Route('/articles', name: 'article_create', methods: ['POST'])]
    public function create(Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        /** @var Article $article */
        $article = $serializer->deserialize(
            $request->getContent(),
            Article::class,
            'json',
            ['groups' => ['article:write']]
        );

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $author */
        $author = $this->getUser();

        $article->setAuthor($author);
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        return $this->json($article, Response::HTTP_CREATED, [], ['groups' => ['article:read']]);
    }

    /**
     * @throws MappingException
     * @throws \Throwable
     * @throws MongoDBException
     * @throws LockException
     */
    #[Route('/articles/{id}', name: 'article_update', methods: ['PUT'])]
    public function update(Article $article, Request $request, SerializerInterface $serializer, ValidatorInterface $validator): Response
    {
        $serializer->deserialize(
            $request->getContent(),
            Article::class,
            'json',
            [
                'object_to_populate' => $article,
                'groups' => ['article:update']
            ]
        );

        $errors = $validator->validate($article);
        if (count($errors) > 0) {
            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        /** @var User $author */
        $author = $this->getUser();

        if ($author->getId() !== $article->getAuthor()->getId()) {
            throw new AccessDeniedHttpException('You cannot edit this article');
        }

        $this->documentManager->persist($article);
        $this->documentManager->flush();

        return $this->json($article, Response::HTTP_CREATED, [], ['groups' => ['article:read']]);
    }

    /**
     * @throws MappingException
     * @throws \Throwable
     * @throws MongoDBException
     * @throws LockException
     */
    #[Route('/articles/{id}', name: 'article_delete', methods: ['DELETE'])]
    public function delete(Article $article): Response
    {
        /** @var User $author */
        $author = $this->getUser();

        if ($author->getId() !== $article->getAuthor()->getId()) {
            throw new AccessDeniedHttpException('You cannot delete this article');
        }

        $this->documentManager->remove($article);
        $this->documentManager->flush();

        return $this->json([], Response::HTTP_NO_CONTENT);
    }
}
