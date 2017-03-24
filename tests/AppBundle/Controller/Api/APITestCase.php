<?php

namespace AppBundle\Controller\Api;

use GuzzleHttp\Client;
use PHPUnit\Framework\TestCase;

class APITestCase extends TestCase
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
}