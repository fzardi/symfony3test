<?php

namespace AppBundle\Controller\Api;

use AppBundle\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Tests\lib\APITestCase;

class APIUsersControllerTest extends APITestCase
{
    private $testUserEmail = 'fzardi+666@gmail.com';

    public function test_newAction()
    {
        $password = 'test';
        $body = json_encode([
            'email' => $this->testUserEmail,
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
                $this->testUserEmail
            ),
            $response->getHeader('Location')[0]
        );
    }

    public function test_showAction()
    {
        $this->createUser(array(
            'email' => $this->testUserEmail
        ));
        $response = $this->client->get('/app_test.php/api/users/'.$this->testUserEmail);
        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $data = json_decode($response->getBody(), true);
        $this->assertEquals(array(
            'email'
        ), array_keys($data));
    }

    protected function createUser(array $data)
    {
        $data = array_merge(array(
            'plainPassword' => 'test',
            'roles' => ['ROLE_ADMIN']
        ), $data);
        $accessor = PropertyAccess::createPropertyAccessor();
        $user = new User();
        foreach ($data as $key => $value) {
            $accessor->setValue($user, $key, $value);
        }

        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();
        return $user;
    }
}
