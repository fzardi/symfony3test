<?php

namespace AppBundle\Controller\Api;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class APIUsersControllerTest extends TestCase
{
    private static $staticClient;
    /**
     * @var Client
     */
    protected $client;

    public static function setUpBeforeClass()
    {
        self::$staticClient = new Client([
            'base_url' => 'http://localhost:8000',
            'defaults' => [
                'exceptions' => false
            ]
        ]);
    }

    protected function setUp()
    {
        $this->client = self::$staticClient;
    }

    public function test_deleteAction()
    {
        $response = $this->client->delete('/api/users/fzardi+666@gmail.com');

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
    }

    public function test_newAction()
    {
        $email = 'fzardi+666@gmail.com';
        $password = 'test';
        $body = json_encode([
            'email' => $email,
            'plainPassword' => $password
        ]);

        $response = $this->client->post('/api/users', [
            'body' => $body
        ]);

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue($response->hasHeader('Location'));
        $this->assertEquals(
            sprintf(
                '/api/users/%s',
                $email
            ),
            $response->getHeader('Location')
        );
    }

    public function test_showAction()
    {
        $response = $this->client->get('/api/users/fzardi+666@gmail.com');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
