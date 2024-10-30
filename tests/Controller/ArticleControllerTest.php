<?php

namespace App\Tests\Controller;

use App\Document\Article;
use App\Document\User;
use Doctrine\ODM\MongoDB\DocumentManager;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleControllerTest extends WebTestCase
{
    private KernelBrowser $client;
    private DocumentManager $documentManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        self::bootKernel();
        $this->documentManager = static::getContainer()->get(DocumentManager::class);
        $this->documentManager->getSchemaManager()->dropDatabases();
    }

    public function testIndex()
    {

        $author = new User();
        $author->setName('Abdeljabar Taoufikallah');
        $author->setEmail('abdeljabar@taoufikallah.com');
        $author->setPlainPassword('myPassword');
        $author->setPassword(hash('sha256', 'myPassword'));
        $this->documentManager->persist($author);

        $token = $this->getToken($author);

        $article = new Article();
        $article->setTitle('Title');
        $article->setContent('Content');
        $article->setAuthor($author);
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $this->client->request(
            'GET',
            '/articles',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf("Bearer %s", $token)
            ]
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent());
        $this->assertCount(1, $responseData);
    }

    public function testCreate()
    {
        $author = new User();
        $author->setName('Abdeljabar Taoufikallah');
        $author->setEmail('abdeljabar@taoufikallah.com');
        $author->setPlainPassword('myPassword');
        $author->setPassword(hash('sha256', 'myPassword'));
        $this->documentManager->persist($author);
        $this->documentManager->flush();

        $token = $this->getToken($author);

        $this->client->request(
            'POST',
            '/articles',
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf("Bearer %s", $token)
            ],
            json_encode([
                'title' => 'Article title',
                'content' => 'This is the content of the article',
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_CREATED);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Article title', $responseData['title']);
        $this->assertEquals('This is the content of the article', $responseData['content']);
    }

    public function testUpdate()
    {
        $author = new User();
        $author->setName('Abdeljabar Taoufikallah');
        $author->setEmail('abdeljabar@taoufikallah.com');
        $author->setPlainPassword('myPassword');
        $author->setPassword(hash('sha256', 'myPassword'));
        $this->documentManager->persist($author);
        $this->documentManager->flush();

        $article = new Article();
        $article->setTitle('Title');
        $article->setContent('Content');
        $article->setAuthor($author);
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $token = $this->getToken($author);

        $this->client->request(
            'PUT',
            sprintf("/articles/%s", $article->getId()),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf("Bearer %s", $token)
            ],
            json_encode([
                'title' => 'Updated article title',
            ]),
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $responseData = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertEquals('Updated article title', $responseData['title']);
    }

    public function testUpdateWithAnotherAuthor()
    {
        $author = new User();
        $author->setName('Abdeljabar Taoufikallah');
        $author->setEmail('abdeljabar@taoufikallah.com');
        $author->setPlainPassword('myPassword');
        $author->setPassword(hash('sha256', 'myPassword'));
        $this->documentManager->persist($author);
        $this->documentManager->flush();

        $otherAuthor = new User();
        $otherAuthor->setName('Abdeljabar Taoufikallah');
        $otherAuthor->setEmail('other@taoufikallah.com');
        $otherAuthor->setPlainPassword('myOtherPassword');
        $otherAuthor->setPassword(hash('sha256', 'myOtherPassword'));
        $this->documentManager->persist($otherAuthor);
        $this->documentManager->flush();

        $article = new Article();
        $article->setTitle('Title');
        $article->setContent('Content');
        $article->setAuthor($author);
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $token = $this->getToken($otherAuthor);

        $this->client->request(
            'PUT',
            sprintf("/articles/%s", $article->getId()),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf("Bearer %s", $token)
            ],
            json_encode([
                'title' => 'Updated article title',
            ]),
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    public function testDelete()
    {
        $author = new User();
        $author->setName('Abdeljabar Taoufikallah');
        $author->setEmail('abdeljabar@taoufikallah.com');
        $author->setPlainPassword('myPassword');
        $author->setPassword(hash('sha256', 'myPassword'));
        $this->documentManager->persist($author);
        $this->documentManager->flush();

        $article = new Article();
        $article->setTitle('Title');
        $article->setContent('Content');
        $article->setAuthor($author);
        $this->documentManager->persist($article);
        $this->documentManager->flush();

        $token = $this->getToken($author);

        $this->client->request(
            'DELETE',
            sprintf("/articles/%s", $article->getId()),
            [],
            [],
            [
                'CONTENT_TYPE' => 'application/json',
                'HTTP_Authorization' => sprintf("Bearer %s", $token)
            ],
        );

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param User $user
     * @return string
     * @throws \Exception
     */
    protected function getToken(User $user): string
    {
        return $this->getJwtManager()->create($user);
    }

    /**
     * @throws \Exception
     */
    protected function getJwtManager(): JWTTokenManagerInterface
    {
        return self::getContainer()->get(JWTTokenManagerInterface::class);
    }

    protected function tearDown(): void
    {
        $this->documentManager->clear();
        $this->documentManager->close();
        static::ensureKernelShutdown();
    }
}
