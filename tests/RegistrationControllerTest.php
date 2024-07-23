<?php

namespace App\Tests;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\TestCase;

class RegistrationControllerTest extends TestCase
{

    /**
     * @throws GuzzleException
     */
    public function testRegistration()
    {
        $client = new Client([
            'base_url' => 'http://localhost:2280',
        ]);
        $response = $client->request('GET', '/api/registration');
        $this->assertEquals(200, $response->getStatusCode());
    }

}
