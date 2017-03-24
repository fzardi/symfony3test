<?php

require __DIR__.'/vendor/autoload.php';

$client = new GuzzleHttp\Client([
    'base_url' => 'http://127.0.0.1:8000',
    'defaults' => [
        'exceptions' => false
    ]
]);

$email = 'fzardi+'.rand(100,200).'@gmail.com';
$password = 'test';
$body = json_encode([
    'email' => $email,
    'plainPassword' => $password
]);

$response = $client->post('/api/users', [
    'body' => $body
]);

echo $response;
echo "\n\n";

$userUrl = $response->getHeader('Location');
$response = $client->get($userUrl);

echo $response;
echo "\n\n";