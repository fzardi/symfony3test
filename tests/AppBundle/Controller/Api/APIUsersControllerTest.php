<?php

namespace AppBundle\Controller\Api;

use Symfony\Component\HttpFoundation\Response;
use Tests\lib\APITestCase;

class APIUsersControllerTest extends APITestCase
{
//    public function test_deleteAction()
//    {
//        $response = $this->client->delete('/api/users/fzardi+666@gmail.com');
//
//        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
//    }

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
            $response->getHeader('Location')[0]
        );

        $response = $this->client->get('/api/users/fzardi+666@gmail.com');

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }
}
