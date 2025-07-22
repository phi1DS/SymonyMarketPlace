<?php 

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    /** @var AbstractDatabaseTool */
    protected $databaseTool;
    protected $client;

    public function setUp(): void
    {
        $test ='';

        parent::setUp();

        self::ensureKernelShutdown(); // Just in case

        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testCreateUserSuccess(): void
    {
        $this->databaseTool->loadFixtures([]); // 💥 Purges DB, no fixtures

        $this->client->request('POST', '/user', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'address' => '123 Main St',
        ]));

        $this->assertResponseStatusCodeSame(201);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('message', $responseData);
        $this->assertEquals('User created', $responseData['message']);

        $this->assertArrayHasKey('user', $responseData);
        $this->assertEquals('john@example.com', $responseData['user']['email']);
        $this->assertEquals('John Doe', $responseData['user']['name']);
        $this->assertArrayHasKey('id', $responseData['user']);
    }

    public function testCreateUserValidationFail(): void
    {
        $this->databaseTool->loadFixtures([]); // 💥 Purges DB, no fixtures

        // Missing email field
        $this->client->request('POST', '/user', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'John Doe',
            // 'email' missing
            'address' => '123 Main St',
        ]));

        $this->assertResponseStatusCodeSame(400);
        $responseData = json_decode($this->client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('errors', $responseData);
        $this->assertStringContainsString('email', $responseData['errors']);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->databaseTool);
    }
}
